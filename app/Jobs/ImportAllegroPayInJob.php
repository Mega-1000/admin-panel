<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Entities\Payment;
use App\Entities\Transaction;
use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\OrderPayments;
use App\Repositories\TransactionRepository;
use App\Services\FindOrCreatePaymentForPackageService;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class ImportAllegroPayInJob
 * @package App\Jobs
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
final class ImportAllegroPayInJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CHAR_TO_REMOVE = [
        "\xEF\xBB\xBF" => '',
        '"' => '',
        'ę' => 'e',
        'ć' => 'c',
        'ą' => 'a',
        'ń' => 'n',
        'ł' => 'l',
        'ś' => 's',
        'Ł' => 'L',
        'Ż' => 'Z',
    ];

    /**
     * @var TransactionRepository
     */
    protected readonly TransactionRepository $transactionRepository;

    /**
     * @var FindOrCreatePaymentForPackageService
     */
    protected FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService;

    public function __construct(
        protected readonly UploadedFile $file
    ) {}

    /**
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function handle(TransactionRepository $transaction, FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService)
    {
        $this->findOrCreatePaymentForPackageService = $findOrCreatePaymentForPackageService;
        $header = NULL;
        $fileName = 'transactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $this->transactionRepository = $transaction;
        $data = array();

        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 3000, ',')) !== FALSE) {
                if (!$header) {
                    foreach ($row as &$headerName) {
                        $headerName = Str::snake(strtr($headerName, self::CHAR_TO_REMOVE));
                    }
                    $header = $row;
                    fputcsv($file, $row);
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        $data = array_reverse($data);
        foreach ($data as $payIn) {
            if (!in_array($payIn['operacja'], ['wpłata', 'zwrot', 'dopłata'])) {
                continue;
            }

            $order = Order::where('allegro_payment_id', '=', $payIn['identyfikator'])->first();

            try {
                if (!empty($order)) {
                    $this->findOrCreatePaymentForPackageService->execute(
                        OrderPackage::where('order_id', $order->id)->first(),
                    );

                    $this->settleOrder($order, $payIn);
                } else {
                    fputcsv($file, $payIn);
                }
            } catch (Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage(), ['line' => __LINE__]);
            }
        }

        fclose($file);
        Storage::disk('local')->put('public/transaction/TransactionWithoutOrders' . date('Y-m-d') . '.csv', file_get_contents($fileName));
    }

    /**
     * Save new transaction
     *
     * @param Order $order Order object
     * @param array $data Additional data
     * @return Transaction|null
     *
     * @throws Exception
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransaction(Order $order, array $data): ?Transaction
    {
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', 'w-' . $data['identyfikator'])->first();
        if ($existingTransaction !== null) {
            if ($data['operacja'] == 'zwrot') {
                $paymentsToReturn = $order->payments()->where('amount', '=', $data['kwota'])->whereNull('deleted_at')->first();
                if (!empty($paymentsToReturn)) {
                    $paymentsToReturn->delete();
                }
            } else {
                return null;
            }
        }
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new DateTime(),
            'posted_in_bank_date' => new DateTime($data['data']),
            'payment_id' => (($data['operacja'] === 'zwrot') ? 'z' : 'w-') . $data['identyfikator'],
            'kind_of_operation' => $data['operacja'],
            'order_id' => $order->id,
            'operator' => $data['operator'],
            'operation_value' => preg_replace('/[^.\d]/', '', $data['kwota']),
            'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data['kwota'],
            'accounting_notes' => '',
            'transaction_notes' => '',
            'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL
        ]);
    }

    /**
     * Calculate balance
     *
     * @param integer $customerId Customer id
     * @return float
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getCustomerBalance(int $customerId): float
    {
        if (!empty($lastCustomerTransaction = $this->transactionRepository->findWhere([
            ['customer_id', '=', $customerId]
        ])->last())) {
            return $lastCustomerTransaction->balance;
        }

        return 0;
    }

    /**
     * Settle promise.
     *
     * @param Order $order Order object.
     * @param array $payIn Pay in row.
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function settlePromisePayments(Order $order, array $payIn): void
    {
        foreach ($order->payments()->where('promise', '=', '1')->whereNull('deleted_at')->get() as $payment) {
            if ($payIn['kwota'] === (float)$payment->amount) {
                $payment->delete();
                continue;
            }

            $preventionArray = [];
            AddLabelService::addLabels($order, [128], $preventionArray, [], Auth::user()?->id);
        }
    }

    /**
     * Return related Orders
     *
     * @param Order $order Order object.
     * @return Collection
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getRelatedOrders(Order $order): Collection
    {
        return Order::where('master_order_id', '=', $order->id)->orWhere('id', '=', $order->id)->get();
    }

    /**
     * Settle orders.
     *
     * @param Order $order
     * @param $payIn
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function settleOrder(Order $order, $payIn): void
    {
        $payIn['kwota'] = explode(" ", $payIn['kwota'])[0];

        $declaredSum = OrderPayments::getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn) >= 1;
        OrderPayments::updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn);

        $existingPayment = $order->payments()->where('amount', $payIn['kwota'])->first();

        $order->payments()->create([
            'amount' => $payIn['kwota'],
            'type' => 'CLIENT',
            'promise' => '',
            'external_payment_id' => $payIn['identyfikator'],
            'payer' => $order->customer->login,
            'operation_date' => Carbon::parse($payIn['data']),
            'comments' => implode(' ', $payIn),
            'operation_type' => 'wplata/wyplata allegro',
            'status' => $declaredSum ? 'Rozliczająca deklarowaną' : null,
        ]);
    }

    /**
     * @param Order $order
     * @return float
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getTotalOrderValue(Order $order): float
    {
        $additional_service = $order->additional_service_cost ?? 0;
        $additional_cod_cost = $order->additional_cash_on_delivery_cost ?? 0;
        $shipment_price_client = $order->shipment_price_for_client ?? 0;
        $totalProductPrice = 0;

        foreach ($order->items as $item) {
            $price = $item->gross_selling_price_commercial_unit ?: $item->net_selling_price_commercial_unit ?: 0;
            $quantity = $item->quantity ?? 0;
            $totalProductPrice += $price * $quantity;
        }

        return round($totalProductPrice + $additional_service + $additional_cod_cost + $shipment_price_client, 2);
    }

    /**
     * Tworzy transakcje przeksięgowania
     *
     * @param Order $order
     * @param Transaction $transaction
     * @param float $amount
     * @return Transaction
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransfer(Order $order, Transaction $transaction, float $amount): Transaction
    {
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new DateTime(),
            'payment_id' => str_replace('w-', 'p-', $transaction->payment_id) . '-' . $transaction->posted_in_system_date->format('Y-m-d H:i:s'),
            'kind_of_operation' => 'przeksięgowanie',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => -$amount,
            'balance' => (float)$this->getCustomerBalance($order->customer_id) - $amount,
            'accounting_notes' => '',
            'transaction_notes' => '',
            'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL,
        ]);
    }
}

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
     * Calculate balance
     *
     * @param integer $customerId Customer id
     * @return float
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
     * Settle orders.
     *
     * @param Order $order
     * @param $payIn
     */
    private function settleOrder(Order $order, $payIn): void
    {
        $payIn['kwota'] = explode(" ", $payIn['kwota'])[0];

        $declaredSum = OrderPayments::getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn) >= 1;
        OrderPayments::updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn);

        $payment = Payment::where('order_id', $order->id)->where('amount', $payIn['kwota'])->first();

        if (empty($payment)) {
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
    }
}

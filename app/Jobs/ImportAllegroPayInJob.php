<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Transaction;
use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\TransactionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImportAllegroPayInJob
 * @package App\Jobs
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
class ImportAllegroPayInJob implements ShouldQueue
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
    protected $transactionRepository;

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * ImportAllegroPayInJob constructor.
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function handle(TransactionRepository $transaction)
    {
        $header = NULL;
        $fileName = 'transactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $this->transactionRepository = $transaction;
        $data = array();
        $i = 0;
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 3000, ',')) !== FALSE) {
                if (!$header) {
                    foreach ($row as &$headerName) {
                        $headerName = snake_case(strtr($headerName, self::CHAR_TO_REMOVE));
                    }
                    $header = $row;
                    fputcsv($file, $row);
                } else {
                    $data[] = array_combine($header, $row);
                    $i++;
                }
            }
            fclose($handle);
        }

        $data = array_reverse($data);
        foreach ($data as $payIn) {
            if (!in_array($payIn['operacja'], ['wpłata', 'zwrot'])) {
                continue;
            }
            $order = Order::where('allegro_payment_id', '=', $payIn['identyfikator'])->first();
            try {
                if (!empty($order)) {
                    $transaction = $this->saveTransaction($order, $payIn);
                    if ($transaction === null) {
                        continue;
                    }
                    if ($payIn['operacja'] === 'wpłata') {
                        $this->settlePromisePayments($order, $payIn);
                        $orders = $this->getRelatedOrders($order);
                        $this->settleOrders($orders, $transaction);
                    }
                } else {
                    fputcsv($file, $payIn);
                }
            } catch (\Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage(), ['line' => __LINE__]);
            }
        }
        fclose($file);
        Storage::disk('local')->put('public/transaction/TransactionWithoutOrders' . date('Y-m-d') . '.csv', file_get_contents($fileName));
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
     * @param Collection $orders Orders collection
     * @param Transaction $transaction Transaction.
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function settleOrders(Collection $orders, Transaction $transaction): void
    {
        $amount = $transaction->operation_value;
        foreach ($orders as $order) {
            $orderBookedPaymentSum = $order->bookedPaymentsSum();
            $amountOutstanding = $this->getTotalOrderValue($order) - $orderBookedPaymentSum;
            if ($amount == 0 || $amountOutstanding == 0) {
                continue;
            }
            if ($amountOutstanding > 0) {
                if (bccomp($amount, $amountOutstanding, 3) >= 0) {
                    $paymentAmount = $amountOutstanding;
                } else {
                    $paymentAmount = $amount;
                }
                $transfer = $this->saveTransfer($order, $transaction, (float)$paymentAmount);
                $payment = $order->payments()->create([
                    'transaction_id' => $transfer->id,
                    'amount' => $paymentAmount,
                    'type' => 'CLIENT',
                    'promise' => '',
                ]);

                if ($payment instanceof OrderPayment) {
                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);
                }

                $amount -= $paymentAmount;
            }
        }
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
            } else {
                dispatch_now(new AddLabelJob($order->id, [128]));
            }
        }
    }

    /**
     * Save new transaction
     *
     * @param Order $order Order object
     * @param array $data Additional data
     * @return Transaction
     *
     * @throws \Exception
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
                $this->saveRefund($order, $existingTransaction, (float)$data['kwota']);
            } else {
                return null;
            }
        }
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'posted_in_bank_date' => new \DateTime($data['data']),
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
        } else {
            return 0;
        }
    }

    /**
     * Tworzy transakcje przeksięgowania
     *
     * @param Order $order
     * @param Transaction $transaction
     * @return Transaction
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransfer(Order $order, Transaction $transaction, float $amount): Transaction
    {
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
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


    /**
     * Tworzy transakcje przeksięgowania
     *
     * @param Order $order
     * @param Transaction $transaction
     * @return Transaction
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveRefund(Order $order, Transaction $transaction, float $amount): Transaction
    {
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'payment_id' => str_replace('w-', 'z-', $transaction->payment_id) . '-' . $transaction->posted_in_system_date,
            'kind_of_operation' => 'przeksięgowanie zwrotne',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => abs($amount),
            'balance' => (float)$this->getCustomerBalance($order->customer_id) + abs($amount),
            'accounting_notes' => '',
            'transaction_notes' => '',
            'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL,
        ]);
    }
}

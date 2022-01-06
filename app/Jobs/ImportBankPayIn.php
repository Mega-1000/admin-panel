<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\Transaction;
use App\Helpers\PdfCharactersHelper;
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

class ImportBankPayIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Transaction
     */
    protected $transactionRepository;

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * ImportBankPayIn constructor.
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @param TransactionRepository $transaction
     * @return void
     */
    public function handle(TransactionRepository $transaction): void
    {
        $header = NULL;
        $fileName = 'bankTransactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $this->transactionRepository = $transaction;
        $data = array();
        $i = 0;
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 5000, ';')) !== FALSE) {
                if (count(array_filter($row)) < 5) {
                    continue;
                }

                if (!$header) {
                    foreach ($row as &$headerName) {
                        if (!empty($headerName)) {
                            $headerName = str_replace('#', '', iconv('ISO-8859-2', 'UTF-8', $headerName));
                            $headerName = snake_case(PdfCharactersHelper::changePolishCharactersToNonAccented($headerName));
                        }
                    }
                    $header = $row;
                    fputcsv($file, $row);
                } else {
                    if (!str_contains($row[2], 'PRZYCHODZ')) {
                        continue;
                    }
                    foreach ($row as &$text) {
                        $text = iconv('ISO-8859-2', 'UTF-8', $text);
                    }
                    $data[] = array_combine($header, $row);
                    $i++;
                }
            }
            fclose($handle);
        }

        foreach ($data as $payIn) {
            $orderId = $this->checkOrderNumberFromTitle($payIn['tytul']);
            if ($orderId == null) {
                continue;
            }
            $order = Order::find($orderId);

            try {
                if (!empty($order)) {
                    $payIn['kwota'] = (float)str_replace(',', '.', preg_replace('/[^.,\d]/', '', $payIn['kwota']));
                    $transaction = $this->saveTransaction($order, $payIn);
                    if ($transaction === null) {
                        continue;
                    }
                    $this->settlePromisePayments($order, $payIn);
                    $orders = $this->getRelatedOrders($order);

                    $this->settleOrders($orders, $transaction);
                } else {
                    fputcsv($file, $payIn);
                }
            } catch (\Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            }
        }
        fclose($file);
        Storage::disk('local')->put('public/transaction/bankTransactionWithoutOrder' . date('Y-m-d') . '.csv', file_get_contents($fileName));
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
     * @param Collection  $orders Orders collection
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
                $transfer = $this->saveTransfer($order, $transaction, $paymentAmount);
                $order->payments()->create([
                    'transaction_id' => $transfer->id,
                    'amount' => $paymentAmount,
                    'type' => 'CLIENT',
                    'promise' => '',
                ]);
                $amount -= $paymentAmount;
            }
        }
    }

    private function getTotalOrderValue(Order $order) : float
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
     * Search order number.
     *
     * @param string $fileLine Line in csv file.
     * @return integer|null
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function checkOrderNumberFromTitle(string $fileLine): ?int
    {
        $matches = $vatPregMatch = [];
        $transactions = str_replace(' ', '', $fileLine);
        preg_match('/[qQ][qQ](\d{3,5})[qQ][qQ]/', $transactions, $matches);
        if (count($matches)) {
            preg_match('/VAT/', $transactions, $vatPregMatch);
            if (count($vatPregMatch) > 0) {
                $orderId = null;
            } else {
                $orderId = $matches[1];
            }
        } else {
            $orderId = null;
        }

        return $orderId;
    }


    /**
     * Save new transaction
     *
     * @param Order $order Order object
     * @param array $data Additional data
     * @return ?Transaction
     *
     * @throws \Exception
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransaction(Order $order, array $data): ?Transaction
    {
        $identifier = 'w-' . strtotime($data['data_ksiegowania']) . '-' . $order->id;
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', $identifier)->first();
        if ($existingTransaction !== null) {
            return null;
        }

        try {
            return $this->transactionRepository->create([
                'customer_id' => $order->customer_id,
                'posted_in_system_date' => new \DateTime(),
                'posted_in_bank_date' => new \DateTime($data['data_ksiegowania']),
                'payment_id' => $identifier,
                'kind_of_operation' => 'przelew przychodzący',
                'order_id' => $order->id,
                'operator' => 'BANK',
                'operation_value' => $data['kwota'],
                'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data['kwota'],
                'accounting_notes' => '',
                'transaction_notes' => '',
            ]);
        } catch (\Exception $exception) {
            Log::notice('Błąd podczas zapisu transakcji: ' . $exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            return null;
        }
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
     * @param Order       $order
     * @param Transaction $transaction
     * @param float       $amount
     * @param boolean     $back
     * @return Transaction
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransfer(Order $order, Transaction $transaction, float $amount, bool $back = false): ?Transaction
    {
        $identifier = 'p-' . strtotime($transaction->posted_in_bank_date->format('Y-m-d H:i:s')) . '-' . $order->id;
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', $identifier)->first();
        if ($existingTransaction !== null) {
            return null;
        }
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'payment_id' => $identifier,
            'kind_of_operation' => 'przeksięgowanie',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => $amount,
            'balance' => (float)$this->getCustomerBalance($order->customer_id) - (float)$amount,
            'accounting_notes' => '',
            'transaction_notes' => '',
        ]);
    }
}

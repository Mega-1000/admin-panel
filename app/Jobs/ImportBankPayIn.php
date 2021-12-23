<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\SelTransaction;
use App\Entities\Transaction;
use App\Helpers\PdfCharactersHelper;
use App\Repositories\TransactionRepository;
use Illuminate\Bus\Queueable;
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
     * @return void
     */
    public function handle(TransactionRepository $transaction)
    {
        $header = NULL;
        $fileName = 'bankTransactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $this->transactionRepository = $transaction;
        $data = array();
        $i = 0;
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 3000, ';')) !== FALSE) {
                if (count($row) < 5) {
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
                    foreach ($row as &$text) {
                        $text = iconv('ISO-8859-2', 'UTF-8', $text);
                    }
                    $data[] = array_combine($header, $row);
                    $i++;
                }
            }
            fclose($handle);
        }

        $data = array_reverse($data);
        foreach ($data as $payIn) {
            if ($payIn['opis_operacji'] === '' || $payIn['tytul'] === '') {
                continue;
            }
            $orderId = $this->checkOrderNumberFromTitle($payIn['tytul']);
            if ($orderId == null) {
                continue;
            }
            $order = Order::find($orderId);

            try {
                if (!empty($order)) {
                    $transaction = $this->saveTransaction($order, $payIn);
                    if ($transaction !== null && $order->payments->count()) {
                        /** @var OrderPayment $payment */
                        foreach ($order->payments as $payment) {
                            $amount = preg_replace('/[^.\d]/', '', $payIn['kwota']);
                            if ($payment->promise === '1' && $payment->amount == $amount) {
                                $payment->delete();
                                $order->payments()->create([
                                    'transaction_id' => $transaction->id,
                                    'amount' => $amount,
                                    'type' => 'CLIENT',
                                    'promise' => '',
                                ]);

                                $this->saveTransfer($order, $transaction);
                            }
                        }
                    }
                } else {
                    fputcsv($file, $payIn);
                }
            } catch (\Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            }
        }
        fclose($file);
        Storage::disk('local')->put('public/transaction/TransactionWithoutOrders' . date('Y-m-d') . '.csv', file_get_contents($fileName));

    }


    private function checkOrderNumberFromTitle(string $fileLine)
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
     * @return Transaction
     *
     * @throws \Exception
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransaction(Order $order, array $data): ?Transaction
    {
        $identyfikator = 'p' . strtotime($data['data_ksiegowania']) . '-' . $order->id;
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=',$identyfikator)->first();
        if ($existingTransaction !== null) {
            return null;
        }
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'posted_in_bank_date' => new \DateTime($data['data_ksiegowania']),
            'payment_id' => $identyfikator,
            'kind_of_operation' => 'przelew przychodzący',
            'order_id' => $order->id,
            'operator' => 'BANK',
            'operation_value' => $data['kwota'],
            'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data['kwota'],
            'accounting_notes' => '',
            'transaction_notes' => '',
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
     * @param Order       $order
     * @param Transaction $transaction
     * @return Transaction
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransfer(Order $order, Transaction $transaction): Transaction
    {
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'payment_id' => 'p-' . $transaction->payment_id . '-' . $transaction->posted_in_system_date->format('Y-m-d H:i:s'),
            'kind_of_operation' => 'przeksięgowanie',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => $transaction->operation_value,
            'balance' => (float)$this->getCustomerBalance($order->customer_id) - (float)$transaction->operation_value,
            'accounting_notes' => '',
            'transaction_notes' => '',
        ]);
    }
}

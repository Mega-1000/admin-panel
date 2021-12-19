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

/**
 * Class ImportAllegroPayInJob
 * @package App\Jobs
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
class ImportAllegroPayInJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Transaction
     */
    protected $transaction;

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

        $this->transaction = $transaction;
        $data = array();
        $i = 0;
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$header) {
                    foreach ($row as &$headerName) {
                        $headerName = snake_case(PdfCharactersHelper::changePolishCharactersToNonAccented($headerName));
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
            if ($payIn['operacja'] === 'zwrot') {
                continue;
            }
            /** @var SelTransaction $selTransaction */
            $selTransaction = SelTransaction::where('tr_CheckoutFormPaymentId', '=', $payIn['identyfikator'])->first();
            try {
                if (!empty($selTransaction->order)) {
                    $transaction = $this->saveTransaction($selTransaction->order, $payIn);
                    if ($selTransaction->order->payments->count()) {
                        /** @var OrderPayment $payment */
                        foreach ($selTransaction->order->payments as $payment) {
                            $amount = preg_replace('/[^.\d]/', '', $payIn['kwota']);
                            if ($payment->promise === '1' && $payment->amount == $amount) {
                                $payment->delete();
                                $selTransaction->order->payments()->create([
                                    'transaction_id' => $transaction->id,
                                    'amount' => $amount,
                                    'type' => 'CLIENT',
                                    'promise' => '',
                                ]);

                                $this->saveTransfer($selTransaction->order, $transaction);
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

    /**
     * Save new transaction
     *
     * @param Order $order Order object
     * @param array $data Additional data
     * @return int
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransaction(Order $order, array $data): Transaction
    {
        return $this->transaction->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'posted_in_bank_date' => new \DateTime($data['data']),
            'payment_id' => $data['identyfikator'],
            'kind_of_operation' => $data['operacja'],
            'order_id' => $order->id,
            'operator' => $data['operator'],
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
        if (!empty($lastCustomerTransaction = $this->transaction->findWhere([
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
        return $this->transaction->create([
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

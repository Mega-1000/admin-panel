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
            /** @var SelTransaction $selTransaction */
            $selTransaction = SelTransaction::where('tr_CheckoutFormPaymentId', '=', $payIn['identyfikator'])->first();
            try {
                if (!empty($selTransaction->order)) {
                    $transactionId = $this->saveTransaction($selTransaction->order, $payIn);
                    if ($selTransaction->order->payments->count()) {
                        /** @var OrderPayment $payment */
                        foreach ($selTransaction->order->payments as $payment) {
                            $amount = preg_replace('/[^.\d]/', '', $payIn['kwota']);
                            if ($payment->promise === '1' && $payment->amount == $amount) {
                                $payment->delete();
                                $selTransaction->order->payments()->create([
                                    'transaction_id' => $transactionId,
                                    'amount' => $amount,
                                    'type' => 'CLIENT',
                                    'promise' => '',
                                ]);
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
    private function saveTransaction(Order $order, array $data): int
    {
        return $this->transaction->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'posted_in_bank_date' => $data['data'],
            'payment_id' => $data['identyfikator'],
            'kind_of_operation' => $data['operacja'],
            'order_id' => $order->id,
            'operator' => $data['operator'],
            'operation_value' => $data['kwota'],
            'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data['kwota'],
            'accounting_notes' => '',
            'transaction_notes' => '',
        ])->id;
    }

    /**
     * Calculate balance
     *
     * @param integer $customerId Customer id
     * @return int
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getCustomerBalance(int $customerId): int
    {
        if (!empty($lastCustomerTransaction = $this->transaction->findWhere([
            ['customer_id', '=', $customerId]
        ])->last())) {
            return $lastCustomerTransaction->balance;
        } else {
            return 0;
        }
    }
}

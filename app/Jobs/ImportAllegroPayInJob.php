<?php

namespace App\Jobs;

use App\Entities\Order;
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
     * @param TransactionRepository $transaction
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
                    $this->saveTransaction($selTransaction->order, $payIn);
                }
            } catch (\Exception $exception) {
                dd($exception);
            }
        }
    }

    private function saveTransaction(Order $order, array $data)
    {
        $this->transaction->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'posted_in_bank_date' => $data['data'],
            'payment_id' => $data['identyfikator'],
            'kind_of_operation' => $data['operacja'],
            'order_id' => $order->id,
            'operator' => $data['operator'],
            'operation_value' => $data['kwota'],
            'balance' => (float)$this->getCustomerBalance($order->customer_id, $data['kwota']) + (float)$data['kwota'],
            'accounting_notes' => '',
            'transaction_notes' => '',
        ]);
    }

    private function getCustomerBalance($customerId, $operationValue)
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

<?php

namespace App\Jobs;

use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\DB;

class ImportPaymentsFromPdfFile implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;
    protected $orderRepository;
    protected $filename;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepository, $filename, $date = null)
    {
        $this->orderRepository = $orderRepository;
        $this->filename        = $filename;
        $this->date            = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->convertPdfFileToTextFile(
            config('payments-import.pdf-application-path'), config('payments-import.pdf-file-path').$this->filename, config('payments-import.output-file-path').$this->filename
        );


        $ordersIds = $this->getOrdersIds();
        $payments  = $this->getPaymentsFromTextFile(config('payments-import.output-file-path').$this->filename, $ordersIds);
        $infos     = $this->storePayments($payments);

        return $infos;
    }

    protected function convertPdfFileToTextFile($pdfApplicationPath, $pdfFilePath, $outputFilePath)
    {
        exec('node '.$pdfApplicationPath.' '.$pdfFilePath.' '.$outputFilePath);
    }

    protected function getPaymentsFromTextFile($filePath, $ordersIds)
    {
        $payments = [];
        $fn       = fopen($filePath, "r");
        $i        = 0;
        while (!feof($fn)) {
            $result = fgets($fn);
            if (strlen($result) == 27 && ctype_digit(substr($result, 0, 26)) || strlen($result) == 29 && ctype_digit(substr($result, 2, 26))) {
                $text   = fgets($fn);
                $letter = DB::table('order_packages')->where('letter_number', 'LIKE', trim($text))->first();
                if (!empty($letter)) {
                    $payments[$i]['orderId'] = $letter->order_id;
                }
                preg_match('/(\d{3,5})/', $text, $matches);
                if (count($matches) > 1) {
                    if (substr($matches[1], 0, 1) !== '0') {
                        if (in_array($matches[1], $ordersIds)) {
                            $payments[$i]['orderId'] = $matches[1];
                        }
                    }
                }
                $nextLine = fgets($fn);
                preg_match('/(\d{3,5})/', $nextLine, $matches);
                if (count($matches) > 1) {
                    if (substr($matches[1], 0, 1) !== '0') {
                        if (in_array($matches[1], $ordersIds)) {
                            $payments[$i]['orderId'] = $matches[1];
                        }
                    }
                }
            }
            if (strpos($result, 'PLN') !== false && strpos($result, '-') === false && strpos($result, 'a') === false) {
                $amount                 = (float) str_replace(',', '.', str_replace(' ', '', str_replace('PLN', '', $result)));
                $payments[$i]['amount'] = $amount;
                $i++;
            }
        }
        fclose($fn);

        return $payments;
    }

    protected function storePayments($payments)
    {
        $paymentsInfo = [];
        foreach ($payments as $payment) {
            if (array_key_exists('orderId', $payment)) {
                $paymentsInfo[] = app()->call(OrdersPaymentsController::class.'@storeFromImport', [$payment['orderId'], $payment['amount'], $this->date]);
            }
        }

        return $paymentsInfo;
    }

    protected function getOrdersIds()
    {
        $ordersIds = [];
        $orders    = $this->orderRepository->all();

        foreach ($orders as $order) {
            $ordersIds[] = $order->id;
            if ($order->id_from_front_db != NULL) {
                $ordersIds[] = $order->id_from_front_db;
            }
        }

        return $ordersIds;
    }
}
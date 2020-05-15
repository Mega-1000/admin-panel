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
        $basePath = base_path();
        $this->convertPdfFileToTextFile(
            $basePath . '/pdf/app.js', $basePath . '/storage/app/' . $this->filename, $basePath . '/storage/app/'. $this->filename
        );

        $ordersIds = $this->getOrdersIds();

        $payments  = $this->getPaymentsFromTextFile($basePath . '/storage/app/' . str_replace('.pdf', '.txt', $this->filename), $ordersIds);
        $infos     = $this->storePayments($payments);

        return $infos;
    }

    protected function convertPdfFileToTextFile($pdfApplicationPath, $pdfFilePath, $outputFilePath)
    {
        $outputFilePath = str_replace('.pdf', '', $outputFilePath);

        exec('node '.$pdfApplicationPath.' '.$pdfFilePath.' '. $outputFilePath);
    }

    protected function getPaymentsFromTextFile($filePath, $ordersIds)
    {
        $payments = [];
        $fn       = fopen($filePath, "r");
        $i        = 0;
        while (!feof($fn)) {
            $result = fgets($fn);
            if ($this->checkIfGivenLineContainAccoutNumber($result)) {
                $text   = fgets($fn);
                $letter = DB::table('order_packages')->where('letter_number', 'LIKE', trim($text))->first();
                if (!empty($letter)) {
                    $payments[$i]['orderId'] = $letter->order_id;
                }
                preg_match('/(\d{3,5})/', $text, $matches);
                if($this->verifyOrderId($matches, $ordersIds)) {
                    $payments[$i]['orderId'] = $matches[1];
                }
                $nextLine = fgets($fn);
                preg_match('/(\d{3,5})/', $nextLine, $matches);
                if($this->verifyOrderId($matches, $ordersIds)) {
                    $payments[$i]['orderId'] = $matches[1];
                }
            }
            if ($this->checkIfGivenLineContainValidAmount($result)) {
                $payments[$i]['amount'] = (float) str_replace(',', '.', str_replace(' ', '', str_replace('PLN', '', $result)));
                $i++;
            }
        }
        fclose($fn);

        return $payments;
    }

    private function verifyOrderId($matchedResult, $ordersIds)
    {
        var_dump($matchedResult);
        return count($matchedResult) > 1 && substr($matchedResult[1], 0, 1) !== '0' && in_array($matchedResult[1], $ordersIds);
    }

    private function checkIfGivenLineContainAccoutNumber(string $fileLine) {
        return strlen($fileLine) == 27 && ctype_digit(substr($fileLine, 0, 26)) || strlen($fileLine) == 29 && ctype_digit(substr($fileLine, 2, 26));
    }

    private function checkIfGivenLineContainValidAmount(string $fileLine) {
        return strpos($fileLine, 'PLN') !== false && strpos($fileLine, '-') === false && strpos($fileLine, 'a') === false;
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

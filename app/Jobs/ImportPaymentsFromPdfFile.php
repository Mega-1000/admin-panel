<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\PdfToText\Pdf;

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
        $this->filename = $filename;
        $this->date = $date;
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
            $basePath . '/pdf/app.js', $basePath . '/storage/app/' . $this->filename, $basePath . '/storage/app/' . $this->filename
        );
        $payments = $this->getPaymentsFromTextFile($basePath . '/storage/app/' . str_replace('.pdf', '.txt', $this->filename));
        $infos = $this->storePayments($payments);

        return $infos;
    }

    protected function convertPdfFileToTextFile($pdfApplicationPath, $pdfFilePath, $outputFilePath)
    {
        $outputFilePath = str_replace('.pdf', '', $outputFilePath);

        exec('node ' . $pdfApplicationPath . ' ' . $pdfFilePath . ' ' . $outputFilePath);
    }

    protected function getPaymentsFromTextFile($filePath)
    {
        $payments = [];
        $fn = fopen($filePath, "r");
        while (!feof($fn)) {
            $line = fgets($fn);
            $isNewTransaction = $this->checkIfTransactionbegins($line);
            if (!$isNewTransaction) {
                continue;
            }
            try {
                $payments = $this->processNewTransaction($fn);
            } catch (\Exception $e) {
                \Log::error($e->getMessage(), ['line' => $e->getTraceAsString()]);
            }
        }
        fclose($fn);

        return $payments;
    }

    private function checkIfTransactionbegins(string $result)
    {
        return preg_match('/PRZELEW.*?PRZYCHODZĄCY/', $result);
    }

    /**
     * @param $fn
     * @param array $payments
     * @return array
     * @throws \Exception
     */
    protected function processNewTransaction($fn)
    {
        $isNewTransaction = false;
        $payments = [];
        while (!$isNewTransaction && !feof($fn)) {
            $line = fgets($fn);

            $orderRegexMatches = $this->checkIfGivenLineContainOrderNumber($line);
            if (empty($orderRegexMatches[0])) {
                continue;
            }
            if ($this->verifyOrderId($orderRegexMatches[0])) {
                $payment = [];
                $payment['orderId'] = $orderRegexMatches[0];
                $line = fgets($fn);
                $newline = str_replace(' ', '', $line);
                $newline = explode(',', $newline);
                if (count($newline) < 3) {
                    throw new \Exception('Problem z importem płatności');
                }
                $amount = $newline[0];
                $penny = substr($newline[1], 0, 2);
                $payment['amount'] = $amount . '.' . $penny;
                $payments [] = $payment;
            }
            $isNewTransaction = $this->checkIfTransactionbegins($line);
        };
        if (!feof($fn)) {
            $payments = array_merge($payments, $this->processNewTransaction($fn));
        }
        return $payments;
    }

    private function checkIfGivenLineContainOrderNumber(string $fileLine)
    {
        $matches = [];
        preg_match_all('/[qQ][qQ](\d{3,5})[qQ][qQ]/', $fileLine, $matches);
        return $matches;
    }

    private function verifyOrderId($matchedResult)
    {
        foreach ($matchedResult as $result) {
            $id = str_replace('QQ', '', $result);
            if (empty(Order::find($id))) {
                return false;
            }
        }
        return true;
    }

    protected function storePayments($payments)
    {
        $paymentsInfo = [];
        foreach ($payments as $payment) {
            if (!array_key_exists('orderId', $payment)) {
                continue;
            }
            $sum = OrderPayment::whereIn('order_id', $payment['orderId'])->where('promise', 1)->sum('amount');
            if (abs($sum - $payment['amount']) > 2) {
                continue;
            }
            foreach ($payment['orderId'] as $id) {
                $payment = OrderPayment::where('order_id', $id)->where('promise', 1)->first();
                $paymentsInfo[] = app()->call(OrdersPaymentsController::class . '@storeFromImport', [$id, $payment->amount, $this->date]);
            }
        }

        return $paymentsInfo;
    }

    private function checkIfGivenLineContainAccoutNumber(string $fileLine)
    {
        return strlen($fileLine) == 27 && ctype_digit(substr($fileLine, 0, 26)) || strlen($fileLine) == 29 && ctype_digit(substr($fileLine, 2, 26));
    }

    private function checkIfGivenLineContainValidAmount(string $fileLine)
    {
        $fileLine = str_replace(' ', '', $fileLine);
        if (strpos($fileLine, 'PLN') !== false) {
            preg_match('/([1-9][0-9]*|0)(\,[0-9]{2})?/', $fileLine, $matches);

            return $matches;
        }
        return [];
    }

}

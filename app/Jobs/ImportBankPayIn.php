<?php

namespace App\Jobs;

use App\DTO\PayInDTO;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Enums\OrderPaymentsEnum;
use App\Enums\OrderTransactionEnum;
use App\Factory\PayInDTOFactory;
use App\Helpers\PdfCharactersHelper;
use App\Repositories\FileInvoiceRepository;
use App\Repositories\OrderPayments;
use App\Repositories\TransactionRepository;
use App\Services\FindOrCreatePaymentForPackageService;
use App\Services\Label\AddLabelService;
use App\Services\LabelService;
use App\Services\OrderPaymentLabelsService;
use App\Services\OrderPaymentService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportBankPayIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TransactionRepository                $transactionRepository;

    protected LabelService                         $labelService;
    protected FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService;

    protected OrderPaymentService                  $orderPaymentService;
    protected                                      $orderPaymentLabelsService;

    public function __construct(
        protected UploadedFile                         $file,
    ) {}

    /**
     * Execute the job.
     *
     * @param TransactionRepository $transaction
     * @param LabelService $labelService
     * @param FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService
     * @param OrderPaymentService $orderPaymentService
     * @param OrderPaymentLabelsService $orderPaymentLabelsService
     * @return string
     */
    public function handle(
        TransactionRepository                $transaction,
        LabelService                         $labelService,
        FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService,
        OrderPaymentService                  $orderPaymentService,
        OrderPaymentLabelsService            $orderPaymentLabelsService,
    ): string
    {
        $this->findOrCreatePaymentForPackageService = $findOrCreatePaymentForPackageService;
        $this->orderPaymentService = $orderPaymentService;
        $this->orderPaymentLabelsService = $orderPaymentLabelsService;

        $header = NULL;
        $fileName = 'bankTransactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $reportName = 'bankTransactionReport' . date('Y-m-d H:i:s') . '.csv';
        $reportPath = 'public/reports/' . $reportName;
        Storage::put($reportPath, '');
        $report = fopen(storage_path('app/' . $reportPath), 'w');

        $this->labelService = $labelService;
        $this->transactionRepository = $transaction;
        $data = array();
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 5000, ';')) !== FALSE) {
                if (count(array_filter($row)) < 5) {
                    continue;
                }

                if (!$header) {
                    foreach ($row as &$headerName) {
                        if (!empty($headerName)) {
                            $headerName = str_replace('#', '', iconv('ISO-8859-2', 'UTF-8', $headerName));
                            $headerName = Str::snake(PdfCharactersHelper::changePolishCharactersToNonAccented($headerName));
                        }
                    }
                    $header = $row;
                    fputcsv($file, $row);
                } else {
                    foreach ($row as &$text) {
                        $text = iconv('ISO-8859-2', 'UTF-8', $text);
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        foreach ($data as $payIn) {
            $payInDto = $this->checkOrderNumberFromTitle($payIn['tytul'], $payIn);
            $payIn['kwota'] = (float)str_replace(',', '.', preg_replace('/[^.,\d]/', '', $payIn['kwota']));

            $payIn['operation_type'] = 'Wpłata/wypłata bankowa';

            if ($payInDto->message === "Brak dopasowania") {
                continue;
            } else if ($payInDto->message === "Brak numeru zamówienia") {
                fputcsv($file, $payIn);
                continue;
            } else if ($payInDto->message === "/[zZ][zZ](\d{3,5})[zZ][zZ]/") {
                $payIn['kwota'] *= -1;
            } else if ($payInDto->message === "/[yY][yY](\d{3,5})[yY][yY]/") {
                $payIn['kwota'] *= -1;

                $payIn['operation_type'] = OrderPaymentsEnum::INVOICE_BUYING_OPERATION_TYPE;
            }

            if ($payInDto->orderId === null) {
                fputcsv($file, $payIn);
                continue;
            }

            $orderId = $payInDto->orderId;

            $order = Order::find($orderId);
            if ($order == null) {
                fputcsv($file, $payIn);
                continue;
            }

            try {
                if (!empty($order)) {
                    $this->settlePromisePayments($order, $payIn);
                    $orders = $this->getRelatedOrders($order);

                    $this->settleOrders($orders, $payIn);
                } else {
                    fputcsv($file, $payIn);
                }
            } catch (Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__, 'error_line' => $exception->getLine()]);
            }
        }

        fclose($file);
        fclose($report);

        Storage::disk('transactionsDisk')
            ->put("bankTransactionWithoutOrder" . date('Y-m-d') . '.csv', file_get_contents($fileName));

        return Storage::url($reportPath);
    }

    /**
     * Search order number.
     *
     * @param string $fileLine Line in csv file.
     * @param $payIn
     * @return PayInDTO
     */
    private function checkOrderNumberFromTitle(string $fileLine, $payIn): PayInDTO
    {
        $fileLine = str_replace(' ', '', $fileLine);

        $possibleOperationDescriptions = [
            'PP/PRZELEW EXPRESS ELIXIR PRZYCH.',
            'PP/PRZELEW WEWNĘTRZNY PRZYCHODZĄCY',
            'PP/PRZELEW ZEWNĘTRZNY PRZYCHODZĄCY',
            'PRZELEW EXPRESS ELIXIR PRZYCH.',
            'PRZELEW EXPRESSOWY PRZELEW PRZYCH.',
            'PRZELEW SEPA PRZYCHODZĄCY',
            'PRZELEW WEWNĘTRZNY PRZYCHODZĄCY',
            'PRZELEW ZEWNĘTRZNY PRZYCHODZĄCY',
            'PRZELEW ZEWNĘTRZNY WYCHODZĄCY',
            'PP/PRZELEW ZEWNĘTRZNY WYCHODZĄCY',
        ];

        $notPossibleOperationDescriptions = [
            'WYPŁATA PAYPRO',
            'Wyplata PayPro',
            'Wypłata PayU',
            'INFODEMOS',
        ];

        foreach ($notPossibleOperationDescriptions as $notPossibleOperationDescription) {
            if (str_contains(implode(' ', $payIn), $notPossibleOperationDescription)) {
                return PayInDTOFactory::createPayInDTO([
                    'data' => $payIn,
                    'message' => 'Brak dopasowania',
                ]);
            }
        }

        $possibleOperationDescriptions = array_map(function($description) {
            return str_replace('Ą', 'Ľ', $description);
        }, $possibleOperationDescriptions);

        if (!in_array($payIn['opis_operacji'], $possibleOperationDescriptions)) {
            return PayInDTOFactory::createPayInDTO([
                'data' => $payIn,
                'message' => 'Brak dopasowania',
            ]);
        }

        $patterns = [
            '/[qQ][qQ](\d{3,5})[qQ][qQ]/',
            '/[zZ][zZ](\d{3,5})[zZ][zZ]/',
            '/[yY][yY](\d{3,5})[yY][yY]/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $fileLine, $matches)) {
                if (preg_match('/\s/', $matches[0]) || preg_match('/[a-zA-Z]/', $matches[0])) {
                    $matches[0] = preg_replace('/\s/', '', $matches[0]);
                    $matches[0] = preg_replace('/[a-zA-Z]/', '', $matches[0]);
                }

                return new PayInDTO(
                    orderId: (int)$matches[0],
                    data: $payIn,
                    message: $pattern
                );
            }
        }

        preg_match_all('/^\d{5}$/', $fileLine, $matches);

        if (count($matches)) {
            foreach ($matches[0] as $orderId) {
                $order = Order::query()->find($orderId);
                Log::notice($order);
                if (!empty($order) && $order->getValue() == (float)str_replace(',', '.', preg_replace('/[^.,\d]/', '', $fileLine))) {
                    return PayInDTOFactory::createPayInDTO([
                        'orderId' => (int)$order->id,
                        'data' => $payIn,
                    ]);
                }

                // if order value does not match, throw exception
                if (!empty($order)) {
                    return PayInDTOFactory::createPayInDTO([
                        'data' => $payIn,
                        'message' => 'Brak dopasowania',
                    ]);
                }
            }
        }

        $allegoIdPattern = '/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/';
        if (preg_match($allegoIdPattern, $payIn['tytul'], $matches)) {
        $order = Order::query()->where('allegro_form_id', $matches[0])->first();

        if (!empty($order)) {
            return PayInDTOFactory::createPayInDTO([
                'orderId' => (int)$order->id,
                'data' => $payIn,
            ]);
        }
    }

        $invoicePattern = '/\b(?:\d{1,6}\s*\/\s*(?:sta|mag|tra|kos)\s*\/\s*\d{2}\s*\/\s*\d{2}\s*\d{2})\b/';
        if (preg_match($invoicePattern, mb_strtolower($payIn['tytul']), $matches)) {
            $matches[0] = str_replace(' ', '', $matches[0]);
            $orderId = FileInvoiceRepository::getInvoiceIdFromNumber(str_replace('/', '_', $matches[0]));
            $order = Order::query()->find($orderId);

            if (!empty($order)) {
                return PayInDTOFactory::createPayInDTO([
                    'orderId' => (int)$order->id,
                    'data' => $payIn,
                ]);
            }
        }

        // No matching order id found
        return PayInDTOFactory::createPayInDTO([
            'data' => $payIn,
        ]);
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
     * Settle promise.
     *
     * @param Order $order Order object.
     * @param array $payIn Pay in row.
     */
    private function settlePromisePayments(Order $order, array $payIn): void
    {
        foreach ($order->payments()->where('promise', '1')->whereNull('deleted_at')->get() as $payment) {
            if ($payIn['kwota'] === (float)$payment->amount) {
                $payment->delete();
            } else {
                $preventionArray = [];
                AddLabelService::addLabels($order, [128], $preventionArray, [], Auth::user()?->id);
            }
        }
    }

    /**
     * Return related Orders
     *
     * @param Order $order Order object.
     * @return Collection
     */
    private function getRelatedOrders(Order $order): Collection
    {
        return Order::where('master_order_id', '=', $order->id)->orWhere('id', '=', $order->id)->get();
    }

    /**
     * Settle orders.
     *
     * @param Collection $orders Orders collection
     * @param array $payIn
     */
    private function settleOrders(Collection $orders, array $payIn): void
    {
        $amount = $payIn['kwota'];
        $order = $orders[0];

        $this->findOrCreatePaymentForPackageService->execute(
            OrderPackage::where('order_id', $order->id)->first(),
        );

        if ($amount < 0) {
            $this->saveOrderPayment($order, $amount, $payIn, false, $payIn['operation_type']);
            return;
        }

        $declaredSum = OrderPayments::getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn) >= 1;
        OrderPayments::updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn);

        $orderPayment = $this->saveOrderPayment($order, $amount, $payIn, $declaredSum, $payIn['operation_type']);

        if ($orderPayment instanceof OrderPayment) {
            $this->orderPaymentService->dispatchLabelsForPaymentAmount($orderPayment);
        }
    }

    /**
     * @param Order $order
     * @param float $paymentAmount
     * @param array $payIn
     * @param false $declaredSum
     * @param string $operationType
     * @return Model
     */
    private function saveOrderPayment(Order $order, float $paymentAmount, array $payIn, $declaredSum = false, string $operationType): Model
    {
        /** @var ?OrderPayment $payment */
        $payment = OrderPayment::where('order_id', $order->id)->where('comments', implode(" ", $payIn))->first();
        $operationType = $operationType ?? 'Wpłata/wypłata bankowa';
        $payer = $operationType === OrderPaymentsEnum::INVOICE_BUYING_OPERATION_TYPE ? 'info@ephpolska.pl' : (string)$order->customer()->first()->login;

        unset($payIn['operation_type']);

        $payment = !isset($payment)
            ? $order->payments()->create([
                'amount' => $paymentAmount,
                'type' => 'CLIENT',
                'promise' => '',
                'payer' => $payer,
                'operation_date' => $payIn['data_ksiegowania'],
                'created_by' => OrderTransactionEnum::CREATED_BY_BANK,
                'comments' => implode(" ", $payIn),
                'operation_type' => $operationType,
                'status' => $declaredSum ? 'Rozliczająca deklarowaną' : null,
            ]) : $payment;

        $this->orderPaymentLabelsService->calculateLabels($payment->order);

        return $payment;
    }
}


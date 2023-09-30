<?php

namespace App\Jobs;

use App\DTO\PayInDTO;
use App\DTO\PayInImport\BankPayInDTO;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Enums\OrderPaymentsEnum;
use App\Enums\OrderTransactionEnum;
use App\Factory\BankPayInDTOFactory;
use App\Factory\PayInDTOFactory;
use App\Helpers\Exceptions\ChatException;
use App\Http\Controllers\CreateTWSOOrdersDTO;
use App\Repositories\FileInvoiceRepository;
use App\Repositories\OrderPayments;
use App\Repositories\TransactionRepository;
use App\Services\FindOrCreatePaymentForPackageService;
use App\Services\Label\AddLabelService;
use App\Services\LabelService;
use App\Services\OrderPaymentLabelsService;
use App\Services\OrderPaymentService;
use App\Services\OrderService;
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

class ImportBankPayIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TransactionRepository                $transactionRepository;
    protected LabelService                         $labelService;
    protected FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService;
    protected OrderPaymentService                  $orderPaymentService;
    protected orderPaymentLabelsService            $orderPaymentLabelsService;

    public function __construct(
        protected UploadedFile $file,
    ) {}

    /**
     * Execute the job.
     *
     * @return string
     * @throws ChatException
     */
    public function handle(): string
    {
        $this->findOrCreatePaymentForPackageService = app(FindOrCreatePaymentForPackageService::class);
        $this->orderPaymentService = app(OrderPaymentService::class);
        $this->orderPaymentLabelsService = app(OrderPaymentLabelsService::class);
        $this->labelService = app(LabelService::class);
        $this->transactionRepository = app(TransactionRepository::class);

        $fileName = 'bankTransactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $reportName = 'bankTransactionReport' . date('Y-m-d H:i:s') . '.csv';
        $reportPath = 'public/reports/' . $reportName;
        Storage::put($reportPath, '');
        $report = fopen(storage_path('app/' . $reportPath), 'w');

        $data = BankPayInDTOFactory::fromFile($this->file);

        foreach ($data as $payIn) {
            $payInDto = $this->checkOrderNumberFromTitle($payIn->tytul, $payIn);
            $payIn->kwota = (float)str_replace(',', '.', preg_replace('/[^.,\d]/', '', $payIn->kwota));

            $payIn->setOperationType('Wpłata/wypłata bankowa');

            switch($payInDto->message) {
                case 'Brak numeru zamówienia':
                case 'Brak dopasowania':
                    if ($payIn->contains('TECHN.')) {
                        continue;
                    }

                    $this->createTWSUOrder($payIn);
                    break;
                case '/[zZ][zZ](\d{3,5})[zZ][zZ]/':
                    $payIn->kwota *= -1;
                    break;
                case '/[yY][yY](\d{3,5})[yY][yY]/':
                    $payIn->setOperationType(OrderPaymentsEnum::INVOICE_BUYING_OPERATION_TYPE);
                    break;
            }

            if ($payInDto->orderId === null) {
                fputcsv($file, $payIn->toArray());
                continue;
            }

            $orderId = $payInDto->orderId;

            $order = Order::find($orderId);
            if ($order == null) {
                fputcsv($file, $payIn->toArray());
                continue;
            }

            try {
                if (!empty($order)) {
                    $this->settlePromisePayments($order, $payIn);
                    $orders = $this->getRelatedOrders($order);

                    $this->settleOrders($orders, $payIn);
                } else {
                    fputcsv($file, $payIn->toArray());
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
     * @param BankPayInDTO $payIn
     * @return PayInDTO
     */
    private function checkOrderNumberFromTitle(string $fileLine, BankPayInDTO $payIn): PayInDTO
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
            if (str_contains($payIn->stringify(), $notPossibleOperationDescription)) {
                return PayInDTOFactory::createPayInDTO([
                    'data' => $payIn,
                    'message' => 'Brak dopasowania',
                ]);
            }
        }

        $possibleOperationDescriptions = array_map(function($description) {
            return str_replace('Ą', 'Ľ', $description);
        }, $possibleOperationDescriptions);

        if (!in_array($payIn->opis_operacji, $possibleOperationDescriptions)) {
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
                    message: $pattern,
                );
            }
        }

        preg_match_all('/^\d{5}$/', $fileLine, $matches);

        if (count($matches)) {
            foreach ($matches[0] as $orderId) {
                $order = Order::query()->find($orderId);
                if (!empty($order) && $order->getValue() == (float)str_replace(',', '.', preg_replace('/[^.,\d]/', '', $fileLine))) {
                    return PayInDTOFactory::createPayInDTO([
                        'orderId' => (int)$order->id,
                        'data' => $payIn,
                    ]);
                }

                if (!empty($order)) {
                    return PayInDTOFactory::createPayInDTO([
                        'data' => $payIn,
                        'message' => 'Brak dopasowania',
                    ]);
                }
            }
        }

        $allegoIdPattern = '/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/';
        if (preg_match($allegoIdPattern, $payIn->tytul, $matches)) {
        $order = Order::query()->where('allegro_form_id', $matches[0])->first();

        if (!empty($order)) {
            return PayInDTOFactory::createPayInDTO([
                'orderId' => (int)$order->id,
                'data' => $payIn,
            ]);
        }
    }

        $invoicePattern = '/\b(?:\d{1,6}\s*\/\s*(?:sta|mag|tra|kos)\s*\/\s*\d{2}\s*\/\s*\d{2}\s*\d{2})\b/';
        if (preg_match($invoicePattern, mb_strtolower($payIn->tytul), $matches)) {
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

        return PayInDTOFactory::createPayInDTO([
            'data' => $payIn,
        ]);
    }

    /**
     * Settle promise.
     *
     * @param Order $order Order object.
     * @param BankPayInDTO $payIn Pay in row.
     */
    private function settlePromisePayments(Order $order, BankPayInDTO $payIn): void
    {
        foreach ($order->payments()->where('promise', '1')->whereNull('deleted_at')->get() as $payment) {
            if ($payIn->kwota === (float)$payment->amount) {
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
     * @param BankPayInDTO $payIn
     */
    private function settleOrders(Collection $orders, BankPayInDTO $payIn): void
    {
        $amount = $payIn->kwota;
        $order = $orders[0];

        $this->findOrCreatePaymentForPackageService->execute(
            OrderPackage::where('order_id', $order->id)->first(),
        );

        if ($amount < 0) {
            $this->saveOrderPayment($order, $amount, $payIn, false, $payIn->operation_type);
            return;
        }

        $declaredSum = OrderPayments::getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn) >= 1;
        OrderPayments::updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn);

        $orderPayment = $this->saveOrderPayment($order, $amount, $payIn, $declaredSum, $payIn->operation_type);

        if ($orderPayment instanceof OrderPayment) {
            $this->orderPaymentService->dispatchLabelsForPaymentAmount($orderPayment);
        }
    }

    /**
     * @param Order $order
     * @param float $paymentAmount
     * @param BankPayInDTO $payIn
     * @param false $declaredSum
     * @param string $operationType
     * @return Model
     */
    private function saveOrderPayment(Order $order, float $paymentAmount, BankPayInDTO $payIn, $declaredSum = false, string $operationType): Model
    {
        unset($payIn->operation_type);
        $operationType = $operationType ?? 'Wpłata/wypłata bankowa';

        /** @var ?OrderPayment $payment */
        $payment = OrderPayment::where('comments', $payIn->stringify())->first();
        $payer = $operationType === OrderPaymentsEnum::INVOICE_BUYING_OPERATION_TYPE ? 'info@ephpolska.pl' : (string)$order->customer()->first()->login;


        $payment = !isset($payment)
            ? $order->payments()->create([
                'amount' => $paymentAmount,
                'type' => 'CLIENT',
                'promise' => '',
                'payer' => $payer,
                'operation_date' => $payIn->data_ksiegowania,
                'created_by' => OrderTransactionEnum::CREATED_BY_BANK,
                'comments' => implode(" ", $payIn->toArray()),
                'operation_type' => $operationType,
                'status' => $declaredSum ? 'Rozliczająca deklarowaną' : null,
            ]) : $payment;

        if ($order->payments()->count() === 1) {
            $order->labels()->attach(45);
        }

        $this->orderPaymentLabelsService->calculateLabels($payment->order);

        return $payment;
    }

    /**
     * Create twsu order if not found
     *
     * @param BankPayInDTO $data
     * @return void
     * @throws ChatException
     */
    public static function createTWSUOrder(BankPayInDTO $data): void
    {
        if (self::checkIfTWSUOrderExists($data)) {
            return;
        }

        $orderId = OrderService::createTWSOOrders(new CreateTWSOOrdersDTO(
            warehousesSymbols: null,
            clientEmail: 'info@ephpolska.pl',
            purchaseValue: $data->kwota,
            consultantDescription: $data->stringify(),
        ));

        $order = Order::find($orderId);

        $order->labels()->detach([83, 237, 92]);
    }

    /**
     * Check if twsu order exists based on comments given in chat
     *
     * @param BankPayInDTO $data
     * @return bool
     */
    public static function checkIfTWSUOrderExists(BankPayInDTO $data): bool
    {
        return Order::query()->whereHas('chat', function ($query) use ($data) {
            $query->whereHas('messages', function ($query) use ($data) {
                $query->where('message', 'like', '%' . $data->stringify() . '%');
            });
        })->exists();
    }
}

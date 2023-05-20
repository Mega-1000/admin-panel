<?php

namespace App\Jobs;

use App\DTO\PayInDTO;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Transaction;
use App\Enums\LabelEventName;
use App\Enums\OrderTransactionEnum;
use App\Factory\PayInDTOFactory;
use App\Helpers\PdfCharactersHelper;
use App\Http\Controllers\OrdersPaymentsController;
use App\Integrations\Pocztex\paczkaPocztowaPLUSType;
use App\Repositories\FileInvoiceRepository;
use App\Repositories\OrderPayments;
use App\Repositories\TransactionRepository;
use App\Services\Label\AddLabelService;
use App\Services\LabelService;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
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

/**
 *
 */
class ImportBankPayIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Transaction
     */
    protected $transactionRepository;

    /**
     * @var LabelService
     */
    protected $labelService;

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
     * @param LabelService $labelService
     * @return string
     */
    public function handle(TransactionRepository $transaction, LabelService $labelService): string
    {
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

            if ($payInDto->message === "Brak dopasowania") {
                continue;
            } else if ($payInDto->message === "Brak numeru zamówienia") {
                fputcsv($file, $payIn);
                continue;
            } else if ($payInDto->message === "/[zZ][zZ](\d{3,5})[zZ][zZ]/") {
                $payIn['kwota'] *= -1;
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
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
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
        ];

        $notPossibleOperationDescriptions = [
            'WYPŁATA PAYPRO',
            'Wypłata PayU',
            'INFODEMOS',
        ];

        foreach ($notPossibleOperationDescriptions as $notPossibleOperationDescription) {
            if (str_contains($fileLine, $notPossibleOperationDescription)) {
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
            '/[zZ][zZ](\d{3,5})[zZ][zZ]/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $fileLine, $matches)) {
                return new PayInDTO(
                    orderId: (int)$matches[1],
                    data: $payIn,
                    message: $pattern
                );
            }
        }

        preg_match_all('/^\s*(\d(?:\s*\d)*)\s*$/', $fileLine, $matches);

        if (count($matches[1])) {
            foreach ($matches[1] as $orderId) {
                $order = Order::query()->find($orderId);

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

        $allegoIdPattern = '/^Platnosc za zamowienie\s+([a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12})$/';
        if (str_contains($fileLine, 'PRAGMAGO') || preg_match($allegoIdPattern, $payIn['tytul'], $matches)) {
            $order = Order::query()->where('allegro_transaction_id', $matches[0])->first();

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
     * Settle promise.
     *
     * @param Order $order Order object.
     * @param array $payIn Pay in row.
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
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
     * @param Collection $orders Orders collection
     * @param array $payIn
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function settleOrders(Collection $orders, array $payIn): void
    {
        $amount = $payIn['kwota'];

        foreach ($orders as $order) {
            if ($amount < 0) {
                $this->saveOrderPayment($order, $amount, $payIn, false);
                continue;
            }

            $amountOutstanding = $order->getOfferFinanceBilans($order);

            if ($amount == 0 || $amountOutstanding == 0) {
                continue;
            }

            $paymentAmount = min($amount, $amountOutstanding);

            $declaredSum = OrderPayments::getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn) >= 1;
            OrderPayments::updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn);

            $orderPayment = $this->saveOrderPayment($order, $paymentAmount, $payIn, $declaredSum);

            if ($orderPayment instanceof OrderPayment) {
                OrdersPaymentsController::dispatchLabelsForPaymentAmount($orderPayment);
            }
        }
    }

    /**
     * @param Order $order
     * @param $paymentAmount
     * @param $payIn
     * @param false $declaredSum
     *
     * @return Model
     */
    private function saveOrderPayment(Order $order, $paymentAmount, $payIn, $declaredSum = false): Model
    {
        return $order->payments()->create([
            'amount' => $paymentAmount,
            'type' => 'CLIENT',
            'promise' => '',
            'payer' => $order->customer()->first()->login,
            'operation_date' => $payIn['data_ksiegowania'],
            'created_by' => OrderTransactionEnum::CREATED_BY_BANK,
            'comments' => implode(" ", $payIn),
            'operation_type' => 'Wpłata/wypłata bankowa',
            'status' => $declaredSum ? 'Rozliczająca deklarowaną' : null,
        ]);
    }


    /**
     * @param Order $order
     * @return float
     */
    private function getTotalOrderValue(Order $order): float
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
     * Tworzy transakcje przeksięgowania
     *
     * @param Order $order
     * @param Transaction $transaction
     * @param float $amount
     * @param boolean $back
     * @return Transaction|null
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
            'posted_in_system_date' => new DateTime(),
            'payment_id' => $identifier,
            'kind_of_operation' => 'przeksięgowanie',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => -$amount,
            'balance' => (float)$this->getCustomerBalance($order->customer_id) - (float)$amount,
            'accounting_notes' => '',
            'transaction_notes' => '',
            'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL,
        ]);
    }
}


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
     * @return void
     */
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
                    if (!str_contains($row[2], 'PRZYCH')) {
                        continue;
                    }
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

            if ($payInDto->message === "Brak dopasowania") {
                continue;
            } else if ($payInDto->message === "Brak numeru zamówienia") {
                fputcsv($report, $payIn);
                continue;
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
                    $payIn['kwota'] = (float)str_replace(',', '.', preg_replace('/[^.,\d]/', '', $payIn['kwota']));
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

        $match = false;
         foreach ($possibleOperationDescriptions as $possibleOperationDescription) {
             if (str_contains(implode(" ", (array)$payIn), $possibleOperationDescription)) {
                $match = true;
                break;
             }
         }

        if ($match === false) {
            return PayInDTOFactory::createPayInDTO([
                'data' => $payIn,
                'message' => 'Brak dopasowania',
            ]);
        }

        // Find order id by searching for "qq" pattern
        preg_match('/[qQ][qQ](\d{3,5})[qQ][qQ]/', $fileLine, $matches);
        if (count($matches)) {
            return PayInDTOFactory::createPayInDTO([
                'orderId' => (int)$matches[1],
                'data' => $payIn,
            ]);
        }

        // Find order id by searching for numeric pattern
        preg_match_all('/(\d{3,5})/', $fileLine, $matches);
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
        foreach ($order->payments()->where('promise', '=', '1')->whereNull('deleted_at')->get() as $payment) {
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
    private function getRelatedOrders(Order $order)
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
            $orderBookedPaymentSum = $order->bookedPaymentsSum();
            $amountOutstanding = $this->getTotalOrderValue($order) - $orderBookedPaymentSum;

            if ($amount == 0 || $amountOutstanding == 0) {
                continue;
            }

            $paymentAmount = min($amount, $amountOutstanding);

            $orderPayment = $order->payments()->create([
                'amount' => $paymentAmount,
                'type' => 'CLIENT',
                'promise' => '',
                'payer' => $order->customer()->first()->login,
                'operation_date' => $payIn['data_ksiegowania'],
                'created_by' => OrderTransactionEnum::CREATED_BY_BANK,
                'comments' => implode(" ", $payIn),
                'operation_type' => 'Wpłata/wypłata bankowa'
            ]);

            if ($orderPayment instanceof OrderPayment) {
                OrdersPaymentsController::dispatchLabelsForPaymentAmount($orderPayment);
            }
        }
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


<?php

namespace App\Services;

use App\Domains\DelivererPackageImport\PriceFormatter;
use App\DTO\ImportPayIn\ShippingPayInCsvDataDTO;
use App\Entities\Deliverer;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Entities\ProviderTransaction;
use App\Entities\Transaction;
use App\Factory\ShippingPayInCsvDataFactory;
use App\Helpers\PdfCharactersHelper;
use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\DelivererRepositoryEloquent;
use App\Repositories\OrderPackageRepositoryEloquent;
use App\Repositories\ProviderTransactionRepositoryEloquent;
use App\Repositories\ProviderTransactions;
use App\Repositories\TransactionRepository;
use App\Services\Label\AddLabelService;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
final class ShippingPayInService
{
    public UploadedFile $file;

    public function __construct(
        readonly protected TransactionRepository $transactionRepository,
        readonly protected OrderPackageRepositoryEloquent $orderPackageRepositoryEloquent,
        readonly protected DelivererRepositoryEloquent $delivererRepositoryEloquent,
        readonly protected ProviderTransactionRepositoryEloquent $providerTransactionRepositoryEloquent
    ) {}

    /**
     * @throws Exception
     */
    public function processPayIn(UploadedFile $file): void
    {
        $this->file = $file;

        $fileName = 'shippingTransactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');
        $header = false;

        $data = array();
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 5000, ';')) !== FALSE) {
                if (count(array_filter($row)) < 5) {
                    continue;
                }

                if (!$header) {
                    foreach ($row as &$headerName) {
                        if (!empty($headerName)) {
                            $headerName = iconv('ISO-8859-2', 'UTF-8', $headerName);
                            $headerName = Str::snake(PdfCharactersHelper::changePolishCharactersToNonAccented($headerName));
                        }
                    }
                    $header = $row;
                    fputcsv($file, $row);
                } else {
                    $data[] = array_combine($header, $row);
                }
            }

            fclose($handle);
        }

        foreach ($data as $payIn) {
            $payIn = ShippingPayInCsvDataFactory::create($payIn);

            try {
                $orderPackage = $this->findOrderPackageByLetterNumber($payIn->numer_listu);

                if (!empty($orderPackage)) {
                    $realCost = $this->addRealCost($orderPackage, $payIn);

                    if (!$realCost) {
                        continue;
                    }

                    $order = $orderPackage->order;
                } else {
                    fputcsv($file, $payIn->toArray());
                    continue;
                }

                if (!empty($order)) {
                    if (empty($payIn->wartosc_pobrania)) {
                        continue;
                    }

                    $payIn->rzeczywisty_koszt_transportu_brutto = (float)str_replace(',', '.',$payIn->rzeczywisty_koszt_transportu_brutto);
                    $transaction = $this->saveTransaction($order, $payIn);

                    if ($transaction === null) {
                        continue;
                    }

                    $this->settlePromisePayments($order, $payIn);
                    $orders = $this->getRelatedOrders($order);

                    $this->settleOrders($orders, $transaction);
                    $this->settleProvider($payIn, $transaction);
                } else {
                    fputcsv(
                        $file,
                        $payIn->toArray()
                    );
                }
            } catch (Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage() . ' w lini ' . $exception->getLine(), ['line' => __LINE__, 'file' => __FILE__]);
            }
        }

        fclose($file);
        Storage::disk('local')
            ->put('public/transaction/shippingTransactionWithoutOrder' . date('Y-m-d') . '.csv', file_get_contents($fileName));
    }

    private function findOrderPackageByLetterNumber(string $letterNumber): ?OrderPackage
    {
        return OrderPackage::where('letter_number', $letterNumber)->first();
    }

    private function addRealCost(OrderPackage $orderPackage, ShippingPayInCsvDataDTO $payIn): bool
    {
        $notices = explode($orderPackage->notices, '^')[0];
        $orderPackage->update([
            'notices' => $notices . '^' . $payIn->reszta,
            'invoice_number' => $payIn->nr_faktury_do_ktorej_dany_lp_zostal_przydzielony,
            'service_courier_name' => $payIn->symbol_spedytora,
        ]);

        $delivery = Deliverer::where('name', $payIn->symbol_spedytora)->firstOrFail();

        $cost = PriceFormatter::asAbsolute(PriceFormatter::fromString($payIn->rzeczywisty_koszt_transportu_brutto));
        $orderPackageRealCost = $orderPackage->realCostsForCompany()
            ->where('deliverer_id', '=', $delivery->id)
            ->where('cost', '=', $cost)
            ->where('order_package_id', '=', $orderPackage->id)
            ->first();

        if (empty($orderPackageRealCost)) {
            $orderPackage->realCostsForCompany()->create([
                'order_package_id' => $orderPackage->id,
                'deliverer_id' => $delivery->id,
                'cost' => PriceFormatter::asAbsolute(PriceFormatter::fromString($payIn->rzeczywisty_koszt_transportu_brutto))
            ]);

            return true;
        }

        return false;
    }

    /**
     * Save new transaction
     *
     * @param Order $order Order object
     * @param ShippingPayInCsvDataDTO $data Additional data
     * @return ?Transaction
     *
     */
    private function saveTransaction(Order $order, ShippingPayInCsvDataDTO $data): ?Transaction
    {
        $identifier = 's-' . strtotime($data->data_nadania_otrzymania) . '-' . $order->id;
        $existingTransaction = Transaction::where('payment_id', $identifier)->first();

        if (!empty($existingTransaction)) {
            return null;
        }

        try {
            return Transaction::create([
                'customer_id' => $order->customer_id,
                'posted_in_system_date' => new DateTime($data->data_nadania_otrzymania),
                'posted_in_bank_date' => new DateTime(),
                'payment_id' => $identifier,
                'kind_of_operation' => 'wpłata pobraniowa',
                'order_id' => $order->id,
                'operator' => $data->symbol_spedytora,
                'operation_value' => $data->wartosc_pobrania,
                'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data->wartosc_pobrania,
                'accounting_notes' => '',
                'transaction_notes' => 'Numer listu: ' . $data->numer_listu . ' Nr faktury:' . $data->nr_faktury_do_ktorej_dany_lp_zostal_przydzielony,
                'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL,
            ]);
        } catch (Exception $exception) {
            Log::notice('Błąd podczas zapisu transakcji: ' . $exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            return null;
        }
    }

    /**
     * Calculate balance
     *
     * @param integer $customerId Customer id
     * @return float
     */
    private function getCustomerBalance(int $customerId): float
    {
        $lastCustomerTransaction = Transaction::findWhere('customer_id', $customerId)->first();

        return $lastCustomerTransaction?->balance ?? 0;
    }

    /**
     * Settle promise.
     *
     * @param Order $order Order object.
     * @param ShippingPayInCsvDataDTO $payIn Pay in row.
     */
    private function settlePromisePayments(Order $order, ShippingPayInCsvDataDTO $payIn): void
    {
        $promisePayments = $order->payments()
            ->where('promise', '=', '1')
            ->where('notices', 'like', 'spedycja-pobranie')
            ->whereNull('deleted_at')
            ->get();

        foreach ($promisePayments as $payment) {
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
     * @param Transaction $transaction Transaction.
     */
    private function settleOrders(Collection $orders, Transaction $transaction): void
    {
        $amount = PriceFormatter::asAbsolute(PriceFormatter::fromString($transaction->operation_value));

        foreach ($orders as $order) {
            $orderBookedPaymentSum = $order->bookedPaymentsSum();
            $amountOutstanding = $this->getTotalOrderValue($order) - $orderBookedPaymentSum;

            if ($amount == 0 || $amountOutstanding == 0) {
                continue;
            }

            if ($amountOutstanding > 0) {
                if (bccomp($amount, $amountOutstanding, 3) >= 0) {
                    $paymentAmount = $amountOutstanding;
                } else {
                    $paymentAmount = $amount;
                }

                $transfer = $this->saveTransfer($order, $transaction, $paymentAmount);
                $payment = $order->payments()->create([
                    'transaction_id' => $transfer->id,
                    'amount' => $paymentAmount,
                    'type' => 'CLIENT',
                    'promise' => '',
                ]);

                if ($payment instanceof OrderPayment) {
                    OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);
                }

                $amount -= $paymentAmount;
            }
        }
    }

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
     */
    private function saveTransfer(Order $order, Transaction $transaction, float $amount, bool $back = false): ?Transaction
    {
        $identifier = 'ps-' . strtotime($transaction->posted_in_bank_date->format('Y-m-d H:i:s')) . '-' . $order->id;
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', $identifier)->first();

        if (!empty($existingTransaction)) {
            return null;
        }

        return Transaction::create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new DateTime(),
            'payment_id' => $identifier,
            'kind_of_operation' => 'przeksięgowanie wpłaty pobraniowej',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => -$amount,
            'balance' => (float)$this->getCustomerBalance($order->customer_id) - (float)$amount,
            'accounting_notes' => '',
            'transaction_notes' => $transaction->transaction_notes,
            'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL,
        ]);
    }

    private function settleProvider(ShippingPayInCsvDataDTO $payIn, $transaction): void
    {
        $providerBalance = $this->providerTransactionRepositoryEloquent->getBalance($payIn->symbol_spedytora);
        $providerBalanceOnInvoice = $this->providerTransactionRepositoryEloquent->getBalanceOnInvoice(
            $payIn->symbol_spedytora,
            $payIn->nr_faktury_do_ktorej_dany_lp_zostal_przydzielony,
        );

        $existingTransaction = ProviderTransactions::getProviderTransactionByProviderAndInvoiceNumberAndCashOnDelivery(
            $payIn->symbol_spedytora,
            $payIn->nr_faktury_do_ktorej_dany_lp_zostal_przydzielony,
            $payIn->wartosc_pobrania,
        );

        if (!empty($existingTransaction)) {
            return;
        }

        ProviderTransaction::create([
            'provider' => $payIn->symbol_spedytora,
            'waybill_number' => $payIn->numer_listu,
            'invoice_number' => $payIn->nr_faktury_do_ktorej_dany_lp_zostal_przydzielony,
            'order_id' => $transaction->order_id,
            'cash_on_delivery' => $payIn->wartosc_pobrania,
            'provider_balance' => $providerBalance + (float)$transaction->operation_value,
            'provider_balance_on_invoice' => $providerBalanceOnInvoice + (float)$transaction->operation_value,
            'transaction_id' => $transaction->id,
        ]);
    }
}

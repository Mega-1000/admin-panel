<?php

namespace App\Jobs;

use App\Domains\DelivererPackageImport\PriceFormatter;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Entities\Transaction;
use App\Helpers\PdfCharactersHelper;
use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\DelivererRepositoryEloquent;
use App\Repositories\OrderPackageRepositoryEloquent;
use App\Repositories\ProviderTransactionRepositoryEloquent;
use App\Repositories\TransactionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportShippingPayIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Transaction
     */
    protected $transactionRepository;

    /**
     * @var OrderPackageRepositoryEloquent
     */
    protected $orderPackageRepositoryEloquent;

    /**
     * @var DelivererRepositoryEloquent
     */
    protected $delivererRepositoryEloquent;

    /**
     * @var ProviderTransactionRepositoryEloquent
     */
    protected $providerTransactionRepositoryEloquent;

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
     * @return void
     */
    public function handle(
        TransactionRepository                 $transaction,
        OrderPackageRepositoryEloquent        $orderPackageRepositoryEloquent,
        DelivererRepositoryEloquent           $delivererRepositoryEloquent,
        ProviderTransactionRepositoryEloquent $providerTransactionRepositoryEloquent
    ): void
    {
        $this->transactionRepository = $transaction;
        $this->orderPackageRepositoryEloquent = $orderPackageRepositoryEloquent;
        $this->delivererRepositoryEloquent = $delivererRepositoryEloquent;
        $this->providerTransactionRepositoryEloquent = $providerTransactionRepositoryEloquent;

        $header = NULL;
        $fileName = 'shippingTransactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $data = array();
        $i = 0;
        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 5000, ';')) !== FALSE) {
                if (count(array_filter($row)) < 5) {
                    continue;
                }

                if (!$header) {
                    foreach ($row as &$headerName) {
                        if (!empty($headerName)) {
                            $headerName = iconv('ISO-8859-2', 'UTF-8', $headerName);
                            $headerName = snake_case(PdfCharactersHelper::changePolishCharactersToNonAccented($headerName));
                        }
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

        foreach ($data as $payIn) {
            try {
                $orderPackage = $this->findOrderByLetterNumber($payIn['numer_listu']);

                if (!empty($orderPackage)) {
                    $realCost = $this->addRealCost($orderPackage, $payIn);
                    if (!$realCost) {
                        continue;
                    }
                    $order = $orderPackage->order;
                } else {
                    fputcsv($file, $payIn);
                    continue;
                }
                if (!empty($order)) {
                    if (empty($payIn['wartosc_pobrania'])) {
                        continue;
                    }
                    $payIn['rzeczywisty_koszt_transportu_brutto'] = (float)str_replace(',', '.', $payIn['rzeczywisty_koszt_transportu_brutto']);
                    $transaction = $this->saveTransaction($order, $payIn);
                    if ($transaction === null) {
                        continue;
                    }
                    $this->settlePromisePayments($order, $payIn);
                    $orders = $this->getRelatedOrders($order);

                    $this->settleOrders($orders, $transaction);
                    $this->settleProvider($payIn, $transaction);
                } else {
                    fputcsv($file, $payIn);
                }
            } catch (\Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage() . ' w lini ' . $exception->getLine(), ['line' => __LINE__, 'file' => __FILE__]);
            }
        }
        fclose($file);
        Storage::disk('local')->put('public/transaction/shippingTransactionWithoutOrder' . date('Y-m-d') . '.csv', file_get_contents($fileName));
    }

    private function settleProvider($payIn, $transaction)
    {
        $providerBalance = $this->providerTransactionRepositoryEloquent->getBalance($payIn['symbol_spedytora']);
        $providerBalanceOnInvoice = $this->providerTransactionRepositoryEloquent->getBalanceOnInvoice($payIn['symbol_spedytora'], $payIn['nr_faktury_do_ktorej_dany_lp_zostal_przydzielony']);
        $existingTransaction = $this->providerTransactionRepositoryEloquent->select()
            ->where('provider', '=', $payIn['symbol_spedytora'])
            ->where('invoice_number', '=', $payIn['nr_faktury_do_ktorej_dany_lp_zostal_przydzielony'])
            ->where('cash_on_delivery', '=', $payIn['wartosc_pobrania'])
            ->first();
        if ($existingTransaction !== null) {
            return null;
        }

        $this->providerTransactionRepositoryEloquent->create([
            'provider' => $payIn['symbol_spedytora'],
            'waybill_number' => $payIn['numer_listu'],
            'invoice_number' => $payIn['nr_faktury_do_ktorej_dany_lp_zostal_przydzielony'],
            'order_id' => $transaction->order_id,
            'cash_on_delivery' => $payIn['wartosc_pobrania'],
            'provider_balance' => $providerBalance + (float)$transaction->operation_value,
            'provider_balance_on_invoice' => $providerBalanceOnInvoice + (float)$transaction->operation_value,
            'transaction_id' => $transaction->id,
        ]);
    }

    private function addRealCost(OrderPackage $orderPackage, array $payIn): bool
    {
        $delivery = $this->delivererRepositoryEloquent->findByField('name', $payIn['symbol_spedytora'])->first();
        if (empty($delivery)) {
            return false;
        }
        $cost = PriceFormatter::asAbsolute(PriceFormatter::fromString($payIn['rzeczywisty_koszt_transportu_brutto']));
        $orderPackageRealCost = $orderPackage->realCostsForCompany()
            ->where('deliverer_id', '=', $delivery->id)
            ->where('cost', '=', $cost)
            ->where('order_package_id', '=', $orderPackage->id)
            ->first();

        if (empty($orderPackageRealCost)) {
            $orderPackage->realCostsForCompany()->create([
                'order_package_id' => $orderPackage->id,
                'deliverer_id' => $delivery->id,
                'cost' => PriceFormatter::asAbsolute(PriceFormatter::fromString($payIn['rzeczywisty_koszt_transportu_brutto']))
            ]);
            return true;
        } else {
            return false;
        }
    }

    private function findOrderByLetterNumber(string $letterNumber): ?OrderPackage
    {
        return $this->orderPackageRepositoryEloquent->findWhere([
            'letter_number' => $letterNumber,
        ])->first();
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
        $promisePayments = $order->payments()
            ->where('promise', '=', '1')
            ->where('notices', 'like', 'spedycja-pobranie')
            ->whereNull('deleted_at')
            ->get();

        foreach ($promisePayments as $payment) {
            if ($payIn['kwota'] === (float)$payment->amount) {
                $payment->delete();
            } else {
                dispatch_now(new AddLabelJob($order->id, [128]));
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
     * @param Transaction $transaction Transaction.
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
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
     * Save new transaction
     *
     * @param Order $order Order object
     * @param array $data Additional data
     * @return ?Transaction
     *
     * @throws \Exception
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransaction(Order $order, array $data): ?Transaction
    {
        $identifier = 's-' . strtotime($data['data_nadania_otrzymania']) . '-' . $order->id;
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', $identifier)->first();
        if ($existingTransaction !== null) {
            return null;
        }

        try {
            return $this->transactionRepository->create([
                'customer_id' => $order->customer_id,
                'posted_in_system_date' => new \DateTime($data['data_nadania_otrzymania']),
                'posted_in_bank_date' => new \DateTime(),
                'payment_id' => $identifier,
                'kind_of_operation' => 'wpłata pobraniowa',
                'order_id' => $order->id,
                'operator' => $data['symbol_spedytora'],
                'operation_value' => $data['wartosc_pobrania'],
                'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data['wartosc_pobrania'],
                'accounting_notes' => '',
                'transaction_notes' => 'Numer listu: ' . $data['numer_listu'] . ' Nr faktury:' . $data['nr_faktury_do_ktorej_dany_lp_zostal_przydzielony'],
            ]);
        } catch (\Exception $exception) {
            Log::notice('Błąd podczas zapisu transakcji: ' . $exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            return null;
        }
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
     * Tworzy transakcje przeksięgowania
     *
     * @param Order $order
     * @param Transaction $transaction
     * @param float $amount
     * @param boolean $back
     * @return Transaction
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransfer(Order $order, Transaction $transaction, float $amount, bool $back = false): ?Transaction
    {
        $identifier = 'ps-' . strtotime($transaction->posted_in_bank_date->format('Y-m-d H:i:s')) . '-' . $order->id;
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', $identifier)->first();
        if ($existingTransaction !== null) {
            return null;
        }
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new \DateTime(),
            'payment_id' => $identifier,
            'kind_of_operation' => 'przeksięgowanie wpłaty pobraniowej',
            'order_id' => $order->id,
            'operator' => 'SYSTEM',
            'operation_value' => -$amount,
            'balance' => (float)$this->getCustomerBalance($order->customer_id) - (float)$amount,
            'accounting_notes' => '',
            'transaction_notes' => $transaction->transaction_notes,
        ]);
    }
}

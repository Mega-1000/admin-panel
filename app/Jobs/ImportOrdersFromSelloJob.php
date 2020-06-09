<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\SelTransaction;
use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\TaskTime;
use App\Entities\Warehouse;
use App\Helpers\GetCustomerForSello;
use App\Helpers\LabelsHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceOverrider;
use App\Helpers\SelloPackageDivider;
use App\Helpers\SelloPriceCalculator;
use App\Helpers\SelloTransportSumCalculator;
use App\Helpers\TaskTimeHelper;
use App\Http\Controllers\OrdersPaymentsController;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportOrdersFromSelloJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const DEFAULT_WAREHOUSE = 'MEGA-OLAWA';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = Carbon::now();
        $taskPrimal = Task::create([
            'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
            'user_id' => User::OLAWA_USER_ID,
            'created_by' => 1,
            'name' => 'Grupa zadań - ' . $date->format('d-m'),
            'color' => Task::DEFAULT_COLOR,
            'status' => Task::WAITING_FOR_ACCEPT
        ]);
        $taskSalaryDetails = TaskSalaryDetails::create([
            'task_id' => $taskPrimal->id,
            'consultant_value' => 0,
            'warehouse_value' => 0
        ]);

        $transactions = SelTransaction::all()->groupBy('tr_CheckoutFormPaymentId');
        $count = $transactions->reduce(function ($count, $transactionGroup) use ($taskPrimal) {
            $isGroup = !empty($transactionGroup->firstWhere('tr_Group', 1));
            if ($isGroup) {
                $transaction = $transactionGroup->firstWhere('tr_Group', 1);
            } else {
                $transaction = $transactionGroup->first();
            }
            if (Order::where('sello_id', $transaction->id)->count() > 0) {
                return $count;
            }
            $transactionArray = $this->createAddressArray($transaction);
            $tax = 1 + env('VAT');
            $products = $transactionGroup
                ->filter(function ($transaction) { return $transaction->tr_Group != 1; })
                ->map(function ($singleTransaction) use ($tax) {
                    if ($singleTransaction->transactionItem->itemExist()) {
                        $symbol = explode('-', $singleTransaction->transactionItem->item->it_Symbol);
                        $newSymbol = [$symbol[0], $symbol[1], '0'];
                        $newSymbol = join('-', $newSymbol);
                        $product = Product::where('symbol', $newSymbol)->first();
                    }
                    if (empty($product)) {
                        $product = Product::getDefaultProduct();
                    }
                    $product->transaction_id = $singleTransaction->id;
                    $product->tt_quantity = $singleTransaction->transactionItem->tt_Quantity;
                    $product->total_price = $singleTransaction->tr_Payment - $singleTransaction->tr_DeliveryCost;
                    $product->price_override = [
                        'gross_selling_price_commercial_unit' => $singleTransaction->transactionItem->tt_Price,
                        'net_selling_price_commercial_unit' => $singleTransaction->transactionItem->tt_Price / $tax
                    ];
                    return $product;
                });

            $orderItems = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'amount' => $product->tt_quantity,
                    'transactionId' => $product->transaction_id
                ];
            })->toArray();
            $transactionArray['order_items'] = $orderItems;
            try {
                DB::beginTransaction();
                $this->buildOrder($transaction, $transactionArray, $products, $transactionGroup, $taskPrimal->id);
                $count++;
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $message = $exception->getMessage();
                Log::error("Problem with sello import: $message", ['class' => $exception->getFile(), 'line' => $exception->getLine(), 'stack' => $exception->getTraceAsString()]);
            }
            return $count;
        }, 0);

        if ($count > 0) {
            $time = ceil($count * 2 / 5) * 5;
            $time = TaskTimeHelper::getFirstAvailableTime($time);
            TaskTime::create([
                'task_id' => $taskPrimal->id,
                'date_start' => $time['start'],
                'date_end' => $time['end']
            ]);
        } else {
            $taskPrimal->delete();
            $taskSalaryDetails->delete();
        }
    }

    private function setAdressArray($transaction, array $transactionArray): array
    {
        if ($transaction->deliveryAddress) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->deliveryAddress, $transaction->customer);
        } else if ($transaction->deliveryAddressBefore) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->deliveryAddressBefore, $transaction->customer);
        } else if ($transaction->defaultAdress) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->defaultAdress, $transaction->customer);
        } else if ($transaction->defaultAdressBefore) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->defaultAdressBefore, $transaction->customer);
        }
        $transactionArray['delivery_address']['email'] = $transactionArray['customer_login'];

        if ($transaction->invoiceAddress) {
            $transactionArray['invoice_address'] = $this->setDeliveryAddress($transaction->invoiceAddress, $transaction->customer);
        } else if ($transaction->invoiceAddressBefore) {
            $transactionArray['invoice_address'] = $this->setDeliveryAddress($transaction->invoiceAddressBefore, $transaction->customer);
        } else if ($transactionArray['delivery_address']) {
            $transactionArray['invoice_address'] = $transactionArray['delivery_address'];
        }
        $transactionArray['invoice_address']['email'] = $transactionArray['customer_login'];
        return $transactionArray;
    }

    private function buildOrder($transaction, array $transactionArray, $products, $group, $taskPrimalId)
    {
        $calculator = new SelloPriceCalculator();

        $calculator->setProductList($products);

        $packageBuilder = new SelloPackageDivider();
        $packageBuilder->setTransactionList($group);

        $prices = [];
        foreach ($products as $product) {
            $prices[$product->id] = $product->price_override;
        }

        $priceOverrider = new OrderPriceOverrider($prices);

        $transportPrice = new SelloTransportSumCalculator();
        $transportPrice->setTransportPrice($transaction->tr_DeliveryCost);

        $orderBuilder = new OrderBuilder();
        $orderBuilder
            ->setPackageGenerator($packageBuilder)
            ->setPriceCalculator($calculator)
            ->setPriceOverrider($priceOverrider)
            ->setTotalTransportSumCalculator($transportPrice)
            ->setUserSelector(new GetCustomerForSello());

        ['id' => $id, 'canPay' => $canPay] = $orderBuilder->newStore($transactionArray);

        $order = Order::find($id);
        $order->sello_id = $transaction->id;
        $user = User::where('name', '001')->first();
        $order->employee()->associate($user);
        $withWarehouse = $products->filter(function ($prod) {
           return !empty($prod->packing->warehouse_physical);
        });
        $warehouseSymbol = $withWarehouse->first()->packing->warehouse_physical ?? self::DEFAULT_WAREHOUSE;
        $warehouse = Warehouse::where('symbol', $warehouseSymbol)->first();
        $order->warehouse()->associate($warehouse);

        $order->save();
        if ($transaction->tr_Paid) {
            $this->createPaymentPromise($order, $transaction);
            $this->setLabels($order, $taskPrimalId);
        }
    }

    private function setDeliveryAddress($address, $customer): array
    {
        list($name, $surname) = $this->getNameFromAdrres($address);
        $street = $address->adr_Address1;
        $street = substr($street, 2);

        $flatNr = '';
        $numberStart = false;
        foreach (str_split($street) as $char) {
            if (is_numeric($char) || $numberStart) {
                $flatNr .= $char;
                $numberStart = true;
            }
        }
        $streetName = str_replace($flatNr, '', $address->adr_Address1);
        $addressArray['city'] = $address->adr_City;
        $addressArray['firstname'] = $name;
        $addressArray['lastname'] = $surname;
        $addressArray['flat_number'] = $flatNr;
        $addressArray['address'] = $streetName;
        $addressArray['nip'] = $address->adr_NIP ?: $customer->cs_NIP ?: '';
        $addressArray['postal_code'] = $address->adr_ZipCode;
        $addressArray['nip'] = $address->adr_NIP;
        $addressArray['firmname'] = $address->adr_Company ?: $customer->cs_Company ?: '';
        $addressArray['phone'] = $address->adr_PhoneNumber ?: $customer->phone->cp_Phone ?: '';
        return $addressArray;
    }

    private function getNameFromAdrres($deliveryAddress): array
    {
        $adressName = explode(' ', $deliveryAddress->adr_Name);
        if (sizeof($adressName) == 2) {
            $name = $adressName[0];
            $surname = $adressName[1];
        } else {
            $name = join('', $adressName);
            $surname = '';
        }
        return array($name, $surname);
    }

    private function createPaymentPromise(Order $order, $transaction)
    {
        $amount = $transaction->tr_Payment;
        OrdersPaymentsController::payOrder($order->id, $amount,
            null, 1,
            null, Carbon::today()->addDay(7)->toDateTimeString());
    }

    private function setLabels($order, $taskPrimalId)
    {
        $preventionArray = [];
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::FINISH_LOGISTIC_LABEL_ID], $preventionArray, LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID));
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID], $preventionArray, []));
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::WAIT_FOR_SPEDITION_FOR_ACCEPT_LABEL_ID], $preventionArray, []));
        if ($order->warehouse->id == Warehouse::OLAWA_WAREHOUSE_ID) {
            dispatch_now(new RemoveLabelJob($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::WAIT_FOR_WAREHOUSE_TO_ACCEPT]));
            $order->createNewTask(5, $taskPrimalId);
        } else {
            dispatch_now(new RemoveLabelJob($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::SEND_TO_WAREHOUSE_FOR_VALIDATION]));
        }
    }

    /**
     * @param $transaction
     * @return array
     */
    private function createAddressArray($transaction): array
    {
        $transactionArray = [];
        $transactionArray['customer_login'] = str_replace('allegromail.pl', 'mega1000.pl', $transaction->customer->email->ce_email);
        $phone = preg_replace('/[^0-9]/', '', $transaction->customer->phone->cp_Phone);
        $pos = strpos($phone, '48');
        if ($pos === 0) {
            $phone = substr($phone, 2);
        }
        $transactionArray['phone'] = $phone;
        $transactionArray['update_email'] = true;
        $transactionArray['customer_notices'] = empty($transaction->note) ? '' : $transaction->note->ne_Content;
        $transactionArray = $this->setAdressArray($transaction, $transactionArray);
        $transactionArray['is_standard'] = 1;
        $transactionArray['rewrite'] = 0;
        $transactionArray['nick_allegro'] = $transaction->customer->cs_Nick;
        return $transactionArray;
    }
}

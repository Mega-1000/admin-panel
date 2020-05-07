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
        $transactions = SelTransaction::all()->groupBy('tr_CheckoutFormPaymentId');
        $transactions->map(function ($transactionGroup) {
            $isGroup = !empty($transactionGroup->firstWhere('tr_Group', 1));
            if ($isGroup) {
                $transaction = $transactionGroup->firstWhere('tr_Group', 1);
            } else {
                $transaction = $transactionGroup->first();
            }
            if (Order::where('sello_id', $transaction->id)->count() > 0) {
                return;
            }
            $transactionArray = $this->createAddressArray($transaction);
            $products = $transactionGroup->map(function ($singleTransaction) {
                if ($singleTransaction->transactionItem->itemExist()) {
                    $symbol = explode('-', $singleTransaction->transactionItem->item->it_Symbol);
                    $newSymbol = [$symbol[0], $symbol[1], '0'];
                    $newSymbol = join('-', $newSymbol);
                    $product = Product::where('symbol', $newSymbol)->first();
                }
                if (empty($product)) {
                    $product = Product::getDefaultProduct();
                }
                $product->tt_quantity = $singleTransaction->transactionItem->tt_Quantity;
                $product->total_price = $singleTransaction->tr_Payment - $singleTransaction->tr_DeliveryCost;
                $product->price_override = ['gross_selling_price_commercial_unit' => $singleTransaction->transactionItem->tt_Price];
                return $product;
            });

            $orderItems = $products->map(function ($product) {
                $item = [];
                $item['id'] = $product->id;
                $item['amount'] = $product->tt_quantity;
                return $item;
            })->toArray();
            $transactionArray['order_items'] = $orderItems;
            try {
                DB::beginTransaction();
                $this->buildOrder($transaction, $transactionArray, $products, $transactionGroup);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $message = $exception->getMessage();
                Log::error("Problem with sello import: $message", ['class' => $exception->getFile(), 'line' => $exception->getLine(), 'stack' => $exception->getTraceAsString()]);
            }
        });
    }

    private function setAdressArray($transaction, array $transactionArray): array
    {
        if ($transaction->deliveryAddress) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->deliveryAddress);
        } else if ($transaction->deliveryAddressBefore) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->deliveryAddressBefore);
        } else if ($transaction->defaultAdress) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->defaultAdress);
        } else if ($transaction->defaultAdressBefore) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->defaultAdressBefore);
        }
        $transactionArray['delivery_address']['email'] = $transactionArray['customer_login'];

        if ($transaction->invoiceAddress) {
            $transactionArray['invoice_address'] = $this->setDeliveryAddress($transaction->invoiceAddress);
        } else if ($transaction->invoiceAddressBefore) {
            $transactionArray['invoice_address'] = $this->setDeliveryAddress($transaction->invoiceAddressBefore);
        } else if ($transactionArray['delivery_address']) {
            $transactionArray['invoice_address'] = $transactionArray['delivery_address'];
        }
        $transactionArray['invoice_address']['email'] = $transactionArray['customer_login'];
        return $transactionArray;
    }

    private function buildOrder($transaction, array $transactionArray, $products, $group)
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
        $warehouseSymbol = $withWarehouse->first()->packing->warehouse_physical ?? 'MEGA-OLAWA';
        $warehouse = Warehouse::where('symbol', $warehouseSymbol)->first();
        $order->warehouse()->associate($warehouse);

        $order->save();
        if ($transaction->tr_Paid) {
            $this->createPaymentPromise($order, $transaction);
            $this->setLabels($order);
        }
    }

    private function setDeliveryAddress($address): array
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
        $addressArray['nip'] = $address->adr_NIP ?: '';
        $addressArray['postal_code'] = $address->adr_ZipCode;
        $addressArray['nip'] = $address->adr_NIP;
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

    private function setLabels($order)
    {
        $preventionArray = [];
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::FINISH_LOGISTIC_LABEL_ID], $preventionArray, LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID));
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::TRANSPORT_SPEDITION_INIT_LABEL_ID], $preventionArray, []));
        dispatch_now(new RemoveLabelJob($order, [LabelsHelper::WAIT_FOR_SPEDITION_FOR_ACCEPT_LABEL_ID], $preventionArray, []));
        if ($order->warehouse->id == Warehouse::OLAWA_WAREHOUSE_ID) {
            dispatch_now(new RemoveLabelJob($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::WAIT_FOR_WAREHOUSE_TO_ACCEPT]));
            $this->createNewTask($order);
        } else {
            dispatch_now(new RemoveLabelJob($order, [LabelsHelper::VALIDATE_ORDER], $preventionArray, [LabelsHelper::SEND_TO_WAREHOUSE_FOR_VALIDATION]));
        }
    }

    /**
     * @param Order $order
     */
    private function createNewTask(Order $order): void
    {
        $date = Carbon::now();
        $task = Task::create([
            'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
            'user_id' => User::OLAWA_USER_ID,
            'order_id' => $order->id,
            'created_by' => 1,
            'name' => $order->id . ' - ' . $date->format('d-m'),
            'color' => Task::DEFAULT_COLOR,
            'status' => Task::WAITING_FOR_ACCEPT
        ]);
        $time = TaskTimeHelper::getFirstAvailableTime(5);
        TaskTime::create([
            'task_id' => $task->id,
            'date_start' => $time['start'],
            'date_end' => $time['end']
        ]);
        TaskSalaryDetails::create([
            'task_id' => $task->id,
            'consultant_value' => 0,
            'warehouse_value' => 0
        ]);
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
        return $transactionArray;
    }
}

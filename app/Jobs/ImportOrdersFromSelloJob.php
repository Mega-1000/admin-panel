<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\Payment;
use App\Entities\Product;
use App\Entities\SelTransaction;
use App\Entities\Task;
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
        $transactions = SelTransaction::all();
        $transactions->map(function ($transaction) {
            $transactionArray = [];
            if (Order::where('sello_id', $transaction->id)->count() > 0) {
                return;
            }
            $transactionArray['customer_login'] = str_replace('allegromail.pl', 'mega1000.pl', $transaction->customer->email->ce_email);
            $phone = preg_replace('/[^0-9]/', '', $transaction->customer->phone->cp_Phone);
            $pos =  strpos($phone, '48');
            if ($pos === 0) {
                $phone = substr($phone, 2);
            }
            $transactionArray['phone'] = $phone;
            $transactionArray['update_email'] = true;
            $transactionArray['customer_notices'] = empty($transaction->note) ? '' : $transaction->note->ne_Content;
            $transactionArray = $this->setAdressArray($transaction, $transactionArray);
            $transactionArray['is_standard'] = 1;
            $transactionArray['rewrite'] = 0;
            if ($transaction->transactionItem->itemExist()) {
                $symbol = explode('-', $transaction->transactionItem->item->it_Symbol);
                $newSymbol = [$symbol[0], $symbol[1], '0'];
                $newSymbol = join('-', $newSymbol);
                $product = Product::where('symbol', $newSymbol)->first();
            }

            if (empty($product)) {
                $product = Product::getDefaultProduct();
            }

            $orderItems = [];
            $item = [];
            $item['id'] = $product->id;
            $item['amount'] = $transaction->transactionItem->tt_Quantity;

            $orderItems [] = $item;
            $transactionArray['order_items'] = $orderItems;
            try {
                DB::beginTransaction();
                $this->buildOrder($transaction, $transactionArray, $product);
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
            $transactionArray['invoice_address']= $transactionArray['delivery_address'];
        }
        $transactionArray['invoice_address']['email'] = $transactionArray['customer_login'];
        return $transactionArray;
    }

    private function buildOrder($transaction, array $transactionArray, Product $product)
    {
        $calculator = new SelloPriceCalculator();
        $calculator->setOverridePrice($transaction->transactionItem->tt_Price);

        $packageBuilder = new SelloPackageDivider();
        $packageBuilder->setDelivererId($transaction->tr_DelivererId);
        $packageBuilder->setDeliveryId($transaction->tr_DeliveryId);
        $packageBuilder->setPackageNumber($transaction->tr_CheckoutFormCalculatedNumberOfPackages);

        $priceOverrider = new OrderPriceOverrider([$product->id => ['gross_selling_price_commercial_unit' => $transaction->transactionItem->tt_Price]]);

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
        $warehouseSymbol = $product->packing->warehouse_physical ?? 'MEGA-OLAWA';
        $warehouse = Warehouse::where('symbol', $warehouseSymbol)->first();
        $order->warehouse()->associate($warehouse);

        $order->save();
        if ($transaction->tr_Paid) {
            $this->payOrder($order, $transaction);
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

    private function payOrder(Order $order, $transaction)
    {
        $payment = new Payment();
        $payment->amount = $transaction->tr_Remittance;
        $payment->amount_left = $transaction->tr_Remittance;
        $payment->customer_id = $order->customer->id;
        $payment->notices = 'Płatność z Allegro';
        $payment->save();
        $promise = '';
        $chooseOrder = $order->id;
        OrdersPaymentsController::payOrder($order->id, $transaction->tr_Remittance,
            $payment->id, $promise,
            $chooseOrder, null);
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
    }
}

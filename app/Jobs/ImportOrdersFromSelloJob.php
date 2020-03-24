<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\SelTransaction;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceOverrider;
use App\Helpers\SelloPackageDivider;
use App\Helpers\SelloPriceCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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

            $transactionArray['phone'] = preg_replace('/[^0-9]/', '', $transaction->customer->phone->cp_Phone);
            $transactionArray['customer_notices'] = empty($transaction->note) ? '' : $transaction->note->ne_Content;
            $transactionArray = $this->setAdressArray($transaction, $transactionArray);
            $transactionArray['is_standard'] = true;
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
                $this->buildOrder($transaction, $transactionArray, $product);
            } catch (\Exception $exception) {
                $message = $exception->getMessage();
                Log::error("Problem with sello import: $message");
            }
        });
    }

    private function setAdressArray($transaction, array $transactionArray): array
    {
        if ($transaction->deliveryAddress) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->deliveryAddress);
        } else if ($transaction->deliveryAddressBefore) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->deliveryAddressBefore);
        }

        if ($transaction->invoiceAddress) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->invoiceAddress);
        } else if ($transaction->invoiceAddressBefore) {
            $transactionArray['delivery_address'] = $this->setDeliveryAddress($transaction->invoiceAddressBefore);
        } else if ($transactionArray['delivery_address']) {
            $transactionArray['invoice_address']= $transactionArray['delivery_address'];
        }
        return $transactionArray;
    }

    private function buildOrder($transaction, array $transactionArray, $product)
    {
        $calculator = new SelloPriceCalculator();
        $calculator->setOverridePrice($transaction->transactionItem->tt_Price);

        $packageBuilder = new SelloPackageDivider();
        $packageBuilder->setDelivererId($transaction->tr_DelivererId);
        $packageBuilder->setDeliveryId($transaction->tr_DeliveryId);
        $packageBuilder->setPackageNumber($transaction->tr_CheckoutFormCalculatedNumberOfPackages);

        $priceOverrider = new OrderPriceOverrider([$product->id => ['net_selling_price_commercial_unit' => $transaction->transactionItem->tt_Price]]);

        $orderBuilder = new OrderBuilder();
        $orderBuilder
            ->setPackageGenerator($packageBuilder)
            ->setPriceCalculator($calculator)
            ->setPriceOverrider($priceOverrider);
        ['id' => $id, 'canPay' => $canPay] = $orderBuilder->newStore($transactionArray);

        $order = Order::find($id);
        $order->sello_id = $transaction->id;
        $order->save();
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
        $StreetName = str_replace($flatNr, '', $address->adr_Address1);
        $addressArray['city'] = $address->adr_City;
        $addressArray['firstname'] = $name;
        $addressArray['lastname'] = $surname;
        $addressArray['flat_number'] = $flatNr;
        $addressArray['address'] = $StreetName;
        $addressArray['nip'] = $address->adr_NIP ?: '';
        $addressArray['postal_code'] = $address->adr_ZipCode;
        $addressArray['nip'] = $address->adr_NIP;
        $addressArray['address'] = $address->adr_Address1 . $address->adr_Address2;
        return $addressArray;
    }

    private function getNameFromAdrres($deliveryAddress): array
    {
        $adressName = explode(' ', $deliveryAddress->adr_Name);
        if (sizeof($adressName) == 2) {
            $name = $adressName[0];
            $surname = $adressName[1];
        } else {
            $name = $adressName;
            $surname = '';
        }
        return array($name, $surname);
    }
}

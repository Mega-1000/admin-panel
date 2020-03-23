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

            $symbol = explode('-', $transaction->transactionItem->item->it_Symbol);
            $newSymbol = [$symbol[0], $symbol[1], '0'];
            $newSymbol = join('-', $newSymbol);
            if ($transaction->transactionItem->itemExist()) {
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
            $transactionArray['delivery_address']['city'] = $transaction->deliveryAddress->adr_City;
            $transactionArray['delivery_address']['postal_code'] = $transaction->deliveryAddress->adr_ZipCode;
            $transactionArray['delivery_address']['nip'] = $transaction->deliveryAddress->adr_NIP;
            $transactionArray['delivery_address']['address'] = $transaction->deliveryAddress->adr_Address1 . $transaction->deliveryAddress->adr_Address2;
        } else if ($transaction->deliveryAddressBefore) {
            $transactionArray['delivery_address']['city'] = $transaction->deliveryAddressBefore->adr_City;
            $transactionArray['delivery_address']['postal_code'] = $transaction->deliveryAddressBefore->adr_ZipCode;
            $transactionArray['delivery_address']['nip'] = $transaction->deliveryAddressBefore->adr_NIP;
            $transactionArray['delivery_address']['address'] = $transaction->deliveryAddressBefore->adr_Address1 . $transaction->deliveryAddressBefore->adr_Address2;
        }

        if ($transaction->invoiceAddress) {
            $transactionArray['invoice_address']['city'] = $transaction->invoiceAddress->adr_City;
            $transactionArray['invoice_address']['postal_code'] = $transaction->invoiceAddress->adr_ZipCode;
            $transactionArray['invoice_address']['nip'] = $transaction->invoiceAddress->adr_NIP;
            $transactionArray['invoice_address']['address'] = $transaction->invoiceAddress->adr_Address1 . $transaction->invoiceAddress->adr_Address2;
        } else if ($transaction->invoiceAddressBefore) {
            $transactionArray['invoice_address']['city'] = $transaction->invoiceAddressBefore->adr_City;
            $transactionArray['invoice_address']['postal_code'] = $transaction->invoiceAddressBefore->adr_ZipCode;
            $transactionArray['invoice_address']['nip'] = $transaction->invoiceAddressBefore->adr_NIP;
            $transactionArray['invoice_address']['address'] = $transaction->invoiceAddressBefore->adr_Address1 . $transaction->invoiceAddressBefore->adr_Address2;
        } else if ($transactionArray['delivery_address']) {
            $transactionArray['invoice_address']['city'] = $transactionArray['delivery_address']['city'];
            $transactionArray['invoice_address']['postal_code'] = $transactionArray['delivery_address']['postal_code'];
            $transactionArray['invoice_address']['nip'] = $transactionArray['delivery_address']['nip'];
            $transactionArray['invoice_address']['address'] = $transactionArray['delivery_address']['address'];
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
}

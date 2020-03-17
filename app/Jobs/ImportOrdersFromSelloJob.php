<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\SelTransaction;
use App\Helpers\OrderBuilder;
use App\Helpers\SelloPackageDivider;
use App\Helpers\SelloPriceCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
                error_log(print_r(Order::where('sello_id', $transaction->id)->first(), 1));
                return;
            }
            $transactionArray['customer_login'] = $transaction->customer->email->ce_email;

            $transactionArray['phone'] = preg_replace('/[^0-9]/', '', $transaction->customer->phone->cp_Phone);
            $transactionArray['customer_notices'] = empty($transaction->note) ? '' : $transaction->note->ne_Content;
//            $transactionArray['delivery_address']['city'] = $transaction->adr_City;
//            $transactionArray['delivery_address']['postal_code'] = $transaction->adr_ZipCode;
            $transactionArray['is_standard'] = false;
            $transactionArray['rewrite'] = 0;

            if ($transaction->transactionItem->itemExist()) {
                $product = Product::where('symbol', $transaction->transactionItem->item->it_Symbol)->first();
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

            $calculator = new SelloPriceCalculator();
            $calculator->setOverridePrice($transaction->tr_Payment);
            $orderBuilder = new OrderBuilder();
            $orderBuilder
                ->setPackageGenerator(new SelloPackageDivider())
                ->setPriceCalculator($calculator);
            ['id' => $id, 'canPay' => $canPay] = $orderBuilder->newStore($transactionArray);
            $order = Order::find($id);
            $order->sello_id = $transaction->id;
            $order->save();
        });
    }
}

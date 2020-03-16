<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Helpers\OrderBuilder;
use App\Helpers\SelloPackageDivider;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

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
        $transactions = DB::table('sel_tr__transaction')
            ->join('sel_cs__customer', 'sel_tr__transaction.tr_CustomerId', '=', 'sel_cs__customer.id')
            ->join('sel_cs_custemail', 'sel_cs_custemail.ce_CustomerId', '=', 'sel_cs__customer.id')
            ->join('sel_cs_custphone', 'sel_cs_custphone.cp_CustomerId', '=', 'sel_cs__customer.id')
            ->join('sel_nt_note', 'sel_nt_note.ne_TransId', '=', 'sel_tr__transaction.id')
            ->join('sel_adr__address', 'sel_adr__address.adr_TransId', '=', 'sel_tr__transaction.id')
            ->join('sel_tr_item', 'sel_tr_item.tt_TransId', '=', 'sel_tr__transaction.id')
            ->get();

//        exit();
        $transactions->map(function ($transaction) {
            $transactionArray = [];
            if (Order::where('sello_id', $transaction->id)->count()) {
                return;
            }
            $transactionArray['customer_login'] = $transaction->ce_email;
            $transactionArray['phone'] = $transaction->cp_Phone;
            $transactionArray['customer_notices'] = $transaction->ne_Content;
            $transactionArray['delivery_address']['city'] = $transaction->adr_City;
            $transactionArray['delivery_address']['postal_code'] = $transaction->adr_ZipCode;
            $transactionArray['is_standard'] = false;
            $transactionArray['rewrite'] = 0;

//            $orderItems = [];
//            $item = [];
//            $item['id']
//            $item['amount']

//            $orderItems [] = $item;
//            $transactionArray['order_items'] = $orderItems;
//            $orderBuilder = new OrderBuilder();
//            $orderBuilder->setPackageGenerator(new SelloPackageDivider());
            error_log(print_r($transactionArray, 1));
            echo $transaction->id . PHP_EOL;
            exit();
        });
    }
}

<?php

namespace App\Jobs;

use App\Mail\DifferentCustomerData;
use App\Mail\InvoiceSent;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ValidateSubiekt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderRepository $orderRepository)
    {
        $toCheck = DB::table('gt_invoices')->where('created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString())->get();

        foreach($toCheck as $order){
            $invoiceRow = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '18')->first();
            $status12 = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '12')->first();
            $invoiceRowDelayed = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '11')->first();
            $noSymbol = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '10')->first();

            $noWarehouseItems = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '61')->first();
            $delayInvoice = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '60')->first();
            if(!empty($invoiceRow)) {
//                \Mailer::create()
//                    ->to($order->customer->login)
//                    ->send(new InvoiceSent('Faktura Mega1000', 'Faktura Mega1000', $invoiceRow->ftp_invoice_filename));
                dispatch_now(new AddLabelJob($order->order_id, [137]));
            } elseif(!empty($invoiceRowDelayed)) {
//                \Mailer::create()
//                    ->to($order->customer->login)
//                    ->send(new InvoiceSent('Faktura Mega1000', 'Faktura Mega1000', $invoiceRowDelayed->ftp_invoice_filename));
                dispatch_now(new RemoveLabelJob($order->order_id, [74]));
            } elseif(!empty($noSymbol)) {
                dispatch_now(new AddLabelJob($order->order_id, [101]));
            } elseif(!empty($status12)) {
                dispatch_now(new AddLabelJob($order->order_id, [124]));
            } elseif(!empty($noWarehouseItems)) {
                dispatch_now(new AddLabelJob($order->order_id, [120]));
            } elseif(!empty($delayInvoice)) {
                dispatch_now(new AddLabelJob($order->order_id, [42]));
            }
        }
    }
}

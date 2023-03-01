<?php

namespace App\Jobs;

use App\Repositories\OrderRepository;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ValidateSubiekt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected ?int $userId;

    public function __construct()
    {
        $this->userId = Auth::user()?->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderRepository $orderRepository)
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $toCheck = DB::table('gt_invoices')->where('created_at', '>', Carbon::now()->subMinutes(5)->toDateTimeString())->get();

        foreach ($toCheck as $order) {
            $invoiceRow = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '18')->first();
            $status12 = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '12')->first();
            $invoiceRowDelayed = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '11')->first();
            $noSymbol = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '10')->first();

            $noWarehouseItems = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '61')->first();
            $delayInvoice = DB::table('gt_invoices')->where('order_id', $order->order_id)->where('gt_invoice_status_id', '60')->first();
            $labelToAdd = 0;
            if (!empty($invoiceRow)) {
//                \Mailer::create()
//                    ->to($order->customer->login)
//                    ->send(new InvoiceSent('Faktura Mega1000', 'Faktura Mega1000', $invoiceRow->ftp_invoice_filename));
                $labelToAdd = 137;
            } elseif (!empty($invoiceRowDelayed)) {
//                \Mailer::create()
//                    ->to($order->customer->login)
//                    ->send(new InvoiceSent('Faktura Mega1000', 'Faktura Mega1000', $invoiceRowDelayed->ftp_invoice_filename));

                $loopPrevention = [];
                RemoveLabelService::removeLabels($order, [74], $loopPrevention, [], Auth::user()->id);
            } elseif (!empty($noSymbol)) {
                $labelToAdd = 101;
            } elseif (!empty($status12)) {
                $labelToAdd = 124;
            } elseif (!empty($noWarehouseItems)) {
                $labelToAdd = 120;
            } elseif (!empty($delayInvoice)) {
                $labelToAdd = 42;
            }

            if ($labelToAdd > 0) {
                $loopPrevention = [];
                AddLabelService::addLabels($order, [$labelToAdd], $loopPrevention, [], Auth::user()->id);
            }
        }
    }
}

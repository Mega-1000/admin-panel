<?php

namespace App\Jobs;

use App\Mail\InvoiceSent;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChangeOrderInvoiceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
    public function handle(OrderRepository $orderRepository, OrderAddressRepository $orderAddressRepository)
    {
        $orders = $orderRepository->all();

        foreach ($orders as $order) {
            $delivery = [
                $order->getDeliveryAddress()->firstname,
                $order->getDeliveryAddress()->lastname,
                $order->getDeliveryAddress()->firmname,
                $order->getDeliveryAddress()->nip,
                $order->getDeliveryAddress()->phone,
                $order->getDeliveryAddress()->address,
                $order->getDeliveryAddress()->flat_number,
                $order->getDeliveryAddress()->city,
                $order->getDeliveryAddress()->postal_code,
                $order->getDeliveryAddress()->email,
            ];

            $invoice = [
                $order->getInvoiceAddress()->firstname,
                $order->getInvoiceAddress()->lastname,
                $order->getInvoiceAddress()->firmname,
                $order->getInvoiceAddress()->nip,
                $order->getInvoiceAddress()->phone,
                $order->getInvoiceAddress()->address,
                $order->getInvoiceAddress()->flat_number,
                $order->getInvoiceAddress()->city,
                $order->getInvoiceAddress()->postal_code,
                $order->getInvoiceAddress()->email,
            ];

            $orderIsSent = DB::table('order_labels')->where([['order_id', '=', $order->id], 'label_id' => 66])->get();
            $orderHasFilledData = DB::table('order_labels')->where([['order_id', '=', $order->id], 'label_id' => 136])->get();
            $invoiceIsSet = DB::table('order_labels')->where([['order_id', '=', $order->id], 'label_id' => 137])->get();
            $today = Carbon::today();
            $orderDate = Carbon::parse($order->shipment_date);
            if ($today->day == $orderDate->day + 7) {
                if (count($orderIsSent) > 0 && count($orderHasFilledData) == 0 && count($invoiceIsSet) == 0 && $order->shipment_date < Carbon::now()->subMonth()->endOfMonth()->toDateTimeString()) {
                    $invoice = DB::table('gt_invoices')->where([['order_id', '=', $order->id], ['gt_invoice_status_id', '=', '13']])->get();
                    if (count($invoice) > 0) {
                        $subiektData = DB::table('gt_addresses_to_check')->where([['gt_invoices_id', '=', $invoice[0]->id]])->get();
                        if (count($subiektData) > 0) {
                            $invoiceAddress = $orderAddressRepository->findWhere(['order_id' => $order->id, 'type' => 'INVOICE_ADDRESS'])->first();
                            $orderAddressRepository->update([
                                'firstname' => $subiektData[0]->firstname,
                                'lastname' => $subiektData[0]->lastname,
                                'firmname' => $subiektData[0]->firmname,
                                'nip' => $subiektData[0]->nip,
                                'phone' => $subiektData[0]->phone,
                                'address' => $subiektData[0]->address,
                                'flat_number' => $subiektData[0]->flat_number,
                                'city' => $subiektData[0]->city,
                                'postal_code' => $subiektData[0]->postal_code,
                                'email' => $subiektData[0]->email,
                            ], $invoiceAddress->id);
                        } else {
                            $invoiceAddress = $orderAddressRepository->findWhere(['order_id' => $order->id, 'type' => 'INVOICE_ADDRESS'])->first();
                            $orderAddressRepository->update([
                                'firstname' => $delivery[0],
                                'lastname' => $delivery[1],
                                'firmname' => $delivery[2],
                                'nip' => $delivery[3],
                                'phone' => $delivery[4],
                                'address' => $delivery[5],
                                'flat_number' => $delivery[6],
                                'city' => $delivery[7],
                                'postal_code' => $delivery[8],
                                'email' => $delivery[9],
                            ], $invoiceAddress->id);
                        }
                    } else {
                        $invoiceAddress = $orderAddressRepository->findWhere(['order_id' => $order->id, 'type' => 'INVOICE_ADDRESS'])->first();
                        $orderAddressRepository->update([
                            'firstname' => $delivery[0],
                            'lastname' => $delivery[1],
                            'firmname' => $delivery[2],
                            'nip' => $delivery[3],
                            'phone' => $delivery[4],
                            'address' => $delivery[5],
                            'flat_number' => $delivery[6],
                            'city' => $delivery[7],
                            'postal_code' => $delivery[8],
                            'email' => $delivery[9],
                        ], $invoiceAddress->id);
                    }
                    dispatch(new AddLabelJob($order->id, [135]));
                }
            }
        }
    }
}

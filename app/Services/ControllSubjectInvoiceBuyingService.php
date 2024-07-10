<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;

class ControllSubjectInvoiceBuyingService
{
    public array $orders = [];

    /**
     * @param array<ControllSubjectInvoiceDTO> $data
     * @return void
     */
    public function handle(array $data): void
    {
        foreach ($data as $orderNotes) {
            $this->handleSingle($orderNotes);
        }

        $orders = Order::whereHas('labels', function ($query) {$query->where('labels.id', 263);})->get();


        foreach ($orders as $order) {

            $sumOfPurchase = 0;

            foreach ($order->items as $item) {
                $pricePurchase = $item['net_purchase_price_commercial_unit_after_discounts'] ?? 0;
                $quantity = $item['quantity'] ?? 0;
                $sumOfPurchase += floatval($pricePurchase) * intval($quantity);
            }

            $totalItemsCost = $sumOfPurchase * 1.23;
            $transportCost = $order->shipment_price_for_us;

            $totalItemsCost += $transportCost;

            $totalGross = BuyingInvoice::where('order_id', $order->id)->sum('value');
            $arr = [];

            if ($order->labels->contains('id', 65) && $totalGross == $totalItemsCost) {
                AddLabelService::addLabels($order, [264], $arr, []);
                RemoveLabelService::removeLabels($order, [263], $arr , [], auth()->id());
            } else {
                AddLabelService::addLabels($order, [263], $arr, []);
                RemoveLabelService::removeLabels($order, [264], $arr , [], auth()->id());
            }
        }
    }

    private function groupDataByOrder(array $data): array
    {
        $grouped = [];
        foreach ($data as $dto) {
            $orderNotes = preg_replace('/\D/', '', $dto->notes);
            $grouped[$orderNotes][] = $dto;
        }
        return $grouped;
    }

    private function handleSingle(ControllSubjectInvoiceDTO $orderNotes): void
    {
        $order = Order::find(preg_replace('/\D/', '', $orderNotes->notes ));

        if (!$order) {
            return;
        }

        if (BuyingInvoice::where('invoice_number', $orderNotes->number)->exists()) {
            return;
        }

        $buyingInvooice = new BuyingInvoice();
        $buyingInvooice->order_id = $order->id;
        $buyingInvooice->value = (float)str_replace(',', '.', str_replace(' ', '', $orderNotes->gross));
        $buyingInvooice->invoice_number = $orderNotes->number;
        $buyingInvooice->save();

        echo $order->id;


        $this->orders[] = $order;
    }
}

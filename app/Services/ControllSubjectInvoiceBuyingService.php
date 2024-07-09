<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\Order;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;

class ControllSubjectInvoiceBuyingService
{
    /**
     * @param array<ControllSubjectInvoiceDTO> $data
     * @return void
     */
    public function handle(array $data): void
    {
        foreach ($data as $orderNotes) {
            $this->handleSingle($orderNotes);
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

    private function handleSingle(array $orderNotes): void
    {
        dd($orderNotes);
        $buyingInvooice = new BuyingInvoice();
        $buyingInvooice->order_id = $orderNotes;
        $buyingInvooice->value = $orderNotes;
        $buyingInvooice->invoice_number = $orderNotes->gross;
        $buyingInvooice->save();


        $order = Order::find($orderNotes);
        $arr = [];

        if (!$order) {
            return;
        }

        $sumOfPurchase = 0;
        $items = $order->items;

        foreach ($items as $item) {
            $pricePurchase = $item['net_purchase_price_commercial_unit_after_discounts'] ?? 0;
            $quantity = $item['quantity'] ?? 0;
            $sumOfPurchase += floatval($pricePurchase) * intval($quantity);
        }
        $totalItemsCost = round($sumOfPurchase * 1.23, 2) + $order->shipment_price_for_us;

        $totalGross = 0;
        foreach ($invoices as $invoice) {
            $totalGross += (float)str_replace(' ', '', $invoice->gross);
        }

        if ($order->labels->contains('id', 65) && $totalGross == $totalItemsCost) {
            AddLabelService::addLabels($order, [264], $arr, []);
            RemoveLabelService::removeLabels($order, [263], $arr , [], auth()->id());
        } else {
            AddLabelService::addLabels($order, [263], $arr, []);
            RemoveLabelService::removeLabels($order, [264], $arr , [], auth()->id());
        }
    }
}

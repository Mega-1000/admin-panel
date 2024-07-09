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

        foreach ($this->orders as $order) {
            $totalItemsCost = $order->items->sum(function ($item) {
                return $item->quantity * $item->net_purchase_price_commercial_unit_after_discounts;
            });

            $totalGross = BuyingInvoice::where('order_id', $order->id)->sum('value');

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
        $order = Order::find(preg_replace('/\D/', '', $orderNotes->notes));

        if (BuyingInvoice::where('invoice_number', $orderNotes->number)->where('value', $orderNotes->number)->exists()) {
            return;
        }

        $buyingInvooice = new BuyingInvoice();
        $buyingInvooice->order_id = $order->id;
        $buyingInvooice->value = $orderNotes->gross;
        $buyingInvooice->invoice_number = $orderNotes->number;
        $buyingInvooice->save();


        $this->orders[] = $order;
    }
}

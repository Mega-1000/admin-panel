<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Helpers\RecalculateBuyingLabels;
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
            RecalculateBuyingLabels::recalculate($order);
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

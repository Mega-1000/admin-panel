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
        foreach ($data as $dto) {
            $this->handleSingle($dto);
        }
    }

    private function handleSingle(ControllSubjectInvoiceDTO $row): void
    {
        $row->notes = preg_replace('/\D/', '', $row->notes);

        $order = Order::find($row->notes);
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
        $totalItemsCost = $sumOfPurchase * 1.23;

        if ($order->id == 85234) {
            dd($totalItemsCost, $row->gross);
        }

        if ($order->labels->has(65) && $row->gross == $totalItemsCost) {
            AddLabelService::addLabels($order, [264], $arr, []);
            RemoveLabelService::removeLabels($order, [263], $arr , [], auth()->id());
        } else {
            AddLabelService::addLabels($order, [263], $arr, []);
            RemoveLabelService::removeLabels($order, [264], $arr , [], auth()->id());
        }
    }
}

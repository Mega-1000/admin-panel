<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\Order;
use App\Entities\OrderInvoiceValue;
use App\Repositories\OrderInvoiceValues;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;

class ControllSubjectInvoiceService
{
    public array $report = [];
    public array $orders = [];

    /**
     * @param array<ControllSubjectInvoiceDTO> $data
     * @return array
     */
    public function handle(array $data): array
    {
        foreach ($data as $dto) {
            $this->handleSingle($dto);
        }

        foreach ($this->orders as $orderId) {
            $this->attachLabels($orderId);
        }

        return $this->report;
    }

    private function handleSingle(ControllSubjectInvoiceDTO $dto): void
    {
        $grossInvoiceValue = $dto->value;
        $notes = $dto->notes;

        $regex = '/^\d{5}/';

        if (preg_match($regex, $notes, $matches)) {
             $order = Order::find($matches[0]);

             if (!$order) {
                    $this->addToReport($dto);
                    return;
             }
        } else {
            $this->addToReport($dto);
            return;
        }

        OrderInvoiceValue::where('order_id', $order->id)->where('value', $grossInvoiceValue)->first()?§->delete();

        OrderInvoiceValue::create([
            'order_id' => $order->id,
            'value' => $grossInvoiceValue
        ]);

        $this->orders[] = $order->id;
    }

    public function addToReport(ControllSubjectInvoiceDTO $dto): void
    {
        $this->report[] = [
            collect($dto)
        ];
    }

    public function attachLabels(int $orderId): void
    {
        $order = Order::find($orderId);

        $orderInvoiceValuesSum = OrderInvoiceValues::getSumOfInvoiceValuesByOrder($order);
        $orderValue = $order->getValue() - Orders::getOrderReturnGoods($order) - Orders::getSumOfWTONPayments($order);
        $arr = [];
        if (round($orderInvoiceValuesSum, 2) != round($orderValue, 2)) {
            AddLabelService::addLabels($order, [231],$arr, []);

            $order->labels()->detach(232);
            return;
        }

        AddLabelService::addLabels($order, [232],$arr, []);
        $order->labels()->detach(231);
    }
}

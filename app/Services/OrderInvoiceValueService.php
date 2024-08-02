<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\Order;
use App\Entities\OrderInvoiceValue;

final class OrderInvoiceValueService
{
    /**
     * @param ControllSubjectInvoiceDTO $dto
     * @param Order $order
     * @return OrderInvoiceValue
     */
    public static function createFromDTO(ControllSubjectInvoiceDTO $dto, Order $order): OrderInvoiceValue
    {
        if (
            OrderInvoiceValue::where('invoice_number', $dto->number)->exists() &&
            OrderInvoiceValue::where('invoice_number', $dto->number)->first()->value === $dto->value
        ) {
            return OrderInvoiceValue::where('invoice_number', $dto->number)->first();
        }

        if (str_contains($dto->number, 'FL')) {
            $order->labels()->detach(288);
            $order->labels()->detach(291);
            $order->labels()->detach(292);
        }

        return OrderInvoiceValue::create([
            'order_id' => $order->id,
            'value' => $dto->value,
            'invoice_number' => $dto->number,
            'issue_date' => $dto->issueDate,
            'type' => 'selling',
        ]);
    }

    /**
     * @param ControllSubjectInvoiceDTO $dto
     * @param OrderInvoiceValue $orderInvoiceValue
     * @return void
     */
    public static function updateFromDTO(ControllSubjectInvoiceDTO $dto, OrderInvoiceValue $orderInvoiceValue): void
    {
        $orderInvoiceValue->update([
            'value' => $dto->value,
            'issue_date' => $dto->issueDate,
        ]);
    }
}

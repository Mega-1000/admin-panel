<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\Order;
use App\Entities\OrderInvoiceValue;

final class OrderInvoiceValueService
{
    public static function createFromDTO(ControllSubjectInvoiceDTO $dto, Order $order): OrderInvoiceValue
    {
        return OrderInvoiceValue::create([
            'order_id' => $order->id,
            'value' => $dto->value,
            'invoice_number' => $dto->number,
        ]);
    }

    public static function updateFromDTO(ControllSubjectInvoiceDTO $dto, OrderInvoiceValue $orderInvoiceValue): void
    {
        $orderInvoiceValue->update([
            'value' => $dto->value,
            'issue_date' => $dto->issueDate,
        ]);
    }
}

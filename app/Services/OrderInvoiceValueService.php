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
        dd($dto);
        if (OrderInvoiceValue::where('invoice_number', $dto->number)->exists() && ) {
            return OrderInvoiceValue::where('invoice_number', $dto->number)->first();
        }

        return dd(OrderInvoiceValue::create([
            'order_id' => $order->id,
            'value' => $dto->value,
            'invoice_number' => $dto->number,
            'issue_date' => $dto->issueDate,
            'type' => request()->get('invoice-kind') === 'faktury sprzedazy' ? 'selling' : 'buying',
        ]));
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

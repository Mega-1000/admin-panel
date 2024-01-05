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
    public const NOTES_FOR_CONTINUE = [
        'OK',
        'ok',
        'Ok',
        'magazyn',
        'Magazyn',
    ];

    /**
     * @param array<ControllSubjectInvoiceDTO> $data
     * @return array
     */
    public function handle(array $data): array
    {
        dd($data, $this->orders);
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
        $notes = $dto->notes;

        if (in_array(explode(' ', $notes)[0], self::NOTES_FOR_CONTINUE)) {
            return;
        }

        $regex = '/^\d{5}/';

        if (preg_match($regex, $notes, $matches)) {
             $order = Order::find($matches[0]);

            if (!$order) {
                $this->addToReport($dto);
                return;
            }

             if ($value = $order->invoiceValues()->where('invoice_number', $dto->number)->first()) {
                 OrderInvoiceValueService::updateFromDTO($dto, $value);
                 return;
             }
        } else {
            $this->addToReport($dto);
            return;
        }

        OrderInvoiceValueService::createFromDTO($dto, $order);

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
        CalculateSubjectInvoiceBilansLabels::handle(
            Order::find($orderId),
        );
    }
}

<?php

namespace App\DTO\AllegroPayment;

use App\Entities\Order;

class AllegroReturnDTO
{
    /**
     * @param string $paymentId
     * @param string $reason
     * @param array $lineItems
     * @param bool $addShipmentPrice
     * @param Order $order
     */
    public function __construct(
        public string $paymentId,
        public mixed $reason = 'zwrot',
        public array $lineItems,
        public bool $addShipmentPrice = false,
        public Order $order,
    ) {}

    public function toAllegroRefundArray(): array
    {
        if (empty($this->reason)) {
            $this->reason = 'zwrot';
        }


        return [
            'payment' => [
                'id' => $this->paymentId
            ],
            'reason' => 'REFUND',
            'lineItems' => array_map(function (AllegroReturnItemDTO $item) {
                return $item->toAllegroRefundArray();
            }, $this->lineItems),
            'message' => 'zwrot',
            'delivery' => [
                'value' => [
                    'amount' => $this->addShipmentPrice ? $this->order->shipment_price_for_client : 0,
                    'currency' => 'PLN'
                ],
            ],
        ];
    }
}

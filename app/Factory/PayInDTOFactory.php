<?php

namespace App\Factory;

use App\DTO\PayInDTO;

class PayInDTOFactory
{
    /**
     * @param int|null $orderId
     * @param array $data
     * @param string|null $message
     *
     * @return PayInDTO
     */
    public static function createPayInDTO(array $options = []): PayInDTO
    {
        $orderId = $options['orderId'] ?? null;
        $data = $options['data'];
        $message = $options['message'] ?? null;

        return new PayInDTO(
            orderId: $orderId,
            data: $data,
            message: $message
        );
    }
}

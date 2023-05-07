<?php

namespace App\DTO;

use App\Entities\Order;

class PayInDTO
{
    public $orderId;
    public $data;

    public function __construct($orderId, $data)
    {
        $this->orderId = $orderId;
        $this->data = $data;
    }
}

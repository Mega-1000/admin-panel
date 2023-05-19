<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class AddingTaskResponseDTO extends BaseDTO implements JsonSerializable
{
    public function __construct(
        private string $status, 
        private int $id, 
        private int $deliveryWarehouse, 
        private string $message
    )
    {
        $this->status = $status;
        $this->id = $id;
        $this->deliveryWarehouse = $deliveryWarehouse;
        $this->message = $message;
    }

    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'id' => $this->id,
            'delivery_warehouse' => $this->deliveryWarehouse,
            'message' => $this->message
        ];
    }
}

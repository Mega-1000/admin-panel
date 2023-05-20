<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class AddingTaskResponseDTO extends BaseDTO implements JsonSerializable
{
    public function __construct(
        private readonly string $status,
        private readonly int    $taskId,
        private readonly int    $deliveryWarehouseId,
        private readonly string $message
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => $this->status,
            'id' => $this->taskId,
            'delivery_warehouse' => $this->deliveryWarehouseId,
            'message' => $this->message
        ];
    }
}

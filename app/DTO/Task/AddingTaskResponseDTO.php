<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class AddingTaskResponseDTO extends BaseDTO implements JsonSerializable
{
    private $status;
    private $id;
    private $delivery_warehouse;
    private $message;

    public function __construct(string $status, int $id, int $delivery_warehouse, string $message)
    {
        $this->status = $status;
        $this->id = $id;
        $this->delivery_warehouse = $delivery_warehouse;
        $this->message = $message;
    }

    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'id' => $this->id,
            'delivery_warehouse' => $this->delivery_warehouse,
            'message' => $this->message
        ];
    }
}
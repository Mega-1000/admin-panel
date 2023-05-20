<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class SeparatorDTO extends BaseDTO implements JsonSerializable
{
    public function __construct(
        private readonly ?int   $id,
        private readonly int    $resourceId,
        private readonly string $title,
        private readonly string $start,
        private readonly string $end,
        private readonly string $color,
        private readonly string $text,
        private readonly ?int   $customOrderId,
        private readonly ?int   $customTaskId
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'resourceId' => $this->resourceId,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'color' => $this->color,
            'text' => $this->text,
            'customOrderId' => $this->customOrderId,
            'customTaskId' => $this->customTaskId
        ];
    }
}

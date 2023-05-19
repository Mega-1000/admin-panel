<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class SeparatorDTO extends BaseDTO implements JsonSerializable
{
    public function __construct(
        private ?int $id,
        private int $resourceId,
        private string $title,
        private string $start,
        private string $end,
        private string $color,
        private string $text,
        private ?int $customOrderId,
        private ?int $customTaskId
    )
    {
        $this->id = $id;
        $this->resourceId = $resourceId;
        $this->title = $title;
        $this->start = $start;
        $this->end = $end;
        $this->color = $color;
        $this->text = $text;
        $this->customOrderId = $customOrderId;
        $this->customTaskId = $customTaskId;
    }

    public function jsonSerialize()
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

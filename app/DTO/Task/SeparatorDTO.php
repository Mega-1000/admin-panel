<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class SeparatorDTO extends BaseDTO implements JsonSerializable
{
    private $id;
    private $resourceId;
    private $title;
    private $start;
    private $end;
    private $color;
    private $text;
    private $customOrderId;
    private $customTaskId;

    public function __construct(
        ?int $id,
        int $resourceId,
        string $title,
        string $start,
        string $end,
        string $color,
        string $text,
        ?int $customOrderId,
        ?int $customTaskId
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

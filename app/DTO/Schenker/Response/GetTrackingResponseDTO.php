<?php

namespace App\DTO\Schenker\Response;

use App\DTO\BaseDTO;

class GetTrackingResponseDTO extends BaseDTO
{

    private $eventDescription;
    private $eventType;
    private $eventCode;
    private $orderNumber;

    public function __construct(int $orderNumber, string $eventDescription, string $eventType, string $eventCode)
    {
        $this->orderNumber = $orderNumber;
        $this->eventDescription = $eventDescription;
        $this->eventCode = $eventCode;
        $this->eventType = $eventType;
    }

    public function getEventDescription(): string
    {
        return $this->eventDescription;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getEventCode(): string
    {
        return $this->eventCode;
    }

    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

}

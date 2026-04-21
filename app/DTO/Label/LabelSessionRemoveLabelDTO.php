<?php

namespace App\DTO\Label;

use App\Entities\Order;

readonly class LabelSessionRemoveLabelDTO
{

    public function __construct(
        private Order $order,
        private array $labelIdsToRemove,
        private array $loopPreventionArray,
        private array $customLabelIdsToAddAfterRemoval,
    ) {}

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getLabelIdsToRemove(): array
    {
        return $this->labelIdsToRemove;
    }

    public function getLoopPreventionArray(): array
    {
        return $this->loopPreventionArray;
    }

    public function getCustomLabelIdsToAddAfterRemoval(): array
    {
        return $this->customLabelIdsToAddAfterRemoval;
    }

}

<?php

namespace App\DTO\Label;

use App\Entities\Order;

class LabelSessionRemoveLabelDTO
{

    public function __construct(
        private readonly Order $order,
        private readonly array $labelIdsToRemove,
        private readonly array $loopPreventionArray,
        private readonly array $customLabelIdsToAddAfterRemoval,
    )
    {
    }

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

<?php
declare(strict_types=1);

namespace App\Services;

use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;

class LabelService
{
    public function removeLabel(int $orderId, array $labelsToRemove): void
    {
        dispatch_now(new RemoveLabelJob($orderId, $labelsToRemove));
    }

    public function dispatchLabelEventByNameJob(int $orderId, string $eventName): void
    {
        dispatch_now(new DispatchLabelEventByNameJob($orderId, $eventName));
    }
}

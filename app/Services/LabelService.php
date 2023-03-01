<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Order;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Services\Label\RemoveLabelService;
use Illuminate\Support\Facades\Auth;

class LabelService
{
    public function removeLabel(int $orderId, array $labelsToRemove): void
    {
        /** @var Order $order */
        $order = Order::query()->findOrFail($orderId);
        $preventionArray = [];
        RemoveLabelService::removeLabels($order, $labelsToRemove, $preventionArray, [], Auth::user()->id);
    }

    public function dispatchLabelEventByNameJob(int $orderId, string $eventName): void
    {
        dispatch_now(new DispatchLabelEventByNameJob($orderId, $eventName));
    }
}

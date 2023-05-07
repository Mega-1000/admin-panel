<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Order;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Services\Label\AddLabelService;
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
        /** @var Order $order */
        $order = Order::query()->findOrFail($orderId);
        dispatch(new DispatchLabelEventByNameJob($order, $eventName));
    }

    public function addLabel(int $orderId, int $labelId): void
    {
        /** @var Order $order */
        $order = Order::query()->findOrFail($orderId);
        $preventionArray = [];
        AddLabelService::addLabels($order, [$labelId], $preventionArray, [], Auth::user()->id);
    }
}

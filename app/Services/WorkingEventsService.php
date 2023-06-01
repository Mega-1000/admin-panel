<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\WorkingEvents;
use App\User;
use Illuminate\Support\Facades\Auth;

class WorkingEventsService
{
    /**
     * Create event.
     *
     * @param string $event
     * @param int|null $orderId
     * @return WorkingEvents|null
     */
    public static function createEvent(string $event, int $orderId = null): ?WorkingEvents
    {
        $now = new \DateTime();
        if (empty(Auth::user()) || !(Auth::user() instanceof User)) {
            return null;
        }

        if (!($latest = WorkingEvents::latest()->first())) {
            return null;
        }

        if ($now->diff($latest->created_at)->i < 2 && $latest->event == $event) {
            return null;
        }

        return WorkingEvents::create([
            'user_id' => Auth::user()->id,
            'event' => $event,
            'order_id' => $orderId
        ]);

    }
}

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
        $order = Order::find($orderId);
        $now = new \DateTime();

        if (!empty($order)) {
            $eventMap = [
                WorkingEvents::ORDER_UPDATE_EVENT => 'Zaktualizowano zamówienie',
                WorkingEvents::ORDER_PACKAGES_UPDATE_EVENT => 'Edytowano paczkę',
                WorkingEvents::ORDER_PACKAGES_CREATE_EVENT => 'Dodano paczkę',
                WorkingEvents::LABEL_ADD_EVENT => 'Dodano etykietę',
                WorkingEvents::LABEL_REMOVE_EVENT => 'Usunięto etykietę',
                WorkingEvents::ORDER_PAYMENT_STORE_EVENT => ['log' => 'payments_log', 'message' => 'Dodano płatność'],
                WorkingEvents::ORDER_PAYMENT_UPDATE_EVENT => ['log' => 'payments_log', 'message' => 'Zaktualizowano płatność']
            ];

            if (array_key_exists($event, $eventMap)) {
                $logName = 'labels_log';
                $message = $eventMap[$event];

                if (is_array($eventMap[$event])) {
                    $logName = $eventMap[$event]['log'];
                    $message = $eventMap[$event]['message'];
                }

                $order->$logName .= PHP_EOL . $now->format('Y-m-d H:i:s') . ' - ' . Auth::user()->name . ' - ' . $message;
                $order->saveQuietly();
            }
        }


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

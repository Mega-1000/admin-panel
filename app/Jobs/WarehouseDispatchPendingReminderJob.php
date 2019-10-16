<?php

namespace App\Jobs;

use App\Mail\WarehouseDispatchPendingReminderMail;
use App\Repositories\OrderWarehouseNotificationRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class WarehouseDispatchPendingReminderJob extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository)
    {
        $now = new Carbon('now');

        if ($this->canNotifyNow($now)) {
            $warehousesToRemind = $orderWarehouseNotificationRepository->findWhere(['waiting_for_response' => true]);

            if (!empty($warehousesToRemind)) {
                foreach ($warehousesToRemind as $warehouseNotification) {
                    if ($this->shouldNotifyWithEmail($warehouseNotification, $now)) {
                        dispatch_now(new OrderStatusChangedToDispatchNotificationJob($warehouseNotification->order_id));
                    }
                }
            }
        }
    }

    protected function shouldNotifyWithEmail($orderWarehouseNotification, $now)
    {
        return $orderWarehouseNotification->created_at->diff($now)->h >= 2; //only schedules that wait longer then 2h
    }

    protected function canNotifyNow($now)
    {
        if (!($now->dayOfWeek == 6 || $now->dayOfWeek == 0)) {  //not Saturday nor Sunday
            if ($now->hour >= 7 && $now->hour < 17) {           //only between 9AM and 5PM
                return true;
            }
        }

        return false;
    }
}

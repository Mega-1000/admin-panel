<?php

namespace App\Jobs\Orders;

use App\Helpers\DateHelper;
use App\Jobs\AddLabelJob;
use App\Jobs\Job;
use App\Jobs\RemoveLabelJob;
use App\Repositories\OrderLabelSchedulerRepository;
use Carbon\Carbon;

class TriggerOrderLabelSchedulersJob extends Job
{
    /** @var DateHelper */
    protected $dateHelper;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderLabelSchedulerRepository $orderLabelSchedulerRepository, DateHelper $dateHelper)
    {
        $this->dateHelper = $dateHelper;
        $now = new Carbon();

        $schedules = $orderLabelSchedulerRepository->findWhere([
            "triggered_at" => null,
            ["trigger_time", "<", $now],
        ]);

        if (!count($schedules)) {
            return;
        }

        foreach ($schedules as $schedule) {

            if (!$this->canTriggerByType($schedule->type, $now)) {
                continue;
            }


            if (!$schedule->order->hasLabel($schedule->label_id) && ($schedule->type !== "C" && !empty($schedule->label_id))) {
                $schedule->delete();
                continue;
            }

            if (substr($schedule->action, 0, 6) == "to_add") {
                $options = [];
                if ($schedule->type == "C") {
                    $options['added_type'] = $schedule->type;
                }
                $preventionArray = [];
                dispatch_now(new AddLabelJob($schedule->order_id, [$schedule->label_id_to_handle], $preventionArray, $options));
            } else {
                dispatch_now(new RemoveLabelJob($schedule->order_id, [$schedule->label_id_to_handle]));
            }

            $schedule->triggered_at = $now;
            $schedule->save();
        }
    }

    protected function canTriggerByType($type, Carbon $date)
    {
        if (!$this->dateHelper->isThatDateWorkingDay($date)) {
            return false;
        }

        if ($type == "A") {
            if (!$this->dateHelper->isTimeBetween(7, 21)) {
                return false;
            }
        }

        return true;
    }
}

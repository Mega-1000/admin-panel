<?php

namespace App\Helpers;

use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\TaskTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TaskHelper
{
    /**
     * @param Collection $newGroup
     * @param $task
     * @param $duration
     * @param $data
     */
    public static function createNewGroup(\Illuminate\Support\Collection $newGroup, $task, $duration, $data = false): void
    {
        $name = $newGroup->map(function ($item) {
            return $item->order_id;
        })->toArray();
        $taskNew = Task::create([
            'warehouse_id' => $task->warehouse_id,
            'user_id' => $task->user_id,
            'created_by' => $task->created_by,
            'name' => implode(', ', $name),
            'color' => $task->color,
            'status' => $task->status
        ]);
        $time = TaskTimeHelper::getFirstAvailableTime($duration, $data);
        TaskTime::create([
            'task_id' => $taskNew->id,
            'date_start' => $time['start'],
            'date_end' => $time['end']
        ]);
        TaskSalaryDetails::create([
            'task_id' => $taskNew->id,
            'consultant_value' => 0,
            'warehouse_value' => 0
        ]);
        $newGroup->map(function ($item) use ($taskNew) {
            $item->parent_id = $taskNew->id;
            $item->save();
        });
    }

    /**
     * @param $task
     * @param $duration
     * @param $data
     */
    public static function updateAbandonedTaskTime($task, $duration, $data = false): void
    {
        $taskTime = $task->taskTime;
        $time = TaskTimeHelper::getFirstAvailableTime($duration, $data);
        $taskTime->date_start = $time['start'];
        $taskTime->date_end = $time['end'];
        $taskTime->save();
        $task->parent_id = null;
        $task->save();
    }

    /**
     * Group tasks by shipment date
     *
     * @param Builder $taskCourierQuery
     * @return Collection
     */
    public static function groupTaskByShipmentDate(Builder $taskCourierQuery)
    {
        $result = [];
        $today = Carbon::today()->addDay(-1);
        foreach ($taskCourierQuery->get() as $task) {
            $orderDate = Carbon::parse($task->order->shipment_date);
            if ($today->isSameDay($orderDate)) {
                $result[][] = $task->id;
            } elseif ($today->addDay()->isSameDay($orderDate)) {
                $result[1][] = $task->id;
            } elseif ($today->addDay(2)->isSameDay($orderDate)) {
                $result[2][] = $task->id;
            } elseif ($today->addDay(3)->isSameDay($orderDate)) {
                $result[3][] = $task->id;
            } else {
                $result[4][] = $task->id;
            }
        }

        return $result;
    }
}

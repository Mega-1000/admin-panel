<?php

namespace App\Helpers;

use App\Entities\Label;
use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\TaskTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class TaskHelper
{
    /**
     * @param \Illuminate\Support\Collection $newGroup
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
     * Get Separator
     * @param $user_id
     * @param $id
     * @param $start
     * @param $end
     * @return Collection<TaskTime>
     */
    public static function getSeparator(int $user_id,int $id, string $start, string $end): Collection
    {
        return TaskTime::with('task')
        ->whereHas('task',
            function ($query) use ($user_id,$id) {
                $query->where('user_id', $user_id);
                $query->where('warehouse_id', $id);
                $query->whereNull('parent_id');
                $query->whereNull('rendering');
            })
        ->whereDate('date_start', '>=', $start)
        ->whereDate('date_end', '<=', $end)
        ->whereNull('transfer_date')
        ->orderBy('date_start', 'asc')
        ->get();
    }

    /**
     * Transfers Task
     * @param int $user_id
     * @return Collection<Task>
     */
    public static function getOpenUserTask(int $user_id): Collection
    {
        return Task::where('user_id', $user_id)
        ->whereHas('order', function ($query) {
            $query->whereDoesntHave('labels', function ($query) {
                $query
                    ->where('labels.id', Label::ORDER_ITEMS_CONSTRUCTED)
                    ->orWhere('labels.id', Label::ORDER_ITEMS_REDEEMED_LABEL);
            })->whereHas('dates', function ($query) {
                $query->orderBy('consultant_shipment_date_to');
            });
        })->get();
    }

}

<?php

namespace App\Helpers;

use App\Entities\Task;
use App\Entities\TaskSalaryDetails;
use App\Entities\TaskTime;
use Illuminate\Support\Collection;

class TaskHelper
{
    public static function createOrUpdateTask(Collection $newGroup, Task $task, int $duration, array $data): Task
    {
        if ($newGroup->count() > 1) {
            return TaskHelper::createNewGroup($newGroup, $task, $duration, $data);
        }
        return TaskHelper::updateAbandonedTaskTime($newGroup->first(), $duration, $data);
    }

    public static function removeOrderIdFromParentName(Task $task): string
    {
        $parent = $task->parent->first();
        $explodedParentName = explode(',', $parent->name);
        if (($key = array_search($task->order_id, $explodedParentName)) !== false) {
            unset($explodedParentName[$key]);
        }
        return join(', ', $explodedParentName);
    }

    /**
     * @param Collection $newGroup
     * @param Task $task
     * @param int $duration
     * @param array $data
     * @return Task
     */
    private static function createNewGroup(Collection $newGroup, Task $task, int $duration, array $data = []): Task
    {
        $name = $newGroup->map(function ($item) {
            return $item->order_id;
        })->toArray();
        $taskNew = Task::create([
            'warehouse_id' => $task->warehouse_id,
            'user_id' => $task->user_id,
            'created_by' => $task->created_by,
            'name' => 'Grupa: ' . implode(', ', $name),
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

        return $taskNew;
    }

    /**
     * @param Task $task
     * @param int $duration
     * @param array $data
     * @return Task
     */
    private static function updateAbandonedTaskTime(Task $task, int $duration, array $data = []): Task
    {
        $taskTime = $task->taskTime;
        $time = TaskTimeHelper::getFirstAvailableTime($duration, $data);
        $taskTime->date_start = $time['start'];
        $taskTime->date_end = $time['end'];
        $taskTime->save();
        $task->parent_id = null;
        $task->save();

        return $task;
    }
}

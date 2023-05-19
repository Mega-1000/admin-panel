<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Task;
use App\Entities\TaskTime;
use App\Entities\Label;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class TaskTimes
{
    /**
     * Get Separator
     * 
     * @return Collection<TaskTime>
     */
    public static function getTimeLastTask(int $user_id, Carbon $date): Collection
    {
        return TaskTime::with(['task'])
        ->whereHas('task',
            function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
                $query->whereNull('parent_id');
                $query->whereNull('rendering');
            })
        ->where('date_start', 'like' , $date->format('Y-m-d')."%")
        ->whereNotNull('transfer_date')
        ->orderBy('date_start', 'asc')
        ->get();
    }

    /**
     * Get Separator
     * 
     * @return Collection<TaskTime>
     */
    public static function getTimeLastNowTask(int $user_id, Carbon $date): Collection
    {
        return TaskTime::with(['task'])
        ->whereHas('task',
            function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
                $query->whereNull('parent_id');
                $query->whereNull('rendering');
            })
        ->where('date_start', 'like' , $date->format('Y-m-d')."%")
        ->whereNull('transfer_date')
        ->orderBy('date_start', 'asc')
        ->get();
    }
    
    /**
     * Get Move Task
     * 
     * @return Collection<TaskTime>
     */
    public static function getMoveTask(int $user_id, Carbon $date): Collection
    {
        return TaskTime::with(['task'])
                ->whereHas('task',
                    function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                        $query->whereNull('parent_id');
                        $query->whereNull('rendering');
                    })
                ->where('date_start', 'like' , $date->format('Y-m-d')."%")
                ->whereNull('transfer_date')
                ->orderBy('date_start', 'asc')
                ->get();
    }

    /**
     * Move Task
     * 
     * @return Collection<TaskTime>
     */
     public static function movingTasksBackward(Task $task, TaskTime $taskTime, Carbon $date_start): Collection
     {
        return TaskTime::with(['task'])
            ->whereHas('task',
                function ($query) use ($task) {
                    $query->where('user_id', $task->user_id);
                    $query->whereNull('parent_id');
                    $query->whereNull('rendering');
                })
            ->where('date_start', 'like' , $date_start->format('Y-m-d')."%")
            ->where('date_start', '>', $taskTime->date_start)
            ->where('id', '!=', $taskTime->id)
            ->orderBy('date_start', 'asc')
            ->get();
     }
}

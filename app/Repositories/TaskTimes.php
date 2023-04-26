<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\TaskTime;
use App\Entities\Label;
use Illuminate\Database\Eloquent\Collection;

class TaskTimes
{
    /**
     * Get Separator
     * @param int $user_id
     * @param int $id
     * @param string $start
     * @param string $end
     */
    public static function getSeparator($user_id,$id,$start,$end): Collection
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
     * Get Separator
     * @param int $user_id
     * @param Carbon $date
     */
    public static function getTimeLastTask($user_id, $date): Collection
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
     * @param int $user_id
     * @param Carbon $date
     */
    public static function getTimeLastNowTask($user_id, $date): Collection
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
     * @param int $user_id
     * @param Carbon $date
     */
    public static function getMoveTask($user_id, $date): Collection
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
     * @param object $task
     * @param object $taskTime
     * @param Carbon $date_start
     */
     public static function movingTasksBackward($task,$taskTime,$date_start): Collection
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

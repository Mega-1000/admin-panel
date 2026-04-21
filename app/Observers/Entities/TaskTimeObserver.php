<?php

namespace App\Observers\Entities;

use App\Entities\TaskTime;
use Illuminate\Support\Facades\Log;

class TaskTimeObserver
{
    /**
     * Handle the task time "created" event.
     *
     * @param TaskTime $taskTime
     * @return void
     */
    public function created(TaskTime $taskTime)
    {
        Log::notice('Kontrola czasu crated', ['line' => __LINE__, 'file' => __FILE__, 'start' => $taskTime->date_start, 'end' => $taskTime->date_end]);
    }

    /**
     * Handle the task time "updated" event.
     *
     * @param TaskTime $taskTime
     * @return void
     */
    public function updated(TaskTime $taskTime)
    {
        Log::notice('Kontrola czasu updated', ['uri'=> request()->getRequestUri(), 'start' => $taskTime->date_start, 'end' => $taskTime->date_end]);
    }

    /**
     * Handle the task time "deleted" event.
     *
     * @param TaskTime $taskTime
     * @return void
     */
    public function deleted(TaskTime $taskTime)
    {
        //
    }

    /**
     * Handle the task time "restored" event.
     *
     * @param TaskTime $taskTime
     * @return void
     */
    public function restored(TaskTime $taskTime)
    {
        //
    }

    /**
     * Handle the task time "force deleted" event.
     *
     * @param TaskTime $taskTime
     * @return void
     */
    public function forceDeleted(TaskTime $taskTime)
    {
    }
}

<?php

namespace App\Jobs;

use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckTasksFromYesterdayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TaskRepository $repository)
    {
        $tasks = $repository->with('taskTime')->whereHas('taskTime', function($query){
            $query->where('date_start', '<', Carbon::today());
        })->findWhere([['rendering','=', null], ['status', '!=', 'FINISHED']])->all();

        $today = Carbon::today();
        $today->addHours(7);
        foreach($tasks as $task){
            if($task->color == '32CD32' || $task->color == '008000'){
                $task->update([
                    'status' => 'FINISHED'
                ]);
                continue;
            }
            $task->update([
                'user_id' => '37',
                'status' => 'WAITING_FOR_ACCEPT'
            ]);
            $different = strtotime($task->taskTime->date_end) - strtotime($task->taskTime->date_start);
            $minutes = $different/60;
            $task->taskTime->update([
                'date_start' => $today->toDateTimeString(),
                'date_end' => $today->addMinutes($minutes)
            ]);
            if($task->order != null) {
                if (strtotime($task->order->shipment_date) < strtotime($task->taskTime->date_start)) {
                    $task->order->update([
                        'shipment_date' => $today,
                        'production_date' => $today,
                    ]);
                }
            }
        }
    }
}

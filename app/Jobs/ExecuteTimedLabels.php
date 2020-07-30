<?php

namespace App\Jobs;

use App\Entities\Label;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class ExecuteTimedLabels implements ShouldQueue
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
    public function handle()
    {
        $timedLabels = DB::table('timed_labels')->where('is_executed', 0)->where('execution_time', '<', Carbon::now())->get();

        foreach($timedLabels as $timedLabel) {
            dispatch_now(new RemoveLabelJob($timedLabel->order_id, [$timedLabel->label_id]));
            dispatch_now(new AddLabelJob($timedLabel->order_id, [Label::URGENT_INTERVENTION]));
            $timedLabel->update([
                'is_executed' => true
            ]);
        }
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\Label\RemoveLabelService;
use App\Entities\Label;
use App\Entities\Order;
use App\Services\Label\AddLabelService;

class TimedLabelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private int $timedLabelId,
        private int $labelId,
        private Order $order,
        private array $loopPreventionArray,
        private array $options,
        private ?int $userId
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        DB::table('timed_labels')->where('id', $this->timedLabelId)->update(['is_executed' => true]);

        $labelsAfterTime = DB::table('label_labels_to_add_after_timed_label')->where('main_label_id', $this->labelId)->get();

        if ($labelsAfterTime->count() > 0) {
            foreach ($labelsAfterTime as $labelAfterTime) {
                $labelsToAddAtTheEnd[] = $labelAfterTime->label_to_add_id;
            }
        } else {
            $labelsToAddAtTheEnd[] = Label::URGENT_INTERVENTION;
        }

        if (count($labelsToAddAtTheEnd) > 0) {
            AddLabelService::addLabels($this->order, $labelsToAddAtTheEnd, $this->loopPreventionArray, $this->options, $this->userId);
        }
        RemoveLabelService::removeLabels($this->order, [ $this->labelId ], $this->loopPreventionArray, [], $this->userId);
    }
}

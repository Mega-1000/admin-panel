<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Entities\Order;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;

class TimedLabelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private int $labelId,
        private int $preLabelId,
        private Order $order,
        private array $loopPreventionArray,
        private array $options,
        private ?int $userId,
        private Carbon $now
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentLabelId = $this->order->labels()->where('label_id', $this->preLabelId)->first();

        // job must have the same creation date
        if( $currentLabelId !== null && $currentLabelId->pivot->created_at->toDateTimeString() == $this->now->toDateTimeString() ) {
            $this->order->labels()->detach($this->preLabelId);
            AddLabelService::addLabels($this->order, [ $this->labelId ], $this->loopPreventionArray, $this->options, $this->userId);
        }
    }
}

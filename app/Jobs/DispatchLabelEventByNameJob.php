<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class DispatchLabelEventByNameJob extends Job implements ShouldQueue
{
    use IsMonitored;

    protected Order $order;
    protected $eventName;
    protected ?int $userId;

    /**
     * DispatchLabelEventByNameJob constructor.
     * @param $order
     * @param $eventName
     */
    public function __construct(Order $order, $eventName)
    {
        $this->userId = Auth::user()?->id;
        $this->order = $order;
        $this->eventName = $eventName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $config = config('labels-map')[$this->eventName];
        $preventionArray = [];

        if (!empty($config['add'])) {
            AddLabelService::addLabels($this->order, $config['add'], $preventionArray, [], Auth::user()->id);
        }

        if (!empty($config['remove'])) {
            RemoveLabelService::removeLabels($this->order, $config['remove'], $preventionArray, [], Auth::user()->id);
        }
    }
}

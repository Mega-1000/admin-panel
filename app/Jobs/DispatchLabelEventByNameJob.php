<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class DispatchLabelEventByNameJob extends Job implements ShouldQueue
{
    use IsMonitored;

    protected $order;
    protected $eventName;

    /**
     * DispatchLabelEventByNameJob constructor.
     * @param $order
     * @param $eventName
     */
    public function __construct($order, $eventName)
    {
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
        $config = config('labels-map')[$this->eventName];
        $preventionArray = [];

        if(!empty($config['add'])) {
            dispatch_now(new AddLabelJob($this->order, $config['add'], $preventionArray));
        }

        if(!empty($config['remove'])) {
            dispatch_now(new RemoveLabelJob($this->order, $config['remove'], $preventionArray));
        }
    }
}

<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class DispatchLabelEventByNameJob extends Job implements ShouldQueue
{
    use IsMonitored;

    protected $order;
    protected $eventName;
    protected ?int $userId;

    /**
     * DispatchLabelEventByNameJob constructor.
     * @param $order
     * @param $eventName
     */
    public function __construct($order, $eventName)
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
        if(Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $config = config('labels-map')[$this->eventName];
        $preventionArray = [];

        if(!empty($config['add'])) {
            dispatch(new AddLabelJob($this->order, $config['add'], $preventionArray));
        }

        if(!empty($config['remove'])) {
            dispatch(new RemoveLabelJob($this->order, $config['remove'], $preventionArray));
        }
    }
}

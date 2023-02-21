<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\WorkingEvents;
use App\Jobs\Orders\SendItemsConstructedMailJob;
use App\Jobs\Orders\SendItemsRedeemedMailJob;
use App\Repositories\LabelRepository;
use App\Repositories\OrderLabelSchedulerAwaitRepository;
use App\Repositories\OrderLabelSchedulerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddLabelJob extends Job implements ShouldQueue
{
    use IsMonitored;

    protected $order;
    protected $labelIdsToAdd;
    protected $loopPreventionArray;
    protected $options;
    protected $self;
    protected $time;

    protected $awaitRepository;

    /**
     * AddLabelJob constructor.
     * @param $order
     * @param $labelIdsToAdd
     * @param $loopPreventionArray
     * @param $options
     * @param $self
     * @param $time
     */
    public function __construct($order, $labelIdsToAdd, &$loopPreventionArray = [], $options = [], $self = null, $time = false)
    {
        $this->order = $order;
        $this->labelIdsToAdd = $labelIdsToAdd;
        $this->loopPreventionArray = $loopPreventionArray;
        $this->options = array_merge([
            'added_type' => null,
        ], $options);
        $this->self = $self;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        OrderRepository $orderRepository,
        LabelRepository $labelRepository,
        OrderLabelSchedulerRepository $orderLabelSchedulerRepository,
        OrderLabelSchedulerAwaitRepository $awaitRepository,
        TaskRepository $taskRepository
    ) {
        $now = Carbon::now();

        if (!($this->order instanceof Order)) {
            $this->order = $orderRepository->find($this->order);
        }

        WorkingEvents::createEvent(WorkingEvents::LABEL_ADD_EVENT, $this->order->id);
        if (count($this->labelIdsToAdd) < 1) {
            return;
        }

        $this->awaitRepository = $awaitRepository;

        foreach ($this->labelIdsToAdd as $labelId) {
            if ($labelId instanceof Label) {
                $labelId = $labelId->id;
            }

            if ($labelId=== 45) {
                if (count(array_intersect($this->order->labels()->pluck('labels.id')->toArray(), Label::NOT_ADD_LABEL_CHECK_CORRECT)) > 0) {
                    continue;
                }
            }

            if (!empty($this->loopPreventionArray['already-added']) && in_array($labelId,
                    $this->loopPreventionArray['already-added'])) {
                continue;
            }

            //attaching current label
            $label = $labelRepository->find($labelId);
            $alreadyHasLabel = $this->order->labels()->where('label_id', $labelId)->get();

            if($this->time !== false) {
                $timedLabel = DB::table('timed_labels')->insert([
                    'execution_time' => $this->time,
                    'order_id' => $this->order->id,
                    'label_id' => $labelId,
                    'is_executed' => false
                ]);
                $labelsAfterTime = DB::table('label_labels_to_add_after_timed_label')->where('main_label_id', $labelId)->get();
                $targetDatetime = Carbon::parse($this->time);
                $delay = $now->diffInSeconds($targetDatetime);

                if($labelsAfterTime->count() > 0) {
                    $removeLabelJob = dispatch(new RemoveLabelJob($this->order->id, [$labelId]))->delay($delay);
                    foreach($labelsAfterTime as $labelAfterTime) {
                        $addLabelJob = dispatch(new AddLabelJob($this->order->id, [$labelAfterTime->label_to_add_id]))->delay($delay);
                    }
                } else {
                    $removeLabelJob = dispatch(new RemoveLabelJob($this->order->id, [$labelId]))->delay($delay);
                    $addLabelJob = dispatch(new AddLabelJob($this->order->id, [Label::URGENT_INTERVENTION]))->delay($delay);
                }
            }

            if (count($alreadyHasLabel) == 0) {
                $this->order->labels()->attach($this->order->id, ['label_id' => $label->id, 'added_type' => $this->options['added_type'], 'created_at' => Carbon::now()]);
                $this->setScheduledLabelsAfterAddition($this->order->id, $label, $orderLabelSchedulerRepository);
                $this->loopPreventionArray['already-added'][] = $labelId;

                if ($label->message !== null && $labelId !== 89) {
                    dispatch_now(new LabelAddNotificationJob($this->order->id, $label->id));
                }

                if ($label->id == 52) {  //wyslana do awizacji
                    if($this->order->customer->id == 4128) {
                        dispatch_now(new OrderStatusChangedToDispatchNotificationJob($this->order->id, true));
                    } else {
                        dispatch_now(new OrderStatusChangedToDispatchNotificationJob($this->order->id));
                    }

                }
//                if ($labelId == 95) {
//                    dispatch_now(new StartCommunicationMailSenderJob($this->order->id, $this->order->customer->login));
//                }

                if($labelId == Label::ORDER_ITEMS_CONSTRUCTED){
                	dispatch(new SendItemsConstructedMailJob($this->order));
                    dispatch_now(new SavePackageGroupJob($this->order));

                    $tasks = $taskRepository->findByField('order_id',$this->order->id)->all();
                    if(count($tasks) != 0) {
                        foreach ($tasks as $task) {
                            $task->update([
                                'color' => '32CD32',
                                'status' => 'FINISHED'
                            ]);
                        }
                    }
                }

                if ($labelId == Label::ORDER_ITEMS_REDEEMED_LABEL) {
                    dispatch(new SendItemsRedeemedMailJob($this->order));
                    $this->order->preferred_invoice_date = $now;
                    $tasks = $taskRepository->findByField('order_id', $this->order->id)->all();

                    if (count($tasks) != 0) {
                        foreach ($tasks as $task) {
                            $task->update([
                                'color' => '008000',
                                'status' => 'FINISHED'
                            ]);
                        }
                    }
                }
            }

            //attaching labels to add after addition
            if (count($label->labelsToAddAfterAddition) > 0) {
                $labelIdsToAttach = [];
                foreach ($label->labelsToAddAfterAddition as $item) {
                    $labelIdsToAttach[] = $item->id;
                }
                dispatch(new AddLabelJob($this->order, $labelIdsToAttach, $this->loopPreventionArray));
            }

            //detaching labels to remove after addition
            if (count($label->labelsToRemoveAfterAddition) > 0) {
                $labelIdsToDetach = [];
                foreach ($label->labelsToRemoveAfterAddition as $item) {
                    $labelIdsToDetach[] = $item->id;
                }
                dispatch(new RemoveLabelJob($this->order, $labelIdsToDetach, $this->loopPreventionArray));
            }
        }
    }

    protected function setScheduledLabelsAfterAddition($orderId, $label, $orderLabelSchedulerRepository)
    {
        $timedLabelsAfterAddition = $label->timedLabelsAfterAddition()->get();

        if (!count($timedLabelsAfterAddition)) {
            return;
        }

        foreach ($timedLabelsAfterAddition as $item) {
            $pivot = $item->pivot;

            $this->addScheduler($orderId, $label, $orderLabelSchedulerRepository, $pivot, 'A', 'to_add_type_a');
            $this->addScheduler($orderId, $label, $orderLabelSchedulerRepository, $pivot, 'A', 'to_remove_type_a');
            $this->addScheduler($orderId, $label, $orderLabelSchedulerRepository, $pivot, 'B', 'to_add_type_b');
            $this->addScheduler($orderId, $label, $orderLabelSchedulerRepository, $pivot, 'B', 'to_remove_type_b');

            $this->awaitForUserToEnterDate($orderId, $pivot, 'to_add_type_c');
            $this->awaitForUserToEnterDate($orderId, $pivot, 'to_remove_type_c');
        }
    }

    protected function awaitForUserToEnterDate($orderId, $pivot, $action)
    {
        if (!empty($pivot->$action)) {
            $this->awaitRepository->create([
                'order_id' => $orderId,
                'user_id' => \Auth::id(),
                'labels_timed_after_addition_id' => $pivot->id,
            ]);
        }
    }

    protected function addScheduler($orderId, $label, $orderLabelSchedulerRepository, $pivot, $type, $action)
    {
        $now = Carbon::now();
        if (!empty($pivot->$action)) {
            $minutesToAdd = (float)$pivot->$action * 60;
            $orderLabelSchedulerRepository->create([
                'order_id' => $orderId,
                'label_id' => $label->id,
                'label_id_to_handle' => $pivot->label_to_handle_id,
                'type' => $type,
                'action' => $action,
                'trigger_time' => $now->addMinutes(ceil($minutesToAdd)),
            ]);
        }
    }
}

<?php

namespace App\Services;

use App\Entities\Label;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Entities\Courier;
use App\Enums\CourierName;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;

class TaskService
{
    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get task query
     *
     * @param array $courierArray
     * @return mixed
     */
    public function getTaskQuery(array $courierArray)
    {
        return $this->taskRepository->where('user_id', Task::WAREHOUSE_USER_ID)
            ->whereHas('order', function ($query) use ($courierArray) {
                $query->whereHas('packages', function ($query) use ($courierArray) {
                    $query->whereIn('service_courier_name', $courierArray);
                })->whereHas('labels', function ($query) {
                    $query
                        ->where('labels.id', Label::BLUE_HAMMER_ID);
                })->whereHas('dates', function ($query) {
                    $query->orderBy('consultant_shipment_date_to');
                })
                    ->whereDoesntHave('labels', function ($query) {
                        $query->where('labels.id', Label::RED_HAMMER_ID)
                            ->orWhere('labels.id', Label::GRAY_HAMMER_ID)
                            ->orWhere('labels.id', Label::PRODUCTION_STOP_ID);
                    });
            });
    }

    /**
     * Get task grouped by date
     *
     * @return array
     */
    public function groupTaskByShipmentDate(): array
    {
        $result = [];
        $dates = [
            'past' => [],
            'future' => [],
        ];
        $today = Carbon::today();
        $lastShowedDate = Carbon::today()->addDays(3);
        $period = CarbonPeriod::create($today, $lastShowedDate);
        foreach ($period as $date) {
            $dates[$date->toDateString()] = [];
        }
        
        $couriers = Courier::where('active',1)->orderBy('item_number')->get();
        foreach ($couriers as $deliveryTypes) {
            $tasksByDay = $dates;
            foreach ($this->getTaskQuery([$deliveryTypes['courier_name']])->get() as $task) {
                $orderDate = Carbon::parse($task->order->dates->customer_shipment_date_to);
                $key = $orderDate->toDateString();
                if ($orderDate->isBefore($today)) {
                    $tasksByDay['past'][] = $task;
                } elseif ($orderDate->isBetween($today, $lastShowedDate)) {
                    $tasksByDay[$key][] = $task;
                } else {
                    $tasksByDay['future'][] = $task;
                }
            }
            ksort($tasksByDay);
            $result[$deliveryTypes['courier_key']] = $tasksByDay;
        }
        return $result;
    }

    /**
     * Prepare task for handling
     *
     * @param $package_type
     * @param $skip
     * @return mixed
     * @throws Exception
     */
    public function prepareTask($package_type, $skip)
    {
        $courierArray = CourierName::DELIVERY_TYPE_FOR_TASKS[$package_type] ?? [];
        if (empty($courierArray)) {
            throw new Exception(__('order_packages.message.package_error'));
        }
        $task = $this->getTaskQuery($courierArray)->offset($skip)->first();
        return $task;
    }

    /**
     * Prepare task for handling
     *
     * @param $id
     * @param $start
     * @param $end
     * @return array
     */
    public function getSeparator($id,$start,$end) :array
    {
        $users = [36, 37, 38];

        $array = [];

        foreach($users as $user_id){
            $takstime = TaskTime::with(['task'])
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
            ->get()->first();
            
            if($takstime){
                $start = Carbon::parse($takstime->date_start)->subMinute();
                $end = Carbon::parse($takstime->date_start);
                
                $array[] = [
                    'id' => null,
                    'resourceId' => $takstime->task->user_id,
                    'title' => '',
                    'start' => $start->format('Y-m-d\TH:i'),
                    'end' => $end->format('Y-m-d\TH:i'),
                    'color' => '#000000',
                    'text' => 'SEPARATOR',
                    'customOrderId' => null,
                    'customTaskId' => null
                ];
            }
        }
        
        return $array;
    }

    /**
     * Prepare task for handling
     *
     * @param $user_id
     * @param $date
     * @return string|null
     */
    public function getUserSeparator($user_id,$date) :string|null
    {
        $sep = null;
        $separator = $this->getSeparator(16,$date->format('Y-m-d').' 00:00:00',$date->format('Y-m-d').' 23:59:59');
        foreach($separator as $s){
            if($s['resourceId'] == $user_id){
                $sep = Carbon::parse($s['end'])->format('Y-m-d H:i:s');
            }
        }
        return $sep;
    }

    /**
     * Prepare task for handling
     *
     * @return mixed
     */
    public function transfersTask()
    {
        $users = [36, 37, 38];
        $colors = ['FF0000', 'E6C74D', '194775'];
        
        $today = Carbon::today();
        foreach($users as $user_id){
            $time_start = Carbon::parse($today->format('Y-m-d').' '.$this->getTimeLastTask($user_id));
            foreach($colors as $color){
                $time_start = $this->prepareTransfersTask($user_id,$color,$time_start);
            }
        }

        return 'completed';
    }

    /**
     * Prepare task for handling
     *
     * @param $user_id
     * @param $color
     * @param $time_start
     * @return string
     */
    public function prepareTransfersTask($user_id,$color,$time_start) :string
    {
        $date = Carbon::yesterday();

        $tasks = Task::with(['taskTime'])
            ->where('status',Task::WAITING_FOR_ACCEPT)
            ->where('user_id',$user_id)
            ->where('color',$color)
            ->whereNull('parent_id')
            ->whereNull('rendering')
            ->whereHas('taskTime', function ($query) use ($date) {
                $query->where('date_start', 'like' , $date->format('Y-m-d')."%");
            })->get();

        foreach($tasks as $task){
            $this->moveTask($user_id,$time_start);
            $time_start = $this->storeTransfersTask($task->taskTime->id,$time_start);
        }

        return $time_start;
    }

    /**
     * Prepare task for handling
     *
     * @param $taskTime_id
     * @param $time_start
     * @return string
     */
    public function storeTransfersTask($taskTime_id,$time_start) :string
    {
        $date_start = $time_start;
        $date_end = Carbon::parse($time_start)->addMinutes(2);
        $taskTime = TaskTime::find($taskTime_id);
        $transfer_date = $taskTime->date_start;
        $taskTime->date_start = $date_start;
        $taskTime->date_end = $date_end->format('Y-m-d H:i:s');
        $taskTime->transfer_date = $transfer_date;
        $taskTime->save();
        
        return $date_end;
    }

    /**
     * Prepare task for handling
     *
     * @param $user_id
     * @return string
     */
    public function getTimeLastTask($user_id) :string
    {
        $date = Carbon::today();
        $takstime = TaskTime::with(['task'])
            ->whereHas('task',
                function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                    $query->whereNull('parent_id');
                    $query->whereNull('rendering');
                })
            ->where('date_start', 'like' , $date->format('Y-m-d')."%")
            ->whereNotNull('transfer_date')
            ->orderBy('date_start', 'asc')
            ->get()->first();

        if(isset($takstime)){
            $time = Carbon::parse($takstime->date_end)->format('H:i:s');
        }else{
            $time = Carbon::parse($date->format('Y-m-d').' 7:00')->format('H:i:s');
        }

        return $time;
    }

    /**
     * Prepare task for handling
     *
     * @param $user_id
     * @param $time
     * @return bool
     */
    public function moveTask($user_id,$time) :bool
    {
        $date = Carbon::today();
        $sep = $this->getUserSeparator($user_id,$date);

        if($time>=$sep){

            $takstime = TaskTime::with(['task'])
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

            foreach($takstime as $task){
                $task->date_start = Carbon::parse($task->date_start)->addMinutes(30);
                $task->date_end = Carbon::parse($task->date_end)->addMinutes(30);
                $task->save();
            }
        }

        return true;
    }
}

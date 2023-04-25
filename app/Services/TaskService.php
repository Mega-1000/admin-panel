<?php

namespace App\Services;

use App\Repositories\Couriers;
use Illuminate\Database\Eloquent\Collection;
use App\Entities\Label;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Entities\Courier;
use App\Enums\CourierName;
use App\Repositories\TaskRepository;
use App\Repositories\Tasks;
use App\Repositories\TaskTimes;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;

class TaskService
{
    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    public function __construct(
        TaskRepository $taskRepository, 
        protected readonly Tasks $tasksRepository,
        protected readonly Couriers $courierRepository,
        protected readonly TaskTimes $taskTimesRepository
    )
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
        return $this->tasksRepository->getTaskQuery($courierArray);
    }

    /**
     * Get task grouped by date
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
        
        $couriers = $this->courierRepository->getActiveOrderByNumber();
        foreach ($couriers as $courier) {
            $tasksByDay = $dates;
            foreach ($this->getTaskQuery([$courier->courier_name])->get() as $task) {
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
            $result[$courier->courier_key] = $tasksByDay;
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
     * @param int $id
     * @param string $start
     * @param string $end
     */
    public function getSeparator($id,$start,$end) :array
    {
        $users = [36, 37, 38];

        $separators = [];

        foreach($users as $user_id){
            $taskTime = $this->taskTimesRepository->getSeparator($user_id,$id,$start,$end);
               
            if($taskTime->count()>0){
                $taskTimeFirst = $taskTime->first();

                $start = Carbon::parse($taskTimeFirst->date_start)->subMinute();
                $end = Carbon::parse($taskTimeFirst->date_start);
                
                $separators[] = [
                    'id' => null,
                    'resourceId' => $taskTimeFirst->task->user_id,
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
        
        return $separators;
    }

    /**
     * Prepare task for handling
     *
     * @param int $user_id
     * @param string $date
     */
    public function getUserSeparator($user_id,$date) :string
    {
        $sep = '';
        $separator = $this->getSeparator(16,$date->format('Y-m-d').' 00:00:00',$date->format('Y-m-d').' 23:59:59');
        foreach($separator as $s){
            if($s['resourceId'] == $user_id){
                $sep = Carbon::parse($s['end'])->format('Y-m-d H:i:s');
            }
        }
        return $sep;
    }

    /**
     * Transfers Task
     */
    public function transfersTask() :string
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
     * Prepare Transfer Task
     *
     * @param int $user_id
     * @param string $color
     * @param string $time_start
     */
    public function prepareTransfersTask($user_id,$color,$time_start) :string
    {
        $date = Carbon::yesterday();

        $tasks = $this->tasksRepository->transfersTask($user_id,$color,$date);

        foreach($tasks as $task){
            $time_start = $this->storeTransfersTask($task->taskTime->id,$time_start);
            $this->moveTask($user_id,$time_start);
        }
        return $time_start;
    }

    /**
     * Prepare task for handling
     *
     * @param int $taskTime_id
     * @param string $time_start
     */
    public function storeTransfersTask($taskTime_id,$time_start) :string
    {
        $date_start = $time_start;
        $date_end = Carbon::parse($time_start)->addMinutes(2);

        $taskTime = TaskTime::find($taskTime_id);
        $transfer_date = $taskTime->date_start;
        $taskTime->update([
            'date_start' => $date_start,
            'date_end' => $date_end->format('Y-m-d H:i:s'),
            'transfer_date' => $transfer_date
        ]);
        
        return $date_end;
    }

    /**
     * Prepare task for handling
     *
     * @param int $user_id
     */
    public function getTimeLastTask($user_id) :string
    {
        $date = Carbon::today();
        $taskTime = $this->taskTimesRepository->getTimeLastTask($user_id, $date);
        $firstTaskTime = $taskTime->first(); 

        return (isset($firstTaskTime->date_end) ? Carbon::parse($firstTaskTime->date_end)->format('H:i:s') : TaskTime::TIME_START);
    }

    /**
     * Prepare task for handling
     *
     * @param int $user_id
     * @param string $time
     */
    public function moveTask($user_id,$time) :bool
    {
        $date = Carbon::today();
        $sep = $this->getUserSeparator($user_id,$date);

        if($time>=$sep){

            $taskTimes = $this->taskTimesRepository->getMoveTask($user_id, $date);

            foreach($taskTimes as $taskTime){
                $taskTime->update([
                    'date_start' => Carbon::parse($taskTime->date_start)->addMinutes(30),
                    'date_end' => Carbon::parse($taskTime->date_end)->addMinutes(30)
                ]);
            }
        }

        return true;
    }

    /**
     * Get user task query
     *
     * @param int $user_id
     */
    public function getOpenUserTask($user_id): Collection
    {
        return $this->tasksRepository->getOpenUserTask($user_id);
    }

    /**
     * moving tasks backward
     *
     * @param object $task
     */
    public function movingTasksBackward($task): Collection
    {
        $actualTaskTime = TaskTime::where('task_id',$task->id)->first();
        $date_start = Carbon::parse($actualTaskTime->date_start);
        $date_end = Carbon::parse($actualTaskTime->date_end);

        $totalDuration = $date_end->diffInMinutes($date_start);

        $moveTasksTime = $this->taskTimesRepository->movingTasksBackward($task,$actualTaskTime,$date_start);
        
        foreach($moveTasksTime as $taskTime){
            $startNextTaskAt = Carbon::parse($t->date_start)->subMinutes($totalDuration);
	        $endNextTaskAt = Carbon::parse($t->date_end)->subMinutes($totalDuration);
            $taskTime->update([
                'date_start' => $startNextTaskAt,
                'date_end' => $endNextTaskAt
            ]);
            $taskTime->refresh();
        }

        return $moveTasksTime;
    }

    /**
     * check task login
     *
     * @param string $login
     * @param object $address
     * @param int $user_id
     */
    public function checkTaskLogin($login,$address,$user_id) :bool|array
    {
        $tasks = $this->tasksRepository->checkTaskLogin($login, $user_id);

        if(empty($tasks)){
            return false;
        }

        $array = [];
        return $array[0] = 'error'; 
        foreach($tasks as $task){
            if(
                $task->order->getDeliveryAddress()->address = $address->address &&
                $task->order->getDeliveryAddress()->flat_number = $address->flat_number &&
                $task->order->getDeliveryAddress()->postal_code = $address->postal_code &&
                $task->order->getDeliveryAddress()->city = $address->city &&
                $task->order->getDeliveryAddress()->phone = $address->phone
            ){
                if($task->order->labels->contains('id', Label::BLUE_HAMMER_ID)){
                    if(!$task->order->labels->contains('id', Label::RED_HAMMER_ID)){
                        if(!$task->order->labels->contains('id', Label::ORDER_ITEMS_CONSTRUCTED)){
                            if($task->parent_id){
                                $array[$task->parent_id] = $task->parent_id;
                            }else{
                                $array[$task->id] = $task->id;
                            }
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }else{
                    if($task->order->labels->contains('id', Label::ORDER_ITEMS_REDEEMED_LABEL)){
                        return false;
                    }else{
                        $array[0] = 'error'; 
                        //zielona bateria jeśli nie to wyświetlic komunikat
                    }
                }
            }
            
        }
        return $array;
    }

    /**
     * add task to planer
     *
     * @param object $order
     * @param int $user_id
     */
    public function addTaskToPlanner($order,$user_id) :int
    {

    }

    /**
     * add task to group planer
     *
     * @param object $order
     * @param object $task
     * @param int $user_id
     */
    public function addTaskToGroupPlanner($order,$task,$user_id) :int
    {

    }
}

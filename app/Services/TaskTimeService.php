<?php

namespace App\Services;

use App\DTO\Task\AddingTaskResponseDTO;
use App\DTO\Task\StockListDTO;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Entities\Label;
use App\Entities\LabelGroup;
use App\Entities\OrderAddress;
use App\Services\TaskService;
use App\Repositories\TaskRepository;
use App\Repositories\TaskTimeRepository;
use App\Repositories\Tasks;
use App\Repositories\TaskTimes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Auth;

class TaskTimeService
{
    public function __construct(
        protected readonly TaskRepository       $taskRepository,
        protected readonly TaskTimeRepository   $taskTimeRepository, 
        protected readonly Tasks                $tasksRepository,
        protected readonly TaskTimes            $taskTimesRepository
    )
    {}

    /**
     * Get courier orderBy
     * 
     */
    public static function getTask(int $id): Task|null
    {
        return Tasks::getTaskLabel($id);
    }

    /**
     * Get Tasks Waiting For Accept With Children
     * @return Collection<Task>
     */
    public static function getTasksWaitingForAcceptWithChildren(): Collection
    {
        return Tasks::getTasksWaitingForAccept();
    }

    /**
     * Get All Tasks With Childrens
     * 
     */
    public static function getAllTasksWithChildrens(int $id): Task
    {
        return Tasks::getAllTasksWithChildrens($id);
    }

    /**
     * Get task query
     * 
     */
    public static function getWarehouseUserWithBlueHammerLabelQuery(array $couriersNames): Builder
    {
        return Tasks::getWarehouseUserWithBlueHammerLabelQuery($couriersNames);
    }

    /**
     * Transfers Task
     * 
     * @return Collection<Task>
     */
    public static function transfersTask(int $user_id, string $color, Carbon $date): Collection
    {
        return Tasks::getTransfersTask($user_id, $color, $date);
    }

    /**
     * add task to planer
     *
     * @param Collection<Order> $order
     */
    public function saveTaskToPlanner(Collection $order, int $user_id): int
    {
        $date = Carbon::today();
        $start_date = $this->getTimeLastNowTask($user_id);
        $start = Carbon::parse($date->format('Y-m-d').' '.$start_date);
        $end = Carbon::parse($date->format('Y-m-d').' '.$start_date)->addMinutes(2);

        $dataToStore = [
            'start' => $start,
            'end' => $end,
            'id' => $user_id,
            'user_id' => $user_id
        ];
        $task = Task::create([
            'user_id' => $user_id,
            'name' => $order->id !== null ? $order->id : null,
            'created_by' => Auth::user()->id,
            'color' => '194775',
            'warehouse_id' => 16,
            'status' => 'WAITING_FOR_ACCEPT',
            'order_id' => $order->id !== null ? $order->id : null
        ]);
        TaskTime::create([
            'task_id' => $task->id,
            'date_start' => $start,
            'date_end' => $end,
        ]);

        return $task->id;
    }

    /**
     * Add task to group planer
     * 
     * @param Collection<Order> $order
     */
    public function addTaskToGroupPlanner(Collection $order, int $task_id, int $user_id): int
    {
        $task = Task::find($task_id);
        $date = Carbon::today();
        $start_date = $this->getTimeLastNowTask($user_id);
        $start = Carbon::parse($date->format('Y-m-d').' '.$start_date);
        $end = Carbon::parse($date->format('Y-m-d').' '.$start_date)->addMinutes(2);

        $dataToStore = [
            'start' => $start,
            'end' => $end,
            'id' => $user_id,
            'user_id' => $user_id
        ];

        $newTask = Task::create([
            'user_id' => $user_id,
            'name' => $order->id !== null ? $order->id : null,
            'created_by' => Auth::user()->id,
            'color' => '194775',
            'warehouse_id' => 16,
            'status' => 'WAITING_FOR_ACCEPT',
            'order_id' => $order->id !== null ? $order->id : null,
            'parent_id' => null
        ]);
        TaskTime::create([
            'task_id' => $newTask->id,
            'date_start' => $start,
            'date_end' => $end,
        ]);

        if($task->childs->count() == 0){
            $newGroup = Task::create([
                'user_id' => $user_id,
                'name' => $task->order_id.', '.$order->id,
                'created_by' => Auth::user()->id,
                'color' => '194775',
                'warehouse_id' => 16,
                'status' => 'WAITING_FOR_ACCEPT',
            ]);
            TaskTime::create([
                'task_id' => $newGroup->id,
                'date_start' => $start,
                'date_end' => $end,
            ]);

            $task->update([
                'parent_id' => $newGroup->id,
            ]);

            $newTask->update([
                'parent_id' => $newGroup->id,
            ]);
            return $newTask->id;
        }
        
        $name = explode(',',$task->name);
        $name[] = $order->id;
        $task->update([
            'name' => join(', ',$name)
        ]);

        $task->taskTime->update([
            'date_start' => $dataToStore['start'],
            'date_end' => $dataToStore['end']
        ]);
        
        $newTask->update([
            'parent_id' => $newGroup->id,
        ]);

        return $newTask->id;
    }

    /**
     * Get now task for handling
     *
     */
    public function getTimeLastNowTask(int $user_id): string
    {
        $date = Carbon::today();
        $taskTime = TaskTimes::getTimeLastNowTask($user_id, $date);
        $firstTaskTime = $taskTime->first(); 

        return $firstTaskTime?->date_end?->format('H:i:s') ?? Carbon::now()->format('H:i:s');
    }

    /**
     * Adding Task To Planner
     * 
     */
    public function addingTaskToPlanner(Order $order, int $deliveryWarehouse): AddingTaskResponseDTO
    {
        $task = $this->checkTaskLogin(
            $order->customer->login,
            $order->getDeliveryAddress(),
            $deliveryWarehouse
        );

        if($task === -1) {
            return new AddingTaskResponseDTO(
                'ERROR', 
                $order->id, 
                $deliveryWarehouse, 
                'Wstrzymać dodawanie zadania?'
            );
        }

        return match($task) {
            0 => $this->saveTaskToPlanner($order, $deliveryWarehouse),
            default => new AddingTaskResponseDTO(
                'ADDED_TASK', 
                $id, 
                $delivery_warehouse, 
                'Dodano zadanie id: ' . $id
            )
        };
        
    }

    /**
     * @return StockListDTO[]
     */
    public function getQuantityInStockList(Collection $tasks): array
    {
        $stockLists = []; 
        foreach($tasks as $task){
            if($task->order_id!==null){
                $orderItems = OrderItem::with(['product'])->where('order_id',$task->order_id)->get();
                foreach($orderItems as $orderItem){
                    if(
                        $orderItem->quantity > $orderItem->product->getPositions()->first()->position_quantity ||
                        $orderItem->quantity > $orderItem->product->stock->quantity
                    ){
                        $stockLists[$orderItem->order_id][] = new StockListDTO(
                            $orderItem->product->id,
                            $orderItem->product->name,
                            $orderItem->product->symbol,
                            $orderItem->quantity,
                            ($orderItem->product->stock->count() ? $orderItem->product->stock->quantity : 0),
                            $orderItem->product->getPositions()->first()->position_quantity
                        );
                    }
                }
            }
        }

        return $stockLists;
    }

    /**
     * check task login
     *
     */
    public function checkTaskLogin(string $login, OrderAddress $address, int $user_id): int
    {
        $tasks = Tasks::getTaskLogin($login, $user_id);

        if(empty($tasks)){
            return 0;
        }

        $taskID = 0;
        foreach($tasks as $task){
            $delivery_address = $task->order->getDeliveryAddress();
            $labels = $task->order->labels;
            if(
                $delivery_address->address == $address->address &&
                $delivery_address->flat_number == $address->flat_number &&
                $delivery_address->postal_code == $address->postal_code &&
                $delivery_address->city == $address->city &&
                $delivery_address->phone == $address->phone
            ){
                if($labels->contains('id', Label::BLUE_HAMMER_ID)){
                    if(!$labels->contains('id', Label::RED_HAMMER_ID)){
                        if(!$labels->contains('id', Label::ORDER_ITEMS_CONSTRUCTED)){
                            if($task->parent_id){
                                return $task->parent_id;
                            }
                            
                            return $task->id;
                        }
                    }
                }

                if(!$labels->contains('id', Label::ORDER_ITEMS_REDEEMED_LABEL)){
                    $taskID = -1; 
                }
            }
            
        }
        return $taskID;
    }
}

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
     * @param int $id
     */
    public static function getTask(int $id): Task|null
    {
        return Task::with(['user', 'taskTime', 'taskSalaryDetail', 'order', 'childs' => function ($q) {
            $q->with(['order' => function ($q) {
                $q->with(['labels' => function ($q) {
                    $q->where('label_group_id', LabelGroup::PRODUCTION_LABEL_GROUP_ID)->orWhereIn('labels.id',
                        [Label::BLUE_BATTERY_LABEL_ID, Label::ORANGE_BATTERY_LABEL_ID, Label::ORDER_ITEMS_REDEEMED_LABEL]);
                }]);
            }]);
        }])->where('id',$id)->first();
    }

    /**
     * Get Task children
     * @return Collection<Task>
     */
    public static function getTasksWithChildren(): Collection
    {
        return Task::with('taskTime','childs')
            ->has('childs')
            ->where('status',Task::WAITING_FOR_ACCEPT)
            ->get();
    }

    /**
     * Get courier orderBy
     * @param int $id
     */
    public static function getChildren(int $id): Task
    {
        return Task::with('taskTime','childs')
            ->has('childs')
            ->find($id);
    }

    /**
     * Get task query
     * @param array $courierArray
     */
    public static function getTaskQuery(array $courierArray): Builder
    {
        return Task::where('user_id', Task::WAREHOUSE_USER_ID)
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
        })
        ->orderByRaw("FIELD(color, 'FF0000', 'E6C74D', '194775')");
    }

    /**
     * Transfers Task
     * @param int $user_id
     * @param string $color
     * @param Carbon $date
     * @return Collection<Task>
     */
    public static function transfersTask(int $user_id, string $color, object $date): Collection
    {
        return Task::with(['taskTime'])
        ->where('status',Task::WAITING_FOR_ACCEPT)
        ->where('user_id',$user_id)
        ->where('color',$color)
        ->whereNull('parent_id')
        ->whereNull('rendering')
        ->whereHas('taskTime', function ($query) use ($date) {
            $query->where('date_start', 'like' , $date->format('Y-m-d')."%");
        })->where(function($query) {
            $query->whereHas('order', function ($query) {
                $query->whereHas('labels', function ($query) {
                    $query->where('labels.id', Label::RED_HAMMER_ID)
                        ->orWhere('labels.id', Label::BLUE_HAMMER_ID);
                });
            })->orWhereDoesntHave('order');
        })->get();
    }

    /**
     * add task to planer
     *
     * @param Collection<Order> $order
     * @param int $user_id
     */
    public function saveTaskToPlanner(object $order, int $user_id): int
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
     * add task to group planer
     *
     * @param Collection<Order> $order
     * @param int $task_id
     * @param int $user_id
     */
    public function addTaskToGroupPlanner(object $order, int $task_id, int $user_id): int
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
     * get now task for handling
     *
     * @param int $user_id
     */
    public function getTimeLastNowTask(int $user_id): string
    {
        $date = Carbon::today();
        $taskTime = $this->taskTimesRepository->getTimeLastNowTask($user_id, $date);
        $firstTaskTime = $taskTime->first(); 

        return (isset($firstTaskTime->date_end) ? Carbon::parse($firstTaskTime->date_end)->format('H:i:s') : Carbon::now()->format('H:i:s'));
    }

    /**
     * Adding Task To Planner
     * @param Order $order
     * @param int $delivery_warehouse
     */
    public function addingTaskToPlanner(object $order, int $delivery_warehouse): AddingTaskResponseDTO
    {
        $task = $this->checkTaskLogin(
            $order->customer->login,
            $order->getDeliveryAddress(),
            $delivery_warehouse
        );
        
        if($task === -1) {
            return new AddingTaskResponseDTO(
                'ERROR', 
                $order->id, 
                $delivery_warehouse, 
                'Wstrzymać dodawanie zadania?'
            );
        }

        return match($task) {
            0 => $this->saveTaskToPlanner($order,$delivery_warehouse),
            default => new AddingTaskResponseDTO(
                'ADDED_TASK', 
                $id, 
                $delivery_warehouse, 
                'Dodano zadanie id: '.$id
            )
        };
        
    }

    /**
     * @param $tasks
     */
    public function getQuantityInStockList(object $tasks): array
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
                            $orderItem->product->stock->quantity,
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
     * @param $login
     * @param $address
     * @param $user_id
     */
    public function checkTaskLogin(string $login, object $address, int $user_id): int
    {
        $tasks = $this->tasksRepository->getTaskLogin($login, $user_id);

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

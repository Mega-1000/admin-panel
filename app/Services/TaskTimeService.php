<?php

namespace App\Services;

use App\DTO\Task\AddingTaskResponseDTO;
use App\DTO\Task\StockListDTO;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderItem;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Repositories\TaskRepository;
use App\Repositories\Tasks;
use App\Repositories\TaskTimeRepository;
use App\Repositories\TaskTimes;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TaskTimeService
{
    const USER_ID = 37;

    public function __construct(
        protected readonly TaskRepository     $taskRepository,
        protected readonly TaskTimeRepository $taskTimeRepository,
        protected readonly Tasks              $tasksRepository,
        protected readonly TaskTimes          $taskTimesRepository
    )
    {
    }

    /**
     * add task to planer
     *
     * @param Order $order
     * @param int $warehouse_id
     * @return int
     */
    public function saveTaskToPlanner(Order $order, int $warehouse_id): int
    {
        $date = Carbon::today();
        $start_date = $this->getTimeLastNowTask(self::USER_ID);
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $start_date);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $start_date)->addMinutes(2);

        $task = Task::create([
            'warehouse_id' => $warehouse_id,
            'user_id' => self::USER_ID,
            'name' => $order->id !== null ? $order->id : null,
            'created_by' => Auth::user()->id,
            'color' => '194775',
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
    public function addingTaskToPlanner(Order $order, int $deliveryWarehouseId): AddingTaskResponseDTO|int
    {
        $taskId = $this->checkTaskLogin(
            $order->customer->login,
            $order->getDeliveryAddress(),
            $deliveryWarehouseId
        );

        if ($taskId === -1) {
            return new AddingTaskResponseDTO(
                'ERROR',
                $order->id,
                $deliveryWarehouseId,
                'Wstrzymać dodawanie zadania?'
            );
        }

        return match ($taskId) {
            0 => $this->saveTaskToPlanner($order, $deliveryWarehouseId),
            default => new AddingTaskResponseDTO(
                'ADDED_TASK',
                $taskId,
                $deliveryWarehouseId,
                'Dodano zadanie id: ' . $taskId
            )
        };

    }

    /**
     * @return StockListDTO[]
     */
    public function getQuantityInStockList(Collection $tasks): array
    {
        $stockLists = [];
        foreach ($tasks as $task) {
            if ($task->order_id !== null) {
                $orderItems = OrderItem::with(['product'])->where('order_id', $task->order_id)->get();
                foreach ($orderItems as $orderItem) {
                    // TODO Obstawiam że to miejsce będzie waliło błędami BW 20.05.2023 10:58
                    if (
                        $orderItem->quantity > $orderItem->product->getPositions()->first()->position_quantity ||
                        $orderItem->quantity > $orderItem->product->stock->quantity
                    ) {
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
    private function checkTaskLogin(string $login, OrderAddress $address, int $user_id): int
    {
        $tasks = Tasks::getTaskLogin($login, $user_id);

        if (empty($tasks)) {
            return 0;
        }

        $taskID = 0;
        foreach ($tasks as $task) {
            $delivery_address = $task->order->getDeliveryAddress();
            $labels = $task->order->labels;
            if (
                $delivery_address->address == $address->address &&
                $delivery_address->flat_number == $address->flat_number &&
                $delivery_address->postal_code == $address->postal_code &&
                $delivery_address->city == $address->city &&
                $delivery_address->phone == $address->phone
            ) {
                if ($labels->contains('id', Label::BLUE_HAMMER_ID)) {
                    if (!$labels->contains('id', Label::RED_HAMMER_ID)) {
                        if (!$labels->contains('id', Label::ORDER_ITEMS_CONSTRUCTED)) {
                            if ($task->parent_id) {
                                return $task->parent_id;
                            }

                            return $task->id;
                        }
                    }
                }

                if (!$labels->contains('id', Label::ORDER_ITEMS_REDEEMED_LABEL)) {
                    $taskID = -1;
                }
            }

        }
        return $taskID;
    }
}

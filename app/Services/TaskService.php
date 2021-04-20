<?php

namespace App\Services;

use App\Entities\Label;
use App\Entities\Task;
use App\Enums\CourierName;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
     *
     *
     * @param array $courierArray
     * @return mixed
     */
    public function getTaskQuery(array $courierArray)
    {
        return $this->taskRepository->where('user_id', Task::WAREHOUSE_USER_ID)
            ->whereHas('order', function ($query) use ($courierArray) {
                $query->whereHas('packages', function ($query) use ($courierArray) {
                    $query->whereIn('service_courier_name', $courierArray)
                        ->orderBy('shipment_date');
                })->whereHas('labels', function ($query) {
                    $query
                        ->where('labels.id', Label::BLUE_HAMMER_ID);
                })->whereDoesntHave('labels', function ($query) {
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
            'inne' => []
        ];
        $today = Carbon::today();
        $lastShowedDate = Carbon::today()->addDays(3);
        $period = CarbonPeriod::create($today, $lastShowedDate);
        foreach ($period as $date) {
            $dates[$date->toDateString()] = [];
        }
        foreach (CourierName::DELIVERY_TYPE_FOR_TASKS as $deliveryTypeName => $deliveryTypes) {
            $tasksByDay = $dates;
            foreach ($this->getTaskQuery($deliveryTypes)->get() as $task) {
                $orderDate = Carbon::parse($task->order->shipment_date);
                $key = $orderDate->toDateString();
                if ($orderDate->isBetween($today, $lastShowedDate)) {
                    $tasksByDay[$key][] = $task;
                } else {
                    $tasksByDay['inne'][] = $task;
                }
            }
            ksort($tasksByDay);
            $result[$deliveryTypeName] = $tasksByDay;
        }
        return $result;
    }
}

<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Helpers\CourierHelper;
use App\Helpers\TaskHelper;
use App\Helpers\TaskTimeHelper;
use App\Entities\Label;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Entities\Courier;
use App\Enums\CourierName;
use App\Repositories\TaskRepository;
use App\Repositories\TaskTimeRepository;
use App\Repositories\Tasks;
use App\Repositories\TaskTimes;
use App\Services\TaskTimeService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Auth;

class TaskService
{
    const USERS_SEPARATOR = [36, 37, 38];

    const COLOR_SEPARATOR = ['FF0000', 'E6C74D', '194775'];

    public function __construct(
        protected readonly TaskRepository       $taskRepository, 
        protected readonly TaskTimeRepository   $taskTimeRepository, 
        protected readonly Tasks                $tasksRepository,
        protected readonly TaskTimeService      $taskTimeService,
        protected readonly TaskTimes            $taskTimesRepository
    )
    {}

    /**
     * Get task query
     *
     * @param array $courierArray
     * @return mixed
     */
    public function getTaskQuery(array $courierArray)
    {
        return $this->taskTimeService->getTaskQuery($courierArray);
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
        
        $couriers = CourierHelper::getActiveOrderByNumber();
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
    public function getSeparator(int $id, string $start, string $end): array
    {
        $separators = [];
        foreach(self::USERS_SEPARATOR as $user_id){
            $taskTime = TaskHelper::getSeparator($user_id, $id, $start, $end);
               
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
     * @param object $date
     */
    public function getUserSeparator(int $user_id, object $date): string
    {
        $separatorDate = '';
        $separators = $this->getSeparator(16, $date->format('Y-m-d').' 00:00:00', $date->format('Y-m-d').' 23:59:59');
        foreach($separators as $separator){
            if($separator['resourceId'] == $user_id){
                $separatorDate = Carbon::parse($separator['end'])->format('Y-m-d H:i:s');
            }
        }
        return $separatorDate;
    }

    /**
     * Transfers Task
     */
    public function transfersTask(): void
    {
        $today = Carbon::today();
        foreach(self::USERS_SEPARATOR as $user_id){
            $time_start = Carbon::parse($today->format('Y-m-d').' '.$this->getTimeLastTask($user_id));
            
            foreach(self::COLOR_SEPARATOR as $color){
                $time_start = $this->prepareTransfersTask($user_id, $color, $time_start);
            }
        }
    }

    /**
     * Prepare Transfer Task
     *
     * @param int $user_id
     * @param string $color
     * @param string $time_start
     */
    public function prepareTransfersTask(int $user_id, string $color, string $time_start): string
    {
        $date = Carbon::yesterday();

        $tasks = $this->taskTimeService->transfersTask($user_id, $color, $date);

        foreach($tasks as $task){
            $time_start = $this->storeTransfersTask($task->taskTime->id, $time_start);
            $this->moveTask($user_id, $time_start);
        }
        return $time_start;
    }

    /**
     * Prepare task for handling
     *
     * @param int $taskTime_id
     * @param string $time_start
     */
    public function storeTransfersTask(int $taskTime_id, string $time_start): string
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
    public function getTimeLastTask(int $user_id): string
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
    public function moveTask(int $user_id, string $time): void
    {
        $date = Carbon::today();
        $separatorDate = $this->getUserSeparator($user_id, $date);

        if($time>=$separatorDate){

            $taskTimes = $this->taskTimesRepository->getMoveTask($user_id, $date);

            foreach($taskTimes as $taskTime){
                $taskTime->update([
                    'date_start' => Carbon::parse($taskTime->date_start)->addMinutes(30),
                    'date_end' => Carbon::parse($taskTime->date_end)->addMinutes(30)
                ]);
            }
        }
    }

    /**
     * Get user task query
     *
     * @param int $user_id
     * @return Collection<Task>
     */
    public function getOpenUserTask(int $user_id): Collection
    {
        return TaskHelper::getOpenUserTask($user_id);
    }

    /**
     * moving tasks backward
     *
     * @param Collection<Task> $task
     * @return Collection<TaskTime>
     */
    public function movingTasksBackward(object $task): Collection
    {
        $actualTaskTime = TaskTime::where('task_id', $task->id)->first();
        $date_start = Carbon::parse($actualTaskTime->date_start);
        $date_end = Carbon::parse($actualTaskTime->date_end);

        $totalDuration = $date_end->diffInMinutes($date_start);

        $moveTasksTime = $this->taskTimesRepository->movingTasksBackward($task, $actualTaskTime, $date_start);
        
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
}

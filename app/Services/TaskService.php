<?php

namespace App\Services;

use App\DTO\Task\SeparatorDTO;
use App\Entities\Label;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Enums\CourierName;
use App\Repositories\Couriers;
use App\Repositories\TaskRepository;
use App\Repositories\Tasks;
use App\Repositories\TaskTimeRepository;
use App\Repositories\TaskTimes;
use App\Services\Label\RemoveLabelService;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskService
{
    public const USERS_SEPARATOR = [36, 37, 38];

    public const COLOR_SEPARATOR = ['FF0000', 'E6C74D', '194775'];

    public function __construct(
        protected readonly TaskRepository     $taskRepository,
        protected readonly TaskTimeRepository $taskTimeRepository,
        protected readonly Tasks              $tasksRepository,
        protected readonly TaskTimeService    $taskTimeService,
        protected readonly TaskTimes          $taskTimesRepository,
        protected readonly Couriers           $couriersRepository
    ) {
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

        $couriers = Couriers::getActiveOrderByNumber();
        foreach ($couriers as $courier) {
            $tasksByDay = $dates;
            $tasks = Tasks::getWarehouseUserWithBlueHammerLabelQuery([$courier->courier_name])->get();
            foreach ($tasks as $task) {
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
     * @return Task|null
     * @throws Exception
     */
    public function prepareTask($package_type, $skip): ?Task
    {
        $courierArray = CourierName::DELIVERY_TYPE_FOR_TASKS[$package_type] ?? [];
        if (empty($courierArray)) {
            throw new Exception(__('order_packages.message.package_error'));
        }
        return Tasks::getWarehouseUserWithBlueHammerLabelQuery($courierArray)->offset($skip)->first();
    }

    /**
     * Prepare Auto task for handling
     *
     * @param string $package_type
     * @param int $skip
     * @return Task|null
     * @throws Exception
     */
    public function prepareAutoTask(string $package_type, int $skip): ?Task
    {
        $courierArray = CourierName::DELIVERY_TYPE_FOR_TASKS[$package_type] ?? [];
        if (empty($courierArray)) {
            throw new Exception(__('order_packages.message.package_error'));
        }

        $groupTask = $this->groupTaskByShipmentDate()[$package_type];

        $past = $groupTask['past'];
        $future = $groupTask['future'];
        if (count($past)>0) {
            return $past[0];
        }
        unset($groupTask['past']);
        unset($groupTask['future']);
        foreach ($groupTask as $date => $tasks) {
            if (count($tasks)>0) {
                return $tasks[0];
            }
        }

        return count($future) > 0 ? $future[0] : null;
    }

    /**
     * Prepare task for handling
     *
     * @return SeparatorDTO[]
     */
    public function getSeparator(int $id, string $start, string $end): array
    {
        $separators = [];
        foreach (self::USERS_SEPARATOR as $user_id) {
            $taskTime = Tasks::getSeparator($user_id, $id, $start, $end);

            if ($taskTime->count() > 0) {
                $taskTimeFirst = $taskTime->first();

                $start = Carbon::parse($taskTimeFirst->date_start)->subMinute();
                $end = Carbon::parse($taskTimeFirst->date_start);

                $separators[] = new SeparatorDTO(
                    null,
                    $taskTimeFirst->task->user_id,
                    '',
                    $start->format('Y-m-d\TH:i'),
                    $end->format('Y-m-d\TH:i'),
                    '#000000',
                    'SEPARATOR',
                    null,
                    null
                );
            }
        }

        return $separators;
    }

    /**
     * Prepare task for handling
     *
     */
    public function getUserSeparator(int $user_id, Carbon $date): string
    {
        $separatorDate = '';
        $separators = $this->getSeparator(16, $date->format('Y-m-d') . ' 00:00:00', $date->format('Y-m-d') . ' 23:59:59');
        foreach ($separators as $separator) {
            if (isset($separator->resourceId) && $separator->resourceId == $user_id) {
                $separatorDate = Carbon::parse($separator->end)->format('Y-m-d H:i:s');
            }
        }
        return $separatorDate;
    }

    /**
     * Transfers Task
     */
    public function transfersTask(): void
    {
        $today = Carbon::today()->addDay();
        foreach (self::USERS_SEPARATOR as $user_id) {
            $time_start = Carbon::parse($today->format('Y-m-d') . ' ' . $this->getTimeLastTask($user_id));
            foreach (self::COLOR_SEPARATOR as $color) {
                $time_start = $this->prepareTransfersTask($user_id, $color, $time_start);
            }
        }
    }

    /**
     * Prepare Transfer Task
     *
     */
    public function prepareTransfersTask(int $userId, string $color, string $timeStart): string
    {
        $date = Carbon::today()->addDay();

        $tasks = Tasks::getTransfersTask($userId, $color, $date);
        foreach ($tasks as $task) {
            $timeStart = $this->updateTransfersTask($task->taskTime->id, $timeStart);
            $this->moveTask($userId, $timeStart);
        }

        return $timeStart;
    }

    /**
     * Update task time
     *
     */
    public function updateTransfersTask(int $taskTime_id, string $time_start): string
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
     * Get last task time
     *
     */
    public function getTimeLastTask(int $user_id): string
    {
        $date = Carbon::today()->addDay();
        $taskTime = TaskTimes::getTimeLastTask($user_id, $date);
        $firstTaskTime = $taskTime->first();

        return ($firstTaskTime) ? Carbon::parse($firstTaskTime->date_end)->format('H:i:s') : TaskTime::TIME_START;
    }

    /**
     * Updating transfer task time
     */
    public function moveTask(int $user_id, string $time): void
    {
        $date = Carbon::today()->addDay();
        $separatorDate = $this->getUserSeparator($user_id, $date);

        if ($time >= $separatorDate) {
            $taskTimes = TaskTimes::getMoveTask($user_id, $date);

            foreach ($taskTimes as $taskTime) {
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
     * @return Collection<Task>
     */
    public function getOpenUserTask(int $user_id): Collection
    {
        return Tasks::getOpenUserTask($user_id);
    }

    /**
     * moving tasks backward
     *
     * @param Task $task
     * @return Collection
     */
    public function movingTasksBackward(Task $task): Collection
    {
        $actualTaskTime = TaskTime::where('task_id', $task->id)->first();
        $date_start = Carbon::parse($actualTaskTime->date_start);
        $date_end = Carbon::parse($actualTaskTime->date_end);

        $totalDuration = $date_end->diffInMinutes($date_start);

        $moveTasksTime = TaskTimes::movingTasksBackward($task, $actualTaskTime, $date_start);

        foreach ($moveTasksTime as $taskTime) {
            $startNextTaskAt = $taskTime->date_start->subMinutes($totalDuration);
            $endNextTaskAt = $taskTime->date_end->subMinutes($totalDuration);
            $taskTime->update([
                'date_start' => $startNextTaskAt,
                'date_end' => $endNextTaskAt
            ]);
            $taskTime->refresh();
        }

        return $moveTasksTime;
    }


    /**
     * @param $task
     * @return bool|null
     */
    public function markTaskAsProduced($task): bool|null
    {
        $response = null;
        if ($task->childs->count()) {
            $task->childs->map(function ($child) use (&$response) {
                if ($child->order_id) {
                    $preventionArray = [];
                    $response = RemoveLabelService::removeLabels(
                        $child->order,
                        [Label::ORDER_ITEMS_UNDER_CONSTRUCTION],
                        $preventionArray,
                        [],
                        Auth::user()->id
                    );
                }
            });
        } elseif ($task->order_id) {
            $preventionArray = [];
            $response = RemoveLabelService::removeLabels(
                $task->order,
                [Label::ORDER_ITEMS_UNDER_CONSTRUCTION],
                $preventionArray,
                [],
                Auth::user()->id
            );
        }
        if ($response!==null) {
            return array_key_exists('success', $response);
        }

        return $response;
    }

    /**
     * Close the task
     *
     * @param $task
     * @return bool
     */
    public function closeTask(Task $task): bool
    {
        if ($task->parent_id !== null)
        {
            $parentTask = $task->parent->first();
            $this->saveClosedTask($parentTask);
            if ($parentTask->childs->count() > 0)
            {
                foreach ($parentTask->childs as $child)
                {
                    $this->saveClosedTask($child);
                }
            }
            return true;
        }

        $this->saveClosedTask($task);

        return true;
    }

    /**
     * Write down the date of completion of the task
     *
     * @param $task
     * @return void
     */
    public function saveClosedTask(Task $task): void
    {
        $end = Carbon::now();
        $end->second = 0;
        $start = Carbon::parse($task->taskTime->date_start);
        $start->second = 0;

        $task->taskTime->date_end = $end;
        if ($start >= $end)
        {
            $task->taskTime->date_end = $start->addMinutes(2);
        }
        $task->taskTime->save();
        $task->status = Task::FINISHED;
        $task->color = Task::LIGHT_GREEN_COLOR;
        $task->save();

        $prev = [];
        if ($task->order_id!==null)
        {
            AddLabelService::addLabels($task->order, [Label::ORDER_ITEMS_CONSTRUCTED], $prev, [], Auth::user()->id);
        }
        Log::info("Zadanie ". $task->id ." zostało zamknięte: ". $task->taskTime->date_end);
    }
}

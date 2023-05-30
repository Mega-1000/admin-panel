<?php declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Label;
use App\Entities\LabelGroup;
use App\Entities\Task;
use App\Entities\TaskTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Tasks
{

    /**
     * Get Task Salary Detail
     *
     * @param int $id
     * @param array $data
     * @return Collection<Task>
     */
    public static function getTaskSalaryDetail(int $id, array $data): Collection
    {
        return Task::with(['taskTime', 'taskSalaryDetail'])
            ->whereHas('taskTime',
                function ($query) use ($data) {
                    $query->whereDate('date_start', '>=', $data['start']);
                    $query->whereDate('date_end', '<=', $data['end']);
                })
            ->where('warehouse_id', $id)
            ->whereNull('parent_id')
            ->whereNull('rendering')
            ->get();
    }


    /**
     * Get Task Salary Detail
     *
     * @param int $taskId
     * @return Collection<Task>
     */
    public static function getChildTasks(int $taskId): Collection
    {
        return Task::with(['parent'])->where('parent_id', $taskId)->get();
    }

    /**
     * Transfers Task
     *
     * @param string $login
     * @param int $user_id
     * @return Collection<Task>
     */
    public static function getTaskLogin(string $login, int $user_id): Collection
    {
        return Task::with(['user', 'taskTime', 'taskSalaryDetail', 'order' => function ($q) use ($login) {
            $q->with(['customer' => function ($q) use ($login) {
                $q->where('login', $login);
            }]);

            $q->with(['labels' => function ($q) {
                $q->whereIn('labels.id',
                    [Label::BLUE_HAMMER_ID, Label::RED_HAMMER_ID, Label::ORDER_ITEMS_CONSTRUCTED]);
            }]);
        }])
            ->where('user_id', $user_id)
            ->whereNotNull('order_id')
            ->get();
    }

    /**
     * Get Separator
     *
     * @param int $user_id
     * @param int $id
     * @param string $start
     * @param string $end
     * @return Collection<TaskTime>
     */
    public static function getSeparator(int $user_id, int $id, string $start, string $end): Collection
    {
        return TaskTime::with('task')
            ->whereHas('task',
                function ($query) use ($user_id, $id) {
                    $query->where('user_id', $user_id);
                    $query->where('warehouse_id', $id);
                    $query->whereNull('parent_id');
                    $query->whereNull('rendering');
                })
            ->whereDate('date_start', '>=', $start)
            ->whereDate('date_end', '<=', $end)
            ->whereNull('transfer_date')
            ->orderBy('date_start', 'asc')
            ->get();
    }

    /**
     * Transfers Task
     *
     * @param int $user_id
     * @return Collection<Task>
     */
    public static function getOpenUserTask(int $user_id): Collection
    {
        return Task::where('user_id', $user_id)
            ->whereHas('order', function ($query) {
                $query->whereDoesntHave('labels', function ($query) {
                    $query
                        ->where('labels.id', Label::ORDER_ITEMS_CONSTRUCTED)
                        ->orWhere('labels.id', Label::ORDER_ITEMS_REDEEMED_LABEL);
                })->whereHas('dates', function ($query) {
                    $query->orderBy('consultant_shipment_date_to');
                });
            })
            ->whereHas('taskTime', function ($query) {
                $query->where('date_start', '<=', Carbon::now()->format('Y-m-d H:i:s'));
            })
            ->get();
    }

    /**
     * Get Task with Label
     *
     * @param int $id
     * @return Task
     */
    public static function getTaskLabel(int $id): Task
    {
        return Task::with(['user', 'taskTime', 'taskSalaryDetail', 'order', 'childs' => function ($q) {
            $q->with(['order' => function ($q) {
                $q->with(['labels' => function ($q) {
                    $q->where('label_group_id', LabelGroup::PRODUCTION_LABEL_GROUP_ID)->orWhereIn('labels.id',
                        [Label::BLUE_BATTERY_LABEL_ID, Label::ORANGE_BATTERY_LABEL_ID, Label::ORDER_ITEMS_REDEEMED_LABEL]);
                }]);
            }]);
        }])->where('id', $id)->first();
    }

    /**
     * Get Tasks Waiting For Accept
     *
     * @return Collection<Task>
     */
    public static function getTasksWaitingForAccept(): Collection
    {
        return Task::with('taskTime', 'childs')
            ->has('childs')
            ->where('status', Task::WAITING_FOR_ACCEPT)
            ->get();
    }

    /**
     * Get Tasks Waiting For Accept
     *
     * @param int $id
     * @return Task
     */
    public static function getAllTaskWithChilds(int $id): Task
    {
        return Task::with('taskTime', 'childs')
            ->has('childs')
            ->find($id);
    }

    /**
     * Get Warehouse User With Blue Hammer Label Query
     *
     * @param array $couriersNames
     * @return Builder
     */
    public static function getWarehouseUserWithBlueHammerLabelQuery(array $couriersNames): Builder
    {
        return Task::where('user_id', Task::WAREHOUSE_USER_ID)
            ->whereHas('order', function ($query) use ($couriersNames) {
                $query->whereHas('packages', function ($query) use ($couriersNames) {
                    $query->whereIn('service_courier_name', $couriersNames);
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
     * Get Transfers Task
     *
     * @param int $user_id
     * @param string $color
     * @param Carbon $date
     * @return Collection<Task>
     */
    public static function getTransfersTask(int $user_id, string $color, Carbon $date): Collection
    {
        return Task::with(['taskTime'])
            ->where('status', Task::WAITING_FOR_ACCEPT)
            ->where('user_id', $user_id)
            ->where('color', $color)
            ->whereNull('parent_id')
            ->whereNull('rendering')
            ->whereHas('taskTime', function ($query) use ($date) {
                $query->where('date_start', 'like', $date->format('Y-m-d') . "%");
            })->where(function ($query) {
                $query->whereHas('order', function ($query) {
                    $query->whereHas('labels', function ($query) {
                        $query->where('labels.id', Label::RED_HAMMER_ID)
                            ->orWhere('labels.id', Label::BLUE_HAMMER_ID);
                    });
                })->orWhereDoesntHave('order');
            })->get();
    }

}

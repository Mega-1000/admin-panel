<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\OrderAddress;
use App\Entities\Task;
use App\Entities\TaskTime;
use App\Entities\Label;
use App\Entities\LabelGroup;
use Illuminate\Database\Eloquent\Collection;

class Tasks
{

    /**
     * Transfers Task
     * @param $login
     * @param $user_id
     * @return Collection<Task>
     */
    public static function getTaskLogin(string $login, int $user_id): Collection
    {
        return Task::with(['user', 'taskTime', 'taskSalaryDetail', 'order' => function ($q) use($login) {
            $q->with(['customer' => function ($q) use($login) {
                $q->where('login', $login);
            }]);

            $q->with(['labels' => function ($q) {
                $q->whereIn('labels.id',
                    [Label::BLUE_HAMMER_ID, Label::RED_HAMMER_ID, Label::ORDER_ITEMS_CONSTRUCTED]);
            }]);
        }])
        ->where('user_id',$user_id)
        ->whereNotNull('order_id')
        ->get();
    }

    /**
     * Get Separator
     * @param $user_id
     * @param $id
     * @param $start
     * @param $end
     * @return Collection<TaskTime>
     */
    public static function getSeparator(int $user_id,int $id, string $start, string $end): Collection
    {
        return TaskTime::with('task')
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
        ->get();
    }

    /**
     * Transfers Task
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
        })->get();
    }
}

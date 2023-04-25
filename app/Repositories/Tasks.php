<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Task;
use App\Entities\Label;
use Illuminate\Database\Eloquent\Collection;

class Tasks
{
    /**
     * Get Task children
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
    public static function getChildren($id): Collection
    {
        return Task::with('taskTime','childs')
            ->has('childs')
            ->find($id);
    }


    /**
     * Get task query
     * @param string $courierArray
     */
    public static function getTaskQuery($courierArray): \Illuminate\Database\Eloquent\Builder
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
     */
    public static function transfersTask($user_id,$color,$date): Collection
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
     * Transfers Task
     * @param int $user_id
     */
    public static function getOpenUserTask($user_id): Collection
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
    
    /**
     * Transfers Task
     * @param string $login
     * @param int $user_id
     */
    public static function checkTaskLogin($login, $user_id): Collection
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
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\OrderAddress;
use App\Entities\Task;
use App\Entities\Label;
use App\Entities\LabelGroup;
use Illuminate\Database\Eloquent\Collection;

class Tasks
{
    /**
     * check task login
     *
     * @param $login
     * @param $address
     * @param $user_id
     */
    public function checkTaskLogin(string $login, object $address, int $user_id): int
    {
        $tasks = $this->getTaskLogin($login, $user_id);

        if(empty($tasks)){
            return 0;
        }

        $taskID = 0;
        foreach($tasks as $task){
            if(
                $task->order->getDeliveryAddress()->address == $address->address &&
                $task->order->getDeliveryAddress()->flat_number == $address->flat_number &&
                $task->order->getDeliveryAddress()->postal_code == $address->postal_code &&
                $task->order->getDeliveryAddress()->city == $address->city &&
                $task->order->getDeliveryAddress()->phone == $address->phone
            ){
                if($task->order->labels->contains('id', Label::BLUE_HAMMER_ID)){
                    if(!$task->order->labels->contains('id', Label::RED_HAMMER_ID)){
                        if(!$task->order->labels->contains('id', Label::ORDER_ITEMS_CONSTRUCTED)){
                            if($task->parent_id){
                                $taskID = $task->parent_id;
                            }else{
                                $taskID = $task->id;
                            }
                        }
                    }
                }else{
                    if(!$task->order->labels->contains('id', Label::ORDER_ITEMS_REDEEMED_LABEL)){
                        $taskID = -1; 
                    }
                }
            }
            
        }
        return $taskID;
    }
    
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
}

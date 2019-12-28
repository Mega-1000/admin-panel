<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Payment extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount', 'amount_left', 'customer_id', 'notices', 'promise', 'promise_date', 'created_at','updated_at'
    ];


    public function getOrdersUsingPayment()
    {
        $orderPayments = DB::table('order_payments')->where('master_payment_id', '=', $this->id)->get();

        $orders = [];

        foreach($orderPayments as $orderPayment)
        {
            if(array_key_exists($orderPayment->order_id, $orders)) {
                $orders[$orderPayment->order_id] = (float)$orders[$orderPayment->order_id] + (float)$orderPayment->amount;
            } else {
                $orders[$orderPayment->order_id] = (float)$orderPayment->amount;
            }

        }

        return $orders;
    }
}

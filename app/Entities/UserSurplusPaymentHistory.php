<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class UserSurplusPaymentHistory extends Model
{
    public $table = 'users_surplus_payments_history';

    public $fillable = ['user_id', 'order_id', 'surplus_amount', 'operation'];

}

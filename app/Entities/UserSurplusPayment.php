<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class UserSurplusPayment extends Model
{
    public $table = 'users_surplus_payments';

    public $fillable = ['surplus_amount', 'user_id', 'order_id'];

}

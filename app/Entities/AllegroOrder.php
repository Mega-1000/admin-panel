<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class AllegroOrder extends Model
{

    protected $fillable = [
        'order_id',
        'new_order_message_sent',
    ];

}

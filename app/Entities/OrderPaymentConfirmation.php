<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentConfirmation extends Model
{
    use HasFactory;

    public $fillable = [
        'file_url',
        'confirmed',
        'order_id',
    ];
}

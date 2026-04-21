<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class InvoiceRequest extends Model
{
    const STATUS_MISSING = 'MISSING';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'status'
    ];
}

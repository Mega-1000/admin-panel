<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Deliverer extends Model
{
    protected $fillable = ['name', 'net_payment_column_number', 'gross_payment_column_number_gross', 'letter_number_column_number'];
}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class PaymentImport extends Model
{
    use TransformableTrait;

    protected $table = 'payments_import';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_path'
    ];
}

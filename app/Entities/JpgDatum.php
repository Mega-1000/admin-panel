<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class JpgDatum extends Model
{
    protected $fillable = [
        'filename',
        'name',
        'row',
        'col',
        'subcol',
        'price',
        'order',
        'image'
    ];
}

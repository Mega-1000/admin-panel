<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'host',
        'port',
        'encryption',
        'password',
        'user_id',
    ];
}

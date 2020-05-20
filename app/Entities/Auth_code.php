<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Auth_code extends Model
{
    protected $table = 'auth_codes';
    protected $primaryKey = 'token';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

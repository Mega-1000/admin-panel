<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auth_code extends Model
{
    protected $table = 'auth_codes';
    protected $primaryKey = 'token';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

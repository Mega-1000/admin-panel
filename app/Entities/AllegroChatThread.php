<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class AllegroChatThread extends Model
{

    public function user() {
        return $this->belongsTo(User::class);
    }
}

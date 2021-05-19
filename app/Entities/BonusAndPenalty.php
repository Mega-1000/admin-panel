<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BonusAndPenalty extends Model
{

    protected $fillable = [
        'amount',
        'points',
        'user_id',
        'order_id',
        'cause',
        'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getAll()
    {
        $user = Auth::user();
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])) {
            return BonusAndPenalty::all();
        }
        return BonusAndPenalty::where('user_id', $user->id)->get();
    }
}

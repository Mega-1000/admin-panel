<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @property ?Order $order
 */
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

    public static function getAll()
    {
        $user = Auth::user();
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])) {
            return BonusAndPenalty::all();
        }
        return BonusAndPenalty::where('user_id', $user->id)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}

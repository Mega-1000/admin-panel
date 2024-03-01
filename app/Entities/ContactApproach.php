<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactApproach extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'referred_by_user_id',
        'done'
    ];

    public function referredByUser(): BelongsTo
    {
        return $this->belongsTo('user', 'id', 'referred_by_user_id');
    }
}

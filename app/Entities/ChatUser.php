<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property User $user
 * @property Customer $customer
 * @property Employee $employee
 */
class ChatUser extends Model
{
    use SoftDeletes;

    protected $table = 'chat_user';

    protected $casts = [
        'assigned_messages_ids' => 'json',
    ];

    public $attributes = [
        'assigned_messages_ids' => '{}',
    ];

    public $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

}

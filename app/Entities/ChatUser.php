<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatUser extends Model
{
    use SoftDeletes;

    protected $table = 'chat_user';

    protected $softDelete = true;

    protected $casts = [
        'assigned_messages_ids' => 'json',
    ];

    public $attributes = [
        'assigned_messages_ids' => [],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

}

<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
* Class Order.
*
* @property string $allegro_offer_id
* @property string $allegro_order_id
* @package namespace App\Entities;
*/

class AllegroChatThread extends Model
{

    protected $fillable = [
        'allegro_thread_id',
        'allegro_msg_id',
        'user_id',
        'allegro_user_login',
        'status',
        'subject',
        'content',
        'is_outgoing',
        'attachments',
        'type',
        'allegro_offer_id',
        'allegro_order_id',
        'original_allegro_date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

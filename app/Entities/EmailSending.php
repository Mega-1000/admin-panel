<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $order_id
 * @property int    $email_setting_id
 * @property string $email
 * @property string $title
 * @property string $content
 * @property string $attachment
 * @property string $scheduled_date
 * @property string $send_date
 * @property bool   $message_send
*/

class EmailSending extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'email_setting_id',
        'email',
        'title',
        'content',
        'attachment',
        'scheduled_date',
        'send_date',
        'message_send',
    ];

    protected $table = 'email_sending';
}

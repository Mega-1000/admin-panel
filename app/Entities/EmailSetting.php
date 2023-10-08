<?php

namespace App\Entities;

use App\Enums\EmailSettingsEnum;
use App\Facades\Mailer;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @property int     $id
 * @property string  $status - NEW | PRODUCED | PICKED_UP | PROVIDED | ADDRESS_CHANGED | PICKED_UP_2 | NEW_ALLEGRO_MSG
 * @property int     $time
 * @property Boolean $isAllegro
 * @property string  $title
 * @property string  $content
 * @property string  $statusTitle
*/

class EmailSetting extends Model
{
    use HasFactory, SoftDeletes;

    const NEW = 'NEW';
    const PRODUCED = 'PRODUCED';
    const PICKED_UP = 'PICKED_UP';
    const PROVIDED = 'PROVIDED';
    const ADDRESS_CHANGED = 'ADDRESS_CHANGED';
    const PICKED_UP_2 = 'PICKED_UP_2';
    const NEW_ALLEGRO_MSG = 'NEW_ALLEGRO_MSG';

    protected $fillable = [
        'status',
        'time',
        'title',
        'content',
        'is_allegro',
    ];

    public function getStatusUserFriendlyName(): string
    {
        try {
            return EmailSettingsEnum::fromKey($this->status);
        } catch (Exception $exception) {
            return 'Nieznany status';
        }
    }

    public function sendEmail(string $email, Order $order): void
    {
        EmailSending::create([
            'email' => $email,
            'title' => $this->title,
            'content' => $this->content,
            'email_setting_id' => $this->id,
            'order_id' => $order->id,
            'scheduled_date' => now(),
        ]);
    }
}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EmailSettingsEnum;

/**
 * @property int    $id
 * @property string $status - NEW | PRODUCED | PICKED_UP | PROVIDED | ADDRESS_CHANGED | PICKED_UP_2 | NEW_ALLEGRO_MSG
 * @property int    $time
 * @property string $title
 * @property string $content
 * @property string $statusTitle
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
        'content'
    ];

    public function getStatusUserFriendlyName(): string
    {
        return EmailSettingsEnum::fromKey( $this->status );
    }
}

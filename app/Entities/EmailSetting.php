<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EmailSettingsEnum;

/**
 * @property int    $id
 * @property string $status - NEW | PRODUCED | PICKED_UP | PROVIDED | ADDRESS_CHANGED | PICKED_UP_2
 * @property int    $time
 * @property string $title
 * @property string $content
 * @property string $statusTitle
*/

class EmailSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'time',
        'title',
        'content'
    ];
}

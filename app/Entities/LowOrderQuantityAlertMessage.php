<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @class LowOrderQuantityAlertMessage
 *
 * @property string $attachment_name
 * @property string $title
 * @property string $message
 * @property int $delay_time
 * @property int $low_order_quantity_alert_id
 */
class LowOrderQuantityAlertMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachment_name',
        'title',
        'message',
        'delay_time',
        'low_order_quantity_alert_id',
    ];

    public function alert(): BelongsTo
    {
        return $this->belongsTo(LowOrderQuantityAlert::class);
    }
}

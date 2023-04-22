<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatAuction extends Model
{
    use HasFactory;

    /**
     * @property int $id
     * @property string $end_of_auction
     * @property string $date_of_delivery
     * @property int $price
     * @property int $quality
     * @property int $chat_id
     * @property Chat $chat
     * @property bool $confirmed
     */

    protected $fillable = [
        'end_of_auction',
        'date_of_delivery',
        'price',
        'quality',
        'chat_id',
        'confirmed',
    ];

    /**
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}

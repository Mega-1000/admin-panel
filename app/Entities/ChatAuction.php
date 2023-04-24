<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatAuction extends Model
{
    use HasFactory;

    /**
     * @property integer $id
     * @property string $end_of_auction
     * @property string $date_of_delivery
     * @property integer $price
     * @property integer $quality
     * @property integer $chat_id
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

    /**
     * @return HasMany
     */
    public  function offers(): HasMany
    {
        return $this->hasMany(ChatAuctionOffer::class);
    }

    /**
     * @return HasMany
     */
    public  function firms(): HasMany
    {
        return $this->hasMany(ChatAuctionFirm::class);
    }
}

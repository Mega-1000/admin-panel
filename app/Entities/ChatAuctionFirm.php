<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatAuctionFirm extends Model
{
    use HasFactory;

    /**
     * @property int $id
     * @property int $chat_auction_id
     * @property int $firm_id
     * @property ChatAuction $chatAuction
     * @property string $token
     */

    protected $fillable = [
        'chat_auction_id',
        'firm_id',
        'token'
    ];

    public function chatAuction(): BelongsTo
    {
        return $this->belongsTo(ChatAuction::class);
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }
}

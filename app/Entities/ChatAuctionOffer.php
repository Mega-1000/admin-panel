<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatAuctionOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_auction_id',
        'firm_id',
        'price',
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

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
        'commercial_price_net',
        'basic_price_net',
        'calculated_price_net',
        'aggregate_price_net',
        'commercial_price_gross',
        'basic_price_gross',
        'calculated_price_gross',
        'aggregate_price_gross',
        'order_item_id',
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

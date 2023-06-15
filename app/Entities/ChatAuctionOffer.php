<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
        'send_notification'
    ];

    public function chatAuction(): BelongsTo
    {
        return $this->belongsTo(ChatAuction::class);
    }

    public function auctionFirm(): BelongsTo
    {
        return $this->belongsTo(ChatAuctionFirm::class, 'firm_id');
    }

    public function firm(): HasOneThrough
    {
        return $this->hasOneThrough(
            Firm::class,
            ChatAuctionFirm::class,
            'id',
            'id',
            'firm_id'
        );
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}

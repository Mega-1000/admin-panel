<?php

namespace App\Repositories;

use App\Entities\ChatAuctionOffer;
use Illuminate\Database\Eloquent\Model;

class ChatAuctionOffers
{
    /**
     * @param string $v
     * @return Model
     */
    public function getOfferByName(string $v): Model
    {
        return ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($v) {
            $query->whereHas('product', function ($query) use ($v) {
                $query->where('name', $v);
            });
        })
        ->first();
    }

    /**
     * @param object $v
     * @return Model
     */
    public function getOfferByOrderItem(object $v): Model
    {
        return ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($v) {
            $query->whereHas('product', function ($query) use ($v) {
                $query->where('name', $v->product->name);
            });
        })
        ->first();
    }
}

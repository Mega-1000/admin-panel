<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class PackageTemplate extends Model
{

    const STATUS_NEW = 'NEW';
    const WAITING_FOR_CANCELLED = 'WAITING_FOR_CANCELLED';
    public const SENDING = 'SENDING';
    public const WAITING_FOR_SENDING = 'WAITING_FOR_SENDING';
    const DELIVERED = 'DELIVERED';
    const CANCELLED = 'CANCELLED';
    
    public function scopeAllegroDeliveryMethod($query, $method) {
        return $query->where('allegro_delivery_method', 'like', '%"'.$method.'"%');
    }
}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class WarehouseOrder extends Model
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_at', 'updated_at', 'symbol', 'shipment_date', 'confirmation_date', 'company', 'email', 'confirmation', 'description', 'arrival_date', 'status', 'warehouse_id', 'consultant_comment_date', 'consultant_comment', 'warehouse_comment', 'comments_for_warehouse',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(WarehouseOrderItem::class);
    }
}

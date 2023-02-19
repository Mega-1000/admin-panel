<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductStockPosition.
 *
 * @package namespace App\Entities;
 */
class ProductStockPosition extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_stock_id',
        'lane',
        'bookstand',
        'shelf',
        'position',
        'position_quantity'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(ProductStock::class, 'product_stock_id', 'id');
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function return()
    {
        return $this->hasMany(OrderReturn::class, 'product_stock_position_id', 'id');
    }

    public $customColumnsVisibilities = [
        'lane',
        'bookstand',
        'shelf',
        'position',
        'position_quantity',
        'created_at',
    ];
}

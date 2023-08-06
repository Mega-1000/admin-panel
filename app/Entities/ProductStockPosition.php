<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductStockPosition.
 * @property int $id
 * @property int $position_quantity
 * @property int $product_stock_id
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
     * @return BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class, 'product_stock_id', 'id');
    }



    /**
     * @return HasMany
     */
    public function return(): HasMany
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

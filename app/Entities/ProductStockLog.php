<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\User;

/**
 * Class ProductStockLog.
 *
 * @package namespace App\Entities;
 */
class ProductStockLog extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_stock_id',
        'product_stock_position_id',
        'action',
        'quantity',
        'user_id',
        'order_id',
        'stock_quantity_after_action',
        'comments',
    ];

    /**
     * @return BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class, 'product_stock_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public $customColumnsVisibilities = [

        'product_stock_id' ,
        'product_stock_position_id' ,
        'action' ,
        'quantity' ,
        'username' ,
        'firstname',
        'lastname' ,
        'add' ,
        'delete' ,
        'created_at' ,
    ];

}

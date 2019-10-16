<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
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
        'order_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
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

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderItem.
 *
 * @package namespace App\Entities;
 */
class OrderItem extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'net_purchase_price_commercial_unit',
        'net_purchase_price_basic_unit',
        'net_purchase_price_calculated_unit',
        'net_purchase_price_aggregate_unit',
        'net_purchase_price_the_largest_unit',
        'net_selling_price_commercial_unit',
        'net_selling_price_basic_unit',
        'net_selling_price_calculated_unit',
        'net_selling_price_aggregate_unit',
        'net_selling_price_the_largest_unit'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function realProduct()
    {
        $productName = $this->product->symbol;
        if(strpos($productName, '-') !== false) {
            $variable = substr($productName, 0, strpos($productName, "-"));
        } else {
            $variable = $productName;
        }
        $product =  DB::table('products')->where('symbol', 'LIKE', $variable)->first();
        $stock = DB::table('product_stocks')->where('product_id', '=', $product->id)->first();

        return $stock->quantity;

    }

    public function realProductPositions()
    {
        $productName = $this->product->symbol;
        if(strpos($productName, '-') !== false) {
            $variable = substr($productName, 0, strpos($productName, "-"));
        } else {
            $variable = $productName;
        }

        $product =  DB::table('products')->where('symbol', 'LIKE', '%' . $variable . '%')->first();
        $stock = DB::table('product_stocks')->where('product_id', '=', $product->id)->first();
        $positions = DB::table('product_stock_positions')->where('product_stock_id', '=', $stock->id)->get();

        return $positions;

    }
}

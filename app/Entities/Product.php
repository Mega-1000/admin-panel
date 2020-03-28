<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Product.
 *
 * @package namespace App\Entities;
 */
class Product extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'symbol',
        'name',
        'multiplier_of_the_number_of_pieces',
        'url',
        'url_for_website',
        'weight_trade_unit',
        'weight_collective_unit',
        'weight_biggest_unit',
        'weight_base_unit',
        'description',
        'video_url',
        'manufacturer_url',
        'priority',
        'meta_price',
        'meta_description',
        'meta_keywords',
        'status',
        'description_photo_promoted',
        'description_photo_table',
        'description_photo_contact',
        'description_photo_details',
        'set_symbol',
        'set_rule',
        'manufacturer',
        'additional_info1',
        'additional_info2',
        'supplier_product_name',
        'product_name_on_collective_box',
        'product_name_supplier',
        'product_name_supplier_on_documents',
        'supplier_product_symbol',
        'product_name_manufacturer',
        'symbol_name_manufacturer',
        'pricelist_name',
        'calculator_type',
        'product_group',
        'date_of_price_change',
        'date_of_the_new_prices',
        'product_group_for_change_price',
        'products_related_to_the_automatic_price_change',
        'text_price_change',
        'text_price_change_data_first',
        'text_price_change_data_second',
        'text_price_change_data_third',
        'text_price_change_data_fourth',
        'subject_to_price_change',
        'value_of_price_change_data_first',
        'value_of_price_change_data_second',
        'value_of_price_change_data_third',
        'value_of_price_change_data_fourth',
        'pattern_to_set_the_price',
        'variation_unit',
        'variation_group',
        'review',
        'quality',
        'quality_to_price',
        'comments',
        'value_of_the_order_for_free_transport',
        'show_on_page',
        'trade_group_name',
        'displayed_group_name'
    ];

    public $customColumnsVisibilities = [
        'symbol',
        'name',
        'manufacturer',
        'product_name_manufacturer',
        'product_name_on_commercial_packing',
        'ean_of_commercial_packing',
        'product_name_on_collective_box',
        'ean_of_collective_packing',
        'quantity',
        'reserved',
        'commercial_free',
        'number_of_sale_units_in_the_pack',
        'full_packs',
        'numbers_of_basic_commercial_units_in_transport_pack',
        'numbers_in_transport_pack_full',
        'numbers_on_a_layer',
        'number_of_layers_full',
        'numbers_on_layer_last',
        'firstAlley',
        'firstStillage',
        'firstShelf',
        'firstPosition',
        'secondAlley',
        'secondStillage',
        'secondShelf',
        'secondPosition',
        'thirdAlley',
        'thirdStillage',
        'thirdShelf',
        'thirdPosition',
        'create_commercial',
        'net_purchase_price_commercial_unit_after_discounts',
        'net_purchase_price_commercial_unit',
        'unit_commercial',
        'create_basicd',
        'net_purchase_price_basic_unit_after_discounts',
        'net_purchase_price_basic_unit',
        'unit_basic',
        'create_calculation',
        'net_purchase_price_calculated_unit_after_discounts',
        'net_purchase_price_calculation_unit',
        'calculation_unit',
        'number_of_sale_units_in_the_pack',
        'create_collective',
        'net_purchase_price_aggregate_unit_after_discounts',
        'net_purchase_price_aggregate_unit',
        'unit_of_collective',
        'number_of_trade_items_in_the_largest_unit',
        'create_transport',
        'net_purchase_price_the_largest_unit_after_discounts',
        'net_purchase_price_the_largest_unit',
        'unit_biggest',
        'discount1',
        'discount2',
        'discount3',
        'show_on_page',
        'gross_selling_price_basic_unit',
        'gross_purchase_price_basic_unit_after_discounts',
        'gross_selling_price_commercial_unit',
        'gross_purchase_price_commercial_unit_after_discounts',
        'gross_selling_price_calculated_unit',
        'gross_purchase_price_calculated_unit_after_discounts',
        'gross_selling_price_aggregate_unit',
        'gross_purchase_price_aggregate_unit_after_discounts',
        'gross_selling_price_the_largest_unit',
        'gross_purchase_price_the_largest_unit_after_discounts'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function packing()
    {
        return $this->hasOne(ProductPacking::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function price()
    {
        return $this->hasOne(ProductPrice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock()
    {
        return $this->hasOne(ProductStock::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function parentProduct()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function tradeGroups()
    {
        return $this->hasMany(ProductTradeGroup::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'product_name_supplier', 'symbol');
    }

    public function isInTransportGroup()
    {
        return $this->tradeGroups()->count() > 0;
    }

    public function hasAllTransportParameters()
    {
        return $this->packing->warehouse && $this->packing->recommended_courier && $this->packing->packing_name;
    }

    public static function getDefaultProduct()
    {
        return Product::where('symbol', 'TWSU')->first();
    }
}

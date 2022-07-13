<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderPackage.
 *
 * @package namespace App\Entities;
 */
class OrderPackage extends Model implements Transformable
{
    use TransformableTrait;
    
    public $customColumnsVisibilities = [
        'number',
        'size_a',
        'size_b',
        'size_c',
        'shipment_date',
        'delivery_date',
        'delivery_courier_name',
        'service_courier_name',
        'weight',
        'quantity',
        'container_type',
        'shape',
        'cash_on_delivery',
        'notices',
        'status',
        'new',
        'sending',
        'waiting_for_sending',
        'delivered',
        'cancelled',
        'sending_number',
        'letter_number',
        'cost_for_client',
        'cost_for_company',
        'real_cost_for_company',
        'created_at',
        'actions',
        'waiting_for_cancelled',
        'reject_cancelled',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'number',
        'size_a',
        'size_b',
        'size_c',
        'shipment_date',
        'delivery_date',
        'service_courier_name',
        'delivery_courier_name',
        'weight',
        'quantity',
        'container_type',
        'shape',
        'cash_on_delivery',
        'notices',
        'status',
        'sending_number',
        'letter_number',
        'cost_for_client',
        'cost_for_company',
        'inpost_url',
        'chosen_data_template',
        'content',
        'send_protocol',
        'symbol',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function realCostsForCompany(): HasMany
    {
        return $this->hasMany('App\Entities\OrderPackageRealCostForCompany');
    }

    public function packedProducts()
    {
        return $this->belongsToMany('App\Entities\Product')->withPivot('quantity');
    }

    public function getPathToSticker()
    {
        if ($this->service_courier_name === 'INPOST' || $this->service_courier_name === 'ALLEGRO-INPOST') {
            $path = "/storage/inpost/stickers/sticker$this->letter_number.pdf";
        } else if ($this->delivery_courier_name === 'DPD') {
            $path = "/storage/dpd/stickers/sticker$this->letter_number.pdf";
        } else if ($this->delivery_courier_name === 'POCZTEX') {
            $path = "/storage/pocztex/protocols/protocol$this->sending_number.pdf";
        } else if ($this->delivery_courier_name === 'JAS') {
            $path = "/storage/jas/protocols/protocol$this->sending_number.pdf";
            $path = "/storage/jas/labels/label$this->sending_number.pdf";
        } else if ($this->delivery_courier_name === 'GIELDA') {
            $path = "/storage/gielda/stickers/sticker$this->letter_number.pdf";
        } else if ($this->delivery_courier_name === 'ODBIOR_OSOBISTY') {
            $path = "/storage/odbior_osobisty/stickers/sticker$this->letter_number.pdf";
        }
        return $path;
    }

    public function getClientCosts()
    {
        return $this->cost_for_client;
    }

    public function getOurCosts()
    {
        return $this->cost_for_company + $this->cod_cost_for_us;
    }
}

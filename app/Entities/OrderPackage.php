<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderPackage.
 * @property string $delivery_courier_name
 * @property string $service_courier_name
 * @property string $symbol
 * @property string $status
 * @property integer $real_cost_for_company
 *
 * @property ?ShipmentGroup $shipmentGroup
 * @package namespace App\Entities;
 */
class OrderPackage extends Model implements Transformable
{
    use TransformableTrait;

    public array $customColumnsVisibilities = [
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
        'protection_method',
        'services',
    ];

    protected $dates = [
        'shipment_date',
        'delivery_date',
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
        'protection_method',
        'services',
        'real_cost_for_company_sum'
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        $realCostsForCompany = OrderPackageRealCostForCompany::limit(20)->get();
        foreach ($realCostsForCompany as $item) {
            $item->orderPackage->real_cost_for_company_sum = $item->orderPackage->real_cost_for_company_sum ?? 0;
                         $item->orderPackage->real_cost_for_company_sum += $item->cost;
                         $item->orderPackage->save();

                         echo $item->orderPackage->real_cost_for_company_sum;
                     }
        return $this->belongsTo(Order::class);
    }

    public function realCostsForCompany(): HasMany
    {
        return $this->hasMany(OrderPackageRealCostForCompany::class);
    }

    public function packedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function shipmentGroup(): BelongsTo
    {
        return $this->belongsTo(ShipmentGroup::class);
    }

    public function getPathToSticker(): string
    {
        $paths = [
            'INPOST' => "/storage/inpost/stickers/sticker$this->letter_number.pdf",
            'ALLEGRO-INPOST' => "/storage/inpost/stickers/sticker$this->letter_number.pdf",
            'DPD' => "/storage/dpd/stickers/sticker$this->letter_number.pdf",
            'POCZTEX' => "/storage/pocztex/protocols/protocol$this->sending_number.pdf",
            'JAS' => [
                "/storage/jas/protocols/protocol$this->sending_number.pdf",
                "/storage/jas/labels/label$this->sending_number.pdf"
            ],
            'GIELDA' => "/storage/gielda/stickers/sticker$this->letter_number.pdf",
            'ODBIOR_OSOBISTY' => "/storage/odbior_osobisty/stickers/sticker$this->letter_number.pdf",
            'DB' => "/storage/db_schenker/stickers/sticker$this->sending_number.pdf"
        ];

        $path = '';
        if (array_key_exists($this->service_courier_name, $paths)) {
            $path = $paths[$this->service_courier_name];
        } else if (array_key_exists($this->delivery_courier_name, $paths)) {
            $path = $paths[$this->delivery_courier_name];
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

    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_package_id', 'id');
    }
}

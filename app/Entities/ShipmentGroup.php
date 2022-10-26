<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ShipmentGroup.
 *
 * @package namespace App\Entities;
 */
class ShipmentGroup extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'courier_name',
        'package_type',
        'lp',
        'shipment_date',
        'sent',
        'closed',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'shipment_date'
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    static function getOpenGroups(): Collection
    {
        return self::where([
            'closed' => false
        ])->get();
    }

    public function getLabel(): string
    {
        return $this->courier_name . '-' . ($this->packing_type ? $this->packing_type . '-' : '') . $this->lp;
    }

    public function getNextLabel(): string
    {
        return $this->courier_name . '-' . ($this->packing_type ? $this->packing_type . '-' : '') . ($this->lp + 1);
    }
}

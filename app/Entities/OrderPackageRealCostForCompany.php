<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $order_package_id
 * @property int $deliverer_id
 * @property float $cost
 * @property string $type
 * @property OrderPackage $orderPackage
 */
class OrderPackageRealCostForCompany extends Model
{
    protected $table = 'order_packages_real_cost_for_company';

    protected $fillable = [
        'order_package_id',
        'deliverer_id',
        'cost',
        'type',
    ];

    public function orderPackage(): BelongsTo
    {
        return $this->belongsTo(OrderPackage::class);
    }
}

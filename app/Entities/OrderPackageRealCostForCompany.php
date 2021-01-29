<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPackageRealCostForCompany extends Model
{
    protected $table = 'order_packages_real_cost_for_company';

    protected $fillable = [
        'order_package_id',
        'deliverer_id',
        'cost',
    ];

    public function orderPackage(): BelongsTo
    {
        return $this->belongsTo('App\Entities\OrderPackage');
    }
}

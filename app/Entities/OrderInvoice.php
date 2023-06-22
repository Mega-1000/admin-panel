<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderInvoice.
 *
 * @package namespace App\Entities;
 */
class OrderInvoice extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_type',
        'invoice_name',
        'is_visible_for_client',
    ];


    /**
     * @return BelongsToMany
     */
    public function order(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_order_invoices', 'invoice_id', 'order_id');
    }

}

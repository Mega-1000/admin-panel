<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Status.
 *
 * @package namespace App\Entities;
 */
class Status extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color', 'status', 'message', 'generate_order_offer'
    ];

    /**
     * @return HasOne
     */
    public function status()
    {
        return $this->hasOne(Order::class);
    }

    /**
     * @return BelongsToMany
     */
    public function labelsToAddOnChange(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'order_status_changed_labels_to_add', 'status_id', 'label_id');
    }

    public $customColumnsVisibilities = [
        'name',
        'color',
        'status',
        'message',
        'created_at',
        'active',
        'pending'
    ];
}

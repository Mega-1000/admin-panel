<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderLabel.
 *
 * @package namespace App\Entities;
 */
class OrderLabel extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'label_id',
        'added_type'
    ];
	
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function order()
	{
		return $this->belongsTo(Order::class);
	}
	
	public function scopeRedeemed($query) {
		$query->where('label_id', Label::ORDER_ITEMS_REDEEMED_LABEL);
	}
	
	public function scopeApproved($query) {
		$query->where('label_id', Label::FINAL_CONFIRMATION_APPROVED);
	}
}

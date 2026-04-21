<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
	 * @return BelongsTo
	 */
	public function order(): BelongsTo
    {
		return $this->belongsTo(Order::class);
	}
}

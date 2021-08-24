<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FirmSource.
 *
 * @package namespace App\Entities;
 */
class FirmSource extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;
	
	protected $softDelete = true;
	public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firm_id', 'order_source_id'
    ];
	
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function firm()
	{
		return $this->belongsTo(Firm::class);
	}
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function orderSource()
	{
		return $this->belongsTo(OrderSource::class);
	}
	
    public function scopeByFirmAndSource($query, $firmId, $order_source_id) {
    	$query->where('firm_id', $firmId)
		    ->where('order_source_id', $order_source_id);
    }
}

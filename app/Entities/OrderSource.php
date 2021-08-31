<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderSource.
 *
 * @package namespace App\Entities;
 */
class OrderSource extends Model implements Transformable
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
        'name', 'short_name', 'multiple'
    ];
	
	public function firmSources()
	{
		return $this->hasMany(FirmSource::class);
	}
	
	public function scopeNotInUse($query, $firmId)
	{
		$usedSources = FirmSource::where('firm_id', '!=', $firmId)->get()->pluck('order_source_id');

		$query->where('multiple', true)
			->orWhere('id', $usedSources);
	}
}

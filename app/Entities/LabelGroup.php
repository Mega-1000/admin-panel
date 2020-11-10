<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class LabelGroups.
 *
 * @package namespace App\Entities;
 */
class LabelGroup extends Model implements Transformable
{
    use TransformableTrait;

    const PRODUCTION_LABEL_GROUP_ID = 2;
    public $customColumnsVisibilities = [
        'name',
        'created_at',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'order',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeLabels()
    {
        return $this->hasMany(Label::class)->where('status', 'ACTIVE');
    }
}

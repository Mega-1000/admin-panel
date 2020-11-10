<?php

namespace App\Entities;

use App\Enums\LabelStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function labels() : HasMany
    {
        return $this->hasMany(Label::class);
    }

    public function activeLabels(): HasMany
    {
        return $this->hasMany(Label::class)->where('status', LabelStatus::Active);
    }
}

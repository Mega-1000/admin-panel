<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TaskSalaryDetails.
 *
 * @package namespace App\Entities;
 */
class TaskSalaryDetails extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'consultant_notice',
        'consultant_value',
        'warehouse_notice',
        'warehouse_value'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

}

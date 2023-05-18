<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * Class TaskTime.
 *
 * @package namespace App\Entities;
 */
class TaskTime extends Model implements Transformable
{
    use TransformableTrait;

    public const TIME_START = '07:00:00';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'date_start',
        'date_end',
        'transfer_date'
    ];

    public function task() :BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Carbon\Carbon;

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

    protected $dates = [
        'date_start',
        'date_end',
        'transfer_date'
    ];

    protected $casts = [
        'task_id' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getDateStartAttribute($value)
    {
        return Carbon::parse($value)->timezone('Europe/Warsaw')->format('Y-m-d H:i:s');
    }

    public function getDateEndAttribute($value)
    {
        return Carbon::parse($value)->timezone('Europe/Warsaw')->format('Y-m-d H:i:s');
    }
}

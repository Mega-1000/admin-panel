<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * Class Task.
 *
 * @package namespace App\Entities;
 */
class Task extends Model implements Transformable
{
    use TransformableTrait;

    public const WAITING_FOR_ACCEPT = 'WAITING_FOR_ACCEPT';
    public const LIGHT_GREEN_COLOR = '32CD32';
    public const DEFAULT_COLOR = '194775';
    public const DISABLED_COLOR = 'd7d7d7';
    public const TO_CONFIRM_USER_ID = 36;
    const WAREHOUSE_USER_ID = 37;
    public const FINISHED = 'FINISHED';
    public const REJECTED = 'REJECTED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_id',
        'user_id',
        'order_id',
        'created_by',
        'name',
        'color',
        'status',
        'rendering',
        'parent_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($model)
        {
            $model->childs()->update(['parent_id' => null]);
        });
    }

    public function warehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order() :BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function taskSalaryDetail() :HasOne
    {
        return $this->hasOne(TaskSalaryDetails::class);
    }

    public function taskTime() :HasOne
    {
        return $this->hasOne(TaskTime::class);
    }

    public function reportProperty() :HasMany
    {
        return $this->hasMany(ReportProperty::class);
    }

    public function childs() :HasMany
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }

    public function parent() :HasMany
    {
        return $this->hasMany(Task::class, 'id', 'parent_id');
    }

}

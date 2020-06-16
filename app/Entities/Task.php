<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Task.
 *
 * @package namespace App\Entities;
 */
class Task extends Model implements Transformable
{
    use TransformableTrait;

    public const WAITING_FOR_ACCEPT = 'WAITING_FOR_ACCEPT';
    public const DEFAULT_COLOR = '194775';

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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function taskSalaryDetail()
    {
        return $this->hasOne(TaskSalaryDetails::class);
    }

    public function taskTime()
    {
        return $this->hasOne(TaskTime::class);
    }

    public function reportProperty()
    {
        return $this->hasMany(ReportProperty::class);
    }

    public function childs()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->hasMany(Task::class, 'id', 'parent_id');
    }

}

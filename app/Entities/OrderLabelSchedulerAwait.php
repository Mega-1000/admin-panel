<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderLabelSchedulerAwait.
 *
 * @package namespace App\Entities;
 */
class OrderLabelSchedulerAwait extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'labels_timed_after_addition_id',
    ];

    public $timestamps = false;

    public function getMainLabelName()
    {
        $pivotId = $this->labels_timed_after_addition_id;

        $res = \DB::table('labels')
            ->join('labels_timed_after_addition', function ($join) use ($pivotId) {
                $join->on('labels.id', "=", "labels_timed_after_addition.main_label_id")
                    ->where('labels_timed_after_addition.id', '=', $pivotId);
            })
            ->first();

        return $res->name;
    }

    public function getSchedulerConfig()
    {
        return \DB::table('labels_timed_after_addition')->where('id', '=', $this->labels_timed_after_addition_id)->first();
    }
}

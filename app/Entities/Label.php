<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Label.
 *
 * @package namespace App\Entities;
 */
class Label extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label_group_id',
        'name',
        'order',
        'color',
        'font_color',
        'status',
        'icon_name',
        'message',
        'manual_label_selection_to_add_after_removal'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function labelGroup()
    {
        return $this->belongsTo(LabelGroup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labelsToAddAfterAddition()
    {
        return $this->belongsToMany(Label::class, 'label_labels_to_add_after_addition', 'main_label_id', 'label_to_add_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labelsToAddAfterRemoval()
    {
        return $this->belongsToMany(Label::class, 'label_labels_to_add_after_removal', 'main_label_id', 'label_to_add_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labelsToRemoveAfterAddition()
    {
        return $this->belongsToMany(Label::class, 'label_labels_to_remove_after_addition', 'main_label_id', 'label_to_add_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labelsToRemoveAfterRemoval()
    {
        return $this->belongsToMany(Label::class, 'label_labels_to_remove_after_removal', 'main_label_id', 'label_to_add_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function timedLabelsAfterAddition()
    {
        return $this->belongsToMany(Label::class, 'labels_timed_after_addition', 'main_label_id', 'label_to_handle_id')
            ->withPivot(['id', 'to_add_type_a', 'to_remove_type_a', 'to_add_type_b', 'to_remove_type_b', 'to_add_type_c', 'to_remove_type_c']);
    }

    public $customColumnsVisibilities = [

        'name' ,
        'group' ,
        'color',
        'status',
        'icon' ,
        'created_at' ,
        'active',
        'pending'
    ];
}

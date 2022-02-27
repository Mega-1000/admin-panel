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

    const ORDER_ITEMS_REDEEMED_LABEL = 66;
    const BLUE_BATTERY_LABEL_ID = 74;
    const ORANGE_BATTERY_LABEL_ID = 67;
    const PACKAGE_NOTIFICATION_LABEL = 53;
    const PACKAGE_NOTIFICATION_SENT_LABEL = 52;
    const BLUE_HAMMER_ID = 47;
    const GREEN_HAMMER_ID = 48;
    const RED_HAMMER_ID = 96;
    const GRAY_HAMMER_ID = 149;
    const PRODUCTION_STOP_ID = 145;
    const ORDER_ITEMS_UNDER_CONSTRUCTION = 49;
    const ORDER_ITEMS_CONSTRUCTED = 50;
    const IS_NOT_PAID = 119;
    const ORDER_SURPLUS = 151;
    const FROM_SELLO = 136;
    const ORANGE_BAG = 51;
    const WAREHOUSE_MARK = 91;
    const CONSULTANT_MARK = 151;
    const SHIPPING_MARK = 152;
    const URGENT_INTERVENTION = 90;
    const MASTER_MARK = 153;
    const ORDER_FOR_REALISATION = 44;
    const BOOKED_FIRST_PAYMENT = 5;
    const WAREHOUSE_REMINDER = 77;
	const INVOICE_ISSUED_WITH_EXERTED_EFFECT = 42;

    const CHAT_TYPE = 'chat';
    const BONUS_TYPE = 'bonus';
    const PAYMENTS_IDS_FOR_TABLE = [119, 134, 120, 101, 99, 102];
    const PRODUCTION_IDS_FOR_TABLE = [96, 51, 54, 77, 118];
    const TRANSPORT_IDS_FOR_TABLE = [68, 103, 104, 105, 106];
    const ADDITIONAL_INFO_IDS_FOR_TABLE = [92, 89, 93, 45, 55, 57, 59, 61, 90];
    const INVOICE_IDS_FOR_TABLE = [63];
    const CUSTOMER_DATA_REMINDER_IDS = [53, 74];
    const DIALOG_TYPE_LABELS_IDS = [55, 56, 57, 58, 78, 79, 80, 81, 82, 83, 84, 85];
    const NOT_SENT_YET_LABELS_IDS = [self::BLUE_HAMMER_ID, self::GREEN_HAMMER_ID, self::ORDER_ITEMS_UNDER_CONSTRUCTION];
    const NOT_ADD_LABEL_CHECK_CORRECT = [49, 47, 48, 96, 149, 50, 51, 52, 53, 54, 77, 114];

    const ORDER_INVOICE_MSG_SENDED = 194;

    const ORDER_RECEIVED_INVOICE_TODAY = 195;
	const ORDER_RECEIVED_INVOICE_STANDARD = 198;

	const FINAL_CONFIRMATION_DECLINED = 196;

    public $customColumnsVisibilities = [
        'name',
        'group',
        'color',
        'status',
        'icon',
        'created_at',
        'active',
        'pending'
    ];
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
        'manual_label_selection_to_add_after_removal',
        'timed'
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
    public function labelsToAddAfterTimedLabel()
    {
        return $this->belongsToMany(Label::class, 'label_labels_to_add_after_timed_label', 'main_label_id', 'label_to_add_id');
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
}

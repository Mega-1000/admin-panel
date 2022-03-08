<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WorkingEvents extends Model
{
    const LOGIN = 'LOGIN';

    const ORDER_LIST_EVENT = 'ORDER_LIST';
    const ORDER_EDIT_EVENT = 'ORDER_EDIT';
    const ORDER_UPDATE_EVENT = 'ORDER_UPDATE';
    const ORDER_STORE_EVENT = 'ORDER_STORE';

    const ORDER_PAYMENT_EDIT_EVENT = 'ORDER_PAYMENT_EDIT';
    const ORDER_PAYMENT_CREATE_EVENT = 'ORDER_PAYMENT_CREATE';
    const ORDER_PAYMENT_STORE_EVENT = 'ORDER_PAYMENT_STORE';
    const ORDER_PAYMENT_UPDATE_EVENT = 'ORDER_PAYMENT_UPDATE';

    const ORDER_PACKAGES_EDIT_EVENT = 'ORDER_PACKAGES_EDIT';
    const ORDER_PACKAGES_CREATE_EVENT = 'ORDER_PACKAGES_CREATE';
    const ORDER_PACKAGES_STORE_EVENT = 'ORDER_PACKAGES_STORE';
    const ORDER_PACKAGES_UPDATE_EVENT = 'ORDER_PACKAGES_UPDATE';

    const LABEL_ADD_EVENT = 'LABEL_ADD';
    const LABEL_REMOVE_EVENT = 'LABEL_REMOVE';

    const CHAT_MESSAGE_ADD_EVENT = 'LABEL_ADD';

    const ACCEPT_DATES_EVENT = 'ACCEPT_DATES';
    const UPDATE_DATES_EVENT = 'UPDATE_DATES';

    const SAVE_SHIPPING_COMMENT_EVENT = 'SAVE_SHIPPING_COMMENT';
    const SAVE_WAREHOUSE_COMMENT_EVENT = 'SAVE_WAREHOUSE_COMMENT';
    const SAVE_CONSULTANT_COMMENT_EVENT = 'SAVE_CONSULTANT_COMMENT';
    const SAVE_FINANCIAL_COMMENT_EVENT = 'SAVE_FINANCIAL_COMMENT';

    const NOTICE_MAPPER = [
        Order::COMMENT_SHIPPING_TYPE => self::SAVE_SHIPPING_COMMENT_EVENT,
        Order::COMMENT_WAREHOUSE_TYPE => self::SAVE_WAREHOUSE_COMMENT_EVENT,
        Order::COMMENT_CONSULTANT_TYPE => self::SAVE_CONSULTANT_COMMENT_EVENT,
        Order::COMMENT_FINANCIAL_TYPE => self::SAVE_FINANCIAL_COMMENT_EVENT,
    ];

    protected $table = 'working_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event',
        'order_id',
        'user_id',
    ];

    public static function createEvent(string $event, int $orderId = null)
    {
        return self::create([
            'user_id' => Auth::user()->id,
            'event' => $event,
            'order_id' => $orderId
        ]);
    }
}

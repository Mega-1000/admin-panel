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

    const EVENT_LABEL_MAPPING = [
        self::ORDER_LIST_EVENT => 'Lista zamówień',
        self::ORDER_EDIT_EVENT => 'Edycja zamówienia',
        self::ORDER_UPDATE_EVENT => 'Aktualizacja zamówienia',
        self::ORDER_STORE_EVENT => 'Zapis zamówienia',

        self::ORDER_PAYMENT_EDIT_EVENT => 'Edycja płatności',
        self::ORDER_PAYMENT_CREATE_EVENT => 'Dodawanie płatności',
        self::ORDER_PAYMENT_STORE_EVENT => 'Zapis płatności',
        self::ORDER_PAYMENT_UPDATE_EVENT => 'Aktualizacjia płatności',

        self::ORDER_PACKAGES_EDIT_EVENT => 'Edycja przesyłki',
        self::ORDER_PACKAGES_CREATE_EVENT => 'Dodanie przesyłki',
        self::ORDER_PACKAGES_STORE_EVENT => 'Zapis przesyłki',
        self::ORDER_PACKAGES_UPDATE_EVENT => 'Aktualizacja przesyłki',

        self::LABEL_ADD_EVENT => 'Dodanie etykiety',
        self::LABEL_REMOVE_EVENT => 'Usunięcie etykiety',

        self::CHAT_MESSAGE_ADD_EVENT => 'Wysłanie wiadomości',

        self::ACCEPT_DATES_EVENT => 'Akceptacja daty',
        self::UPDATE_DATES_EVENT => 'Aktualizacja dat',

        self::SAVE_SHIPPING_COMMENT_EVENT => 'Zapis komentarza dostawcy',
        self::SAVE_WAREHOUSE_COMMENT_EVENT => 'Zapis komentarza magazynu',
        self::SAVE_CONSULTANT_COMMENT_EVENT => 'Zapis komentarza konsultanta',
        self::SAVE_FINANCIAL_COMMENT_EVENT => 'Zapis komentarza ksiegowości',
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

    public static function createEvent(string $event,int $orderId = null)
    {
        if(empty(Auth::user()) || !(Auth::user() instanceof User)) {
            return true;
        }
        return self::create([
            'user_id' => Auth::user()->id,
            'event' => $event,
            'order_id' => $orderId
        ]);
    }

    public function getTitle(): string
    {
        return self::EVENT_LABEL_MAPPING[$this->event];
    }

    public function getContent(): string
    {
        $content = self::EVENT_LABEL_MAPPING[$this->event];
        $content .= ' Data: ' . $this->created_at . ' ';
        if (!empty($this->order_id)) {
            $content .= 'W ramach obsługi zamówienia ' . $this->order_id;
        }
        return $content;
    }
}

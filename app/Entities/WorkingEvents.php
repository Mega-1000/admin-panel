<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Working events class.
 */
class WorkingEvents extends Model
{
    /**
     * Login
     */
    const LOGIN = 'LOGIN';

    /**
     * Order list event
     */
    const ORDER_LIST_EVENT = 'ORDER_LIST';

    /**
     * Order edit event
     */
    const ORDER_EDIT_EVENT = 'ORDER_EDIT';

    /**
     * Order update event
     */
    const ORDER_UPDATE_EVENT = 'ORDER_UPDATE';

    /**
     * Order store event
     */
    const ORDER_STORE_EVENT = 'ORDER_STORE';

    /**
     * Order payment edit event
     */
    const ORDER_PAYMENT_EDIT_EVENT = 'ORDER_PAYMENT_EDIT';

    /**
     * Order payment create event
     */
    const ORDER_PAYMENT_CREATE_EVENT = 'ORDER_PAYMENT_CREATE';

    /**
     * Order payment store event
     */
    const ORDER_PAYMENT_STORE_EVENT = 'ORDER_PAYMENT_STORE';

    /**
     * Order payment update event
     */
    const ORDER_PAYMENT_UPDATE_EVENT = 'ORDER_PAYMENT_UPDATE';

    /**
     * Order package edit event
     */
    const ORDER_PACKAGES_EDIT_EVENT = 'ORDER_PACKAGES_EDIT';

    /**
     * Order package create event
     */
    const ORDER_PACKAGES_CREATE_EVENT = 'ORDER_PACKAGES_CREATE';

    /**
     * Order package store event
     */
    const ORDER_PACKAGES_STORE_EVENT = 'ORDER_PACKAGES_STORE';

    /**
     * Order package update event
     */
    const ORDER_PACKAGES_UPDATE_EVENT = 'ORDER_PACKAGES_UPDATE';

    /**
     * Label add event
     */
    const LABEL_ADD_EVENT = 'LABEL_ADD';

    /**
     * Label remove event
     */
    const LABEL_REMOVE_EVENT = 'LABEL_REMOVE';

    /**
     * Chat message event
     */
    const CHAT_MESSAGE_ADD_EVENT = 'LABEL_ADD';

    /**
     * Accept dates event
     */
    const ACCEPT_DATES_EVENT = 'ACCEPT_DATES';

    /**
     * Update dates event
     */
    const UPDATE_DATES_EVENT = 'UPDATE_DATES';

    /**
     * Save shipping comment event
     */
    const SAVE_SHIPPING_COMMENT_EVENT = 'SAVE_SHIPPING_COMMENT';

    /**
     * Save warehouse comment event
     */
    const SAVE_WAREHOUSE_COMMENT_EVENT = 'SAVE_WAREHOUSE_COMMENT';

    /**
     * Save consultant comment event
     */
    const SAVE_CONSULTANT_COMMENT_EVENT = 'SAVE_CONSULTANT_COMMENT';

    /**
     * Save financial comment
     */
    const SAVE_FINANCIAL_COMMENT_EVENT = 'SAVE_FINANCIAL_COMMENT';

    /**
     * Notice mapper
     */
    const NOTICE_MAPPER = [
        Order::COMMENT_SHIPPING_TYPE => self::SAVE_SHIPPING_COMMENT_EVENT,
        Order::COMMENT_WAREHOUSE_TYPE => self::SAVE_WAREHOUSE_COMMENT_EVENT,
        Order::COMMENT_CONSULTANT_TYPE => self::SAVE_CONSULTANT_COMMENT_EVENT,
        Order::COMMENT_FINANCIAL_TYPE => self::SAVE_FINANCIAL_COMMENT_EVENT,
    ];

    /**
     * Event label mapping
     */
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

    /**
     * @var string
     */
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

    /**
     * Return event title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return self::EVENT_LABEL_MAPPING[$this->event];
    }

    /**
     * Get event content.
     *
     * @return string
     */
    public function getContent(): string
    {
        $content = self::EVENT_LABEL_MAPPING[$this->event];
        $content .= ' Data: ' . $this->created_at . ' ';

        return $content;
    }
}

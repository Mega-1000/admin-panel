<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Entities\Order;

class Orders
{
    /**
     * get chat orders (disputes) need support
     *
     * @return Collection|null $ordersNeedSupport
     */
    public static function getChatOrdersNeedSupport(): ?Collection
    {
        $ordersNeedSupport = Order::where('need_support', true)->whereHas('chat', function($q) {
            $q->whereNull('user_id');
        })->get();

        return $ordersNeedSupport;
    }

    /**
     * get orders without reminder for label
     *
     * @param int $labelId
     * @return Collection $ordersWithoutReminder
     */
    public static function getOrdersWithoutReminderForLabel(int $labelId = 224): Collection
    {
        return Order::query()->whereHas('labels', function ($query) use ($labelId) {
            $query->where('label_id', $labelId);
        })->where('reminder_date', null)->get();
    }

    /**
     * get all related orders.
     *
     * @param Order $order
     *
     * @return Collection
     */
    public static function getAllRelatedOrders(Order $order): Collection
    {
        return Order::where('master_order_id', '=', $order->id)->orWhere('id', $order->id)->with('payments')->get();
    }

    public static function getAllRelatedOrdersValue(Order $order): float
    {
        $orders = self::getAllRelatedOrders($order);

        $ordersValue = 0;
        foreach ($orders as $order) {
            $ordersValue += $order->getValue() ?? $order->declared_sum ?? 0;
        }

        return $ordersValue;
    }

    /**
     * @param Order $order
     *
     * @return float
     */
    public function getAllRelatedOrderPaymentsValue(Order $order): float
    {
        $payments = self::getAllRelatedOrderPayments($order);

        $paymentsValue = 0;
        foreach ($payments as $order) {
            $paymentsValue += $order->amount ?? $order->declared_sum ?? 0;
        }

        return $paymentsValue;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getAllRelatedOrderPayments(Order $order): array
    {
        $orders = self::getAllRelatedOrders($order);

        $orderPayments = [];
        foreach ($orders as $order) {
            foreach ($order->payments as $payment) {
                $orderPayments[] = $payment;
            }
        }

        return $orderPayments;
    }
}

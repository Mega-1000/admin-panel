<?php

namespace App\Repositories;

use App\Entities\Label;
use App\Entities\Order;
use App\Enums\OrderPaymentsEnum;
use App\Enums\PackageStatus;
use Illuminate\Support\Collection;

class Orders
{
    /**
     * get chat orders (disputes) need support
     *
     * @return Collection|null $ordersNeedSupport
     */
    public static function getChatOrdersNeedSupport(): ?Collection
    {
        return Order::where('need_support', true)->whereHas('chat', function($q) {
            $q->whereNull('user_id');
        })->get();
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
        return Order::where('master_order_id', '=', $order->id)->orWhere('id', '=', $order->id)->get();
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
    public static function getAllRelatedOrderPaymentsValue(Order $order): float
    {
        $payments = self::getAllRelatedOrderPayments($order);
        $paymentsValue = 0;
        foreach ($payments as $order) {
            if ($order->operation_type != "Zwrot towaru") {
                $paymentsValue += $order->amount ?? $order->declared_sum ?? 0;
            }
        }

        return $paymentsValue;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public static function getAllRelatedOrderPayments(Order $order): array
    {
        $orders = self::getAllRelatedOrders($order);
        $orderPayments = [];
        foreach ($orders as $order) {
            foreach ($order->payments as $payment) {
                if ($payment->status !== 'Rozliczona deklarowana' && $payment->operation_type !== 'Wpłata/wypłata bankowa - związana z fakturą zakupową' || $order->login == 'info@ephpolska.pl' || str_contains($payment->operation_type, 'przeksiegowanie')) {
                    $orderPayments[] = $payment;
                }
            }
        }

        return $orderPayments;
    }

    /**
     * @param int $order_id
     * @return object|null
     */
    public function getOrderWithCustomer(int $order_id): object|null
    {
        return Order::with(['customer','labels'])->where('id',$order_id)->first();
    }

    /**
     * @param Order $order
     *
     * @return float
     */
    public static function getOrderReturnGoods(Order $order): float
    {
        $payments = self::getAllRelatedOrderPayments($order);

        $paymentsValue = 0;
        foreach ($payments as $payment) {
            if ($payment->operation_type == "Zwrot towaru") {
                $paymentsValue += $payment->amount ?? 0;
            }
        }

        return $paymentsValue;
    }

    /**
     * @param int $labelId
     * @return Collection
     */
    public static function getAllOrdersAttachedToLabel(int $labelId): Collection
    {
        return Order::query()->whereHas('labels', function ($query) use ($labelId) {
            $query->where('label_id', $labelId);
        })->get();
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function getSumOfWTONPayments(Order $order): float
    {
        $payments = self::getAllRelatedOrderPayments($order);
        $paymentsValue = 0;
        foreach ($payments as $order) {
            if ($order->operation_type == OrderPaymentsEnum::KWON_STATUS) {
                $paymentsValue += $order->amount ?? $order->declared_sum ?? 0;
            }
        }

        return $paymentsValue;
    }

    public static function getSumOfBuyingInvoicesReturns(Order $order): float
    {
        return $order->payments()->where('operation_type', OrderPaymentsEnum::INVOICE_BUYING_OPERATION_TYPE)->sum('amount');
    }

    public function orderIsConstructed(Order $order): bool
    {
        return $order->labels()->where('label_id', Label::ORDER_ITEMS_CONSTRUCTED)->exists();
    }

    public function deleteNewOrderPackagesAndCancelOthers(Order $order): void
    {
        $order->packages()->where('status', PackageStatus::NEW)->delete();
        $order->packages()->whereNot('status', PackageStatus::NEW)->update(['status' => 'CANCELLED']);
    }

    public static function getOrdersNotCheckedAsShippedButRealizationDateIsPassed(): Collection
    {
        return Order::whereHas('orderWarehouseNotification', function ($query) {
                $query->where('realization_date', '<', now()->toDateString());
            })
            ->whereDoesntHave('labels', function ($query) {
                $query->where('label_id', 66);
            });
    }
}

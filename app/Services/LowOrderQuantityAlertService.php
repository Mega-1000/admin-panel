<?php

namespace App\Services;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\LowOrderQuantityAlertMessage;
use App\Entities\Order;
use App\Entities\ProductPacket;
use App\Jobs\AlertForOrderLowQuantityJob;
use App\NewsletterPacket;
use App\Repositories\OrderItems;

class LowOrderQuantityAlertService
{
    /**
     * Dispatch alerts for order
     *
     * @param Order $order
     * @return void
     */
    public function dispatchAlertsForOrder(Order $order): void
    {
        $alertsToSend = collect();

        LowOrderQuantityAlert::all()->each(function (LowOrderQuantityAlert $alert) use (&$order, &$alertsToSend) {
            if ($order->items()->whereHas('product', fn ($q) => $q->where('symbol', 'SUP-900-0'))->exists()) {
                return;
            }

            $finalQuantity = 0;

            $orderItems = OrderItems::getItemsWithProductsWithLowOrderQuantityAlertText($order->id);

            foreach ($orderItems as $item) {
                /** @var Order $order */
                $order = $item->order;

                $columnName = $alert->column_name;
                if (
                    in_array(
                        $item->product->$columnName,
                        explode(',', $alert->item_names)
                    )
                ) {
                    $finalQuantity += $item->quantity;
                }
            }

            if ($finalQuantity !== 0 && $finalQuantity < $alert->min_quantity) {
                $alertsToSend->push($alert);
            }
        });

        foreach (NewsletterPacket::all() as $packet) {
            $alertsToSendForPacket = collect();

            $packetAlertSymbols = explode(',', $packet->packet_products_symbols);

            // Check if all alert symbols are present in the $alertsToSend collection
            if ($alertsToSend->pluck('symbol')->intersect($packetAlertSymbols)->count() === count($packetAlertSymbols)) {
                $alertsToSendForPacket->push($packet);
            }
        }

        foreach ($alertsToSend as $alert) {
            /** @var LowOrderQuantityAlertMessage $message */
            foreach ($alert->messages as $message) {
                dispatch(new AlertForOrderLowQuantityJob($order, $message))->delay(\Carbon\Carbon::now()->addMinutes($message->delay_time));
            }
        }
    }

    public static function parseToken(string $text, int $orderId): string
    {
        $text = self::replaceForNewsletterLinks($text, $orderId);

        return str_replace('{idZamowienia}', $orderId, $text);
    }

    private static function replaceForNewsletterLinks(string $text, int $orderId): string
    {
        $order = Order::find($orderId);
        $products = $order->items->pluck('product')->unique();
        $categories = collect();
        $products->each(function ($product) use (&$categories) {
            $categories->push(explode('-', $product->symbol)[1]);
        });
        $categories = $categories->unique();

        $replaceText = '';
        foreach ($categories as $category) {
            $replaceText .= route('newsletter.generate', $category);
        }

        return str_replace('{linkiDoGazetki}', $replaceText, $text);
    }
}

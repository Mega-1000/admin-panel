<?php

namespace App\Services;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\Order;
use App\Jobs\AlertForOrderLowQuantityJob;
use App\NewsletterPacket;
use App\Repositories\OrderItems;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
        $alertsToSend = $this->collectAlertsToSend($order);

        $this->filterFromGroups($alertsToSend);

        $this->dispatchMessages($alertsToSend, $order);
    }

    /**
     * Collect alerts to send for the given order
     *
     * @param Order $order
     * @return Collection
     */
    private function collectAlertsToSend(Order $order): Collection
    {
        return LowOrderQuantityAlert::all()->filter(function (LowOrderQuantityAlert $alert) use ($order) {
            if ($order->items()->whereHas('product', fn ($q) => $q->where('symbol', 'SUP-900-0'))->exists()) {
                return false;
            }

            $finalQuantity = $this->calculateFinalQuantity($alert, $order);

            return $finalQuantity !== 0 && $finalQuantity < $alert->min_quantity;
        });
    }

    /**
     * Calculate the final quantity for the given alert and order
     *
     * @param LowOrderQuantityAlert $alert
     * @param Order $order
     * @return int
     */
    private function calculateFinalQuantity(LowOrderQuantityAlert $alert, Order $order): int
    {
        $finalQuantity = 0;

        $orderItems = OrderItems::getItemsWithProductsWithLowOrderQuantityAlertText($order->id);

        foreach ($orderItems as $item) {
            $columnName = $alert->column_name;

            if (in_array($item->product->$columnName, explode(',', $alert->item_names))) {
                $finalQuantity += $item->quantity;
            }
        }

        return $finalQuantity;
    }

    /**
     * Filter alerts from groups based on packet symbols
     *
     * @param Collection $alertsToSend
     * @return void
     */
    private function filterFromGroups(Collection $alertsToSend): void
    {
        foreach (NewsletterPacket::all() as $packet) {
            $packetAlertSymbols = explode(',', $packet->packet_products_symbols);
            $found = $alertsToSend->filter(fn ($alert) => in_array($alert->id, $packetAlertSymbols));

            if ($found->count() == count($packetAlertSymbols)) {
                $alertsToSend->forget($found->keys()->toArray())->push($found);
            }
        }
    }

    /**
     * Dispatch messages for the given alerts and order
     *
     * @param Collection $alertsToSend
     * @param Order $order
     * @return void
     */
    private function dispatchMessages(Collection $alertsToSend, Order $order): void
    {
        foreach ($alertsToSend as $alert) {
            foreach ($alert->messages as $message) {
                dispatch(new AlertForOrderLowQuantityJob($order, $message))->delay(Carbon::now()->addMinutes($message->delay_time));
            }
        }
    }

    /**
     * Parse the token in the given text for the specified order ID
     *
     * @param string $text
     * @param int $orderId
     * @return string
     */
    public static function parseToken(string $text, int $orderId): string
    {
        $text = self::replaceForNewsletterLinks($text, $orderId);

        return str_replace('{idZamowienia}', $orderId, $text);
    }

    /**
     * Replace links for newsletter in the given text based on the order ID
     *
     * @param string $text
     * @param int $orderId
     * @return string
     */
    private static function replaceForNewsletterLinks(string $text, int $orderId): string
    {
        $order = Order::find($orderId);
        $products = $order->items->pluck('product')->unique();
        $categories = $products->map(function ($product) {
            return explode('-', $product->symbol)[1];
        })->unique();

        $replaceText = $categories->reduce(function ($carry, $category) {
            return $carry . route('newsletter.generate', $category);
        }, '');

        return str_replace('{linkiDoGazetki}', $replaceText, $text);
    }
}

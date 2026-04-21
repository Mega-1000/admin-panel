<?php

namespace App\Services;

use App\Entities\ChatAuctionOffer;
use App\Entities\Order;
use App\Entities\Product;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\TransportSumCalculator;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

readonly class AuctionOffersCreatorService
{
    private OrderBuilder $orderBuilder;
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * @throws Exception
     */
    public function createOrder(array $order, $user): Model|Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $items = $this->createOrderItems($order);
        $id = $this->createNewOrder($user);

        foreach ($items as &$item) {
            $item['amount'] = 1;
        }

        $order = Order::query()->findOrFail($id);
        $this->assignItemsAndUpdateOrder($order, $items);

        return $order;
    }

    private function createOrderItems(array $orderNames): array
    {
        return array_map(function ($orderName) {
            $offer = $this->getChatAuctionOffer($orderName);
            $product = Product::query()->where('name', $orderName)->first()->toArray();

            return $product + $this->getOfferPrices($offer);
        }, $orderNames);
    }

    private function getChatAuctionOffer(string $productName): Model
    {
        return ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($productName) {
            $query->whereHas('product', function ($query) use ($productName) {
                $query->where('name', $productName);
            });
        })
            ->first();
    }

    private function getOfferPrices($offer): array
    {
        return [
            'commercial_price_net' => $offer->commercial_price_net,
            'basic_price_net' => $offer->basic_price_net,
            'calculated_price_net' => $offer->calculated_price_net,
            'aggregate_price_net' => $offer->aggregate_price_net,
            'commercial_price_gross' => $offer->commercial_price_gross,
            'basic_price_gross' => $offer->basic_price_gross,
            'calculated_price_gross' => $offer->calculated_price_gross,
            'aggregate_price_gross' => $offer->aggregate_price_gross,
        ];
    }

    /**
     * @throws ChatException
     */
    private function createNewOrder($user): int
    {
        $orderBuilder = new OrderBuilder();
        $orderBuilder
            ->setProductService($this->productService)
            ->setPackageGenerator(new BackPackPackageDivider())
            ->setPriceCalculator(new OrderPriceCalculator())
            ->setTotalTransportSumCalculator(new TransportSumCalculator)
            ->setUserSelector(new GetCustomerForNewOrder());
        $this->orderBuilder = $orderBuilder;

        ['id' => $id] = $this->orderBuilder->newStore([], $user);

        return $id;
    }

    /**
     * @throws Exception
     */
    private function assignItemsAndUpdateOrder($order, array $items): void
    {
        $this->orderBuilder->assignItemsToOrder($order, $items);

        foreach ($order->items as $item) {
            $offer = $this->getChatAuctionOffer($item->product->name);
            $prices = $this->getOfferPrices($offer);
            $item->update($prices);
        }
    }
}

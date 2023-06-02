<?php

namespace App\Services;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\ChatAuctionOffer;
use App\Entities\Firm;
use App\Entities\Order;
use App\Entities\Product;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\TransportSumCalculator;
use App\Mail\NotifyFirmAboutAuction;
use App\Repositories\ChatAuctionOffers;
use App\Repositories\Firms;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

readonly class ChatAuctionsService
{
    public function __construct(
        protected ProductService    $productService,
        protected ChatAuctionOffers $chatAuctionOffersRepository,
    ) {}

    /**
     * Get all firms for auction
     *
     * @param array $variations
     * @return array
     */
    public function getFirms(array $variations): array
    {
        $firms = [];
        foreach ($variations as $variation) {
            foreach ($variation as $item) {
                $firms[] = Firms::getFirmBySymbol($item['product_name_supplier']);
            }
        }

        return $firms;
    }

    /**
     * Create auction
     *
     * @param CreateChatAuctionDTO $data
     * @return Model
     */
    public function createAuction(CreateChatAuctionDTO $data): Model
    {
        return $data->chat->auctions()->create([
            'end_of_auction' => $data->end_of_auction,
            'date_of_delivery' => $data->date_of_delivery,
            'price' => $data->price,
            'quality' => $data->quality,
        ]);
    }

    /**
     * @throws DeliverAddressNotFoundException
     */
    public function confirmAuction(ChatAuction $auction): void
    {
        $order = $auction->chat->order;
        $variations = $this->productService->getVariations($order);

        $firms = $this->getFirms($variations);

        foreach ($firms as $firm) {
            Mailer::create()
                ->to($firm->email)
                ->send(new NotifyFirmAboutAuction($auction, $firm, $this->generateLinkForAuction($auction, $firm)));
        }

        $auction->update([
            'confirmed' => true,
        ]);
    }

    /**
     * Generate link for auction
     *
     * @param ChatAuction $auction
     * @param Firm $firm
     * @return string
     */
    public function generateLinkForAuction(ChatAuction $auction, Firm $firm): string
    {
        $token = Str::random(60);

        ChatAuctionFirm::query()->create([
            'chat_auction_id' => $auction->id,
            'firm_id' => $firm->id,
            'token' => $token,
        ]);

        return $token;
    }

    /**
     * Create offer for auction
     *
     * @param CreateChatAuctionOfferDTO $data
     * @return Model
     */
    public function createOffer(CreateChatAuctionOfferDTO $data): Model
    {
        $auction = ChatAuction::query()->findOrFail($data->chat_auction_id);

        return ChatAuctionOffer::query()->create([
            'chat_auction_id' => $auction->id,
            'commercial_price_net' => $data->commercial_price_net,
            'basic_price_net' => $data->basic_price_net,
            'calculated_price_net' => $data->calculated_price_net,
            'aggregate_price_net' => $data->aggregate_price_net,
            'commercial_price_gross' => $data->commercial_price_gross,
            'basic_price_gross' => $data->basic_price_gross,
            'calculated_price_gross' => $data->calculated_price_gross,
            'aggregate_price_gross' => $data->aggregate_price_gross,
            'order_item_id' => $data->order_item_id,
            'firm_id' => $data->firm_id,
        ]);
    }

    /**
     * @throws Exception
     */
    public function endAuction(ChatAuction $auction, string $order, $user): Model|Collection|Builder|array|null
    {
        $orders = json_decode($order, true);

        foreach ($orders as $order) {
            $this->createOrder($order, $user);
        }

        return null;
    }

    /**
     * @throws ChatException
     * @throws Exception
     */
    public function createOrder(array $order, $user): Model|Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $items = [];
        foreach ($order as  $k => $v) {
            $offer = ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($v) {
                $query->whereHas('product', function ($query) use ($v) {
                    $query->where('name', $v);
                });
            })
                ->first();


            $items[] = Product::query()->where('name', $v)->first()->toArray() + [
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

        $orderParams = [];
        $orderBuilder = new OrderBuilder();
        $orderBuilder
            ->setPackageGenerator(new BackPackPackageDivider())
            ->setPriceCalculator(new OrderPriceCalculator())
            ->setTotalTransportSumCalculator(new TransportSumCalculator)
            ->setUserSelector(new GetCustomerForNewOrder())
            ->setProductService($this->productService);

        ['id' => $id] = $orderBuilder->newStore($orderParams, $user);

        foreach ($items as &$item) {
            $item = $item + [
                    'amount' => 1,
                ];
        }

        $order = Order::query()->findOrFail($id);
        $orderBuilder->assignItemsToOrder($order, $items);
        $order = Order::query()->findOrFail($id);

        foreach ($order->items as $v) {
            $offer = ChatAuctionOffer::query()->whereHas('orderItem', function ($query) use ($v) {
                $query->whereHas('product', function ($query) use ($v) {
                    $query->where('name', $v->product->name);
                });
            })
                ->first();


            $v->update([
                'net_purchase_price_commercial_unit' => $offer->commercial_price_net,
                'net_purchase_price_basic_unit' => $offer->basic_price_net,
                'net_purchase_price_calculated_unit' => $offer->calculated_price_net,
                'net_purchase_price_aggregate_unit' => $offer->aggregate_price_net,
                'net_purchase_price_the_largest_unit' => $offer->aggregate_price_net,
                'net_selling_price_commercial_unit' => $offer->commercial_price_net,
                'net_selling_price_basic_unit' => $offer->basic_price_net,
                'net_selling_price_calculated_unit' => $offer->calculated_price_net,
                'net_selling_price_aggregate_unit' => $offer->aggregate_price_net,
                'net_selling_price_the_largest_unit' => $offer->aggregate_price_net,
                'net_purchase_price_commercial_unit_after_discounts' => $offer->commercial_price_net,
                'net_purchase_price_basic_unit_after_discounts' => $offer->basic_price_net,
                'net_purchase_price_calculated_unit_after_discounts' => $offer->calculated_price_net,
                'net_purchase_price_aggregate_unit_after_discounts' => $offer->aggregate_price_net,
                'net_purchase_price_the_largest_unit_after_discounts' => $offer->aggregate_price_net,
                'gross_selling_price_commercial_unit' => $offer->commercial_price_gross,
                'gross_selling_price_basic_unit' => $offer->basic_price_gross,
                'gross_selling_price_calculated_unit' => $offer->calculated_price_gross,
                'gross_selling_price_aggregate_unit' => $offer->aggregate_price_gross,
                'gross_selling_price_the_largest_unit' => $offer->aggregate_price_gross,
            ]);
        }

        return $order;
    }
}

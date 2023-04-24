<?php

namespace App\Services;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\ChatAuctionOffer;
use App\Entities\Firm;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Mail\NotifyFirmAboutAuction;
use App\Repositories\Firms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatAuctionsService
{
    public function __construct(
        protected ProductService $productService,
    )
    {
    }

    private function getFirms(array $variations): array
    {
        $firms = [];
        foreach ($variations as $variation) {
            foreach ($variation as $item) {
                $firms[] = Firms::getFirmBySymbol($item['product_name_supplier']);
            }
        }

        return $firms;
    }

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

}

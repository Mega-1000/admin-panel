<?php

namespace App\Services;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\Firm;
use App\Enums\ChatAuctionEnum;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Helpers\MessagesHelper;
use App\Mail\NotifyFirmAboutAuction;
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
                $firms[] = Firm::query()->where('symbol', $item['product_name_supplier'])->first();
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

    public function generateLinkForAuction(ChatAuction $auction, Firm $firm): string
    {
        $token = Str::random(255);

        ChatAuctionFirm::query()->findOrNew([
            'chat_auction_id' => $auction->id,
            'firm_id' => $firm->id,
            'token' => $token,
        ]);

        return $token;
    }

}

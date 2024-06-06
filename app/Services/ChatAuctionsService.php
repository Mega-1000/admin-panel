<?php

namespace App\Services;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Entities\Firm;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Mail\NotifyFirmAboutAuction;
use App\Repositories\ChatAuctionFirms;
use App\Repositories\ChatAuctionOffers;
use App\Repositories\Employees;
use App\Repositories\Firms;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

readonly class ChatAuctionsService
{
    public function __construct(
        protected ProductService              $productService,
        protected ChatAuctionOffers           $chatAuctionOffersRepository,
        protected Employees                   $employeesRepository,
        protected ChatAuctionOfferService     $chatAuctionOfferService,
        protected Firms                       $firmsRepository,
        protected AuctionOffersCreatorService $auctionOffersCreatorService,
        protected ChatAuctionFirms            $chatAuctionFirmsRepository,
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
                $firms[] = $this->firmsRepository->getFirmBySymbol($item['product_name_supplier']);
            }
        }

        return $firms;
    }

    /**
     * - zapisz wszystkie w aktualizacji cen
     * -
     *
     * Create auction
     *
     * @param CreateChatAuctionDTO $data
     * @return Model
     */
    public function createAuction(CreateChatAuctionDTO $data): Model
    {
        return $data->chat->auctions()->create([
            'end_of_auction' => $data->end_of_auction,
            'price' => $data->price,
            'quality' => $data->quality,
            'notes' => $data->notes
        ]);
    }

    /**
     * @throws DeliverAddressNotFoundException|Exception
     */
    public function confirmAuction($auction): void
    {
        $order = $auction->chat->order;
        $employees = Employees::getEmployeesForAuction($order);

        foreach ($employees as $employee) {
            Mailer::create()
                ->to($employee->email)
                ->send(new NotifyFirmAboutAuction($auction, $employee->firm, $this->generateLinkForAuction($auction, $employee->firm, $employee->email)));
        }

        $auction->update([
            'confirmed' => true,
        ]);

        $arr = [];
        RemoveLabelService::removeLabels($order, [266], $arr, [], null);

        AddLabelService::addLabels(
            $order,
            [224],
            $arr,
            [],
        );
    }

    /**
     * Generate link for auction
     *
     * @param ChatAuction $auction
     * @param Firm $firm
     * @param string $emailOfEmployee
     * @return string
     */
    public function generateLinkForAuction(ChatAuction $auction, Firm $firm, string $emailOfEmployee): string
    {
        return $this->chatAuctionFirmsRepository->createWithToken($auction, $firm, $emailOfEmployee);
    }

    /**
     * @throws Exception
     */
    public function endAuction(ChatAuction $auction, string $order, $user): void
    {
        $orders = json_decode($order, true);

        foreach ($orders as $order) {
            $this->auctionOffersCreatorService->createOrder($order, $user);
        }
    }

    /**
     * Get auctions
     *
     * @param Firm $firm
     * @return Collection
     */
    public function getAuctions(Firm $firm): Collection
    {
        return ChatAuction::whereHas('firms', function ($query) use ($firm) {
            $query->where('firm_id', $firm->id);
        })
        ->with(['offers', 'offers.firm', 'chat.order.customer.addresses', 'chat.order.items.product.packing'])
        ->orderBy('updated_at', 'desc')
        ->get()
        ->each(function (ChatAuction $auction) use ($firm) {
            $auction->editPricesLink = route('auctions.offer.create', ['token' => ChatAuctionFirm::where('chat_auction_id', $auction->id)
                ->where('firm_id', $firm->id)
                ->first()
                ->token
            ]);
        });
    }
}

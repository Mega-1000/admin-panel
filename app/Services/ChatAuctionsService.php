<?php

namespace App\Services;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\Entities\ChatAuction;
use App\Entities\Firm;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Facades\Mailer;
use App\Mail\NotifyFirmAboutAuction;
use App\Repositories\ChatAuctionFirms;
use App\Repositories\ChatAuctionOffers;
use App\Repositories\Employees;
use App\Repositories\Firms;
use App\Services\Label\AddLabelService;
use Exception;
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
    private function getFirms(array $variations): array
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

        $employees = [];
        foreach ($firms as $firm) {
            foreach ($this->employeesRepository->getEmployeesForAuctionOrderByFirm($firm) as $employee) {
                $employees[] = $employee;
            }
        }

        foreach ($employees as $employee) {
            Mailer::create()
                ->to($employee->email)
                ->send(new NotifyFirmAboutAuction($auction, $employee->firm, $this->generateLinkForAuction($auction, $employee->firm)));
        }

        $auction->update([
            'confirmed' => true,
        ]);

        $arr = [];

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
     * @return string
     */
    public function generateLinkForAuction(ChatAuction $auction, Firm $firm): string
    {
        return $this->chatAuctionFirmsRepository->createWithToken($auction, $firm);
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
}

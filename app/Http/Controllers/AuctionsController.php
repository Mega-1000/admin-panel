<?php

namespace App\Http\Controllers;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Helpers\Exceptions\ChatException;
use App\Http\Requests\CreateAuctionRequest;
use App\Http\Requests\CreateChatAuctionOfferRequest;
use App\Http\Requests\UpdateChatAuctionRequest;
use App\Repositories\ChatAuctionFirms;
use App\Services\ChatAuctionOfferService;
use App\Services\ChatAuctionsService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuctionsController extends Controller
{
    public function __construct(
        private readonly ChatAuctionsService     $chatAuctionsService,
        private readonly ChatAuctionOfferService $chatAuctionOfferService,
        private readonly ChatAuctionFirms        $chatAuctionFirmsRepository,
    ) {}

    /**
     * Show the form for creating a new resource.
     *
     * @param Chat $chat
     * @return View
     */
    public function create(Chat $chat): View
    {
        return view('auctions.create', [
            'chat' => $chat,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Chat $chat
     * @param CreateAuctionRequest $request
     * @return RedirectResponse
     */
    public function store(Chat $chat, CreateAuctionRequest $request): RedirectResponse
    {
        $this->chatAuctionsService->createAuction(CreateChatAuctionDTO::fromRequest($chat, $request->validated()));

        return redirect()->route('success');
    }

    /**
     * Display the specified resource.
     *
     * @param ChatAuction $auction
     * @return Application|Factory|View
     */
    public function show(ChatAuction $auction): View|Factory|Application
    {
        return view('auctions.show', [
            'auction' => $auction,
        ]);
    }

    /**
     * Show success page
     *
     * @return View
     */
    public function success(): View
    {
        return view('auctions.success');
    }

    /**
     * @throws DeliverAddressNotFoundException
     */
    public function confirm(ChatAuction $auction): RedirectResponse
    {
        $this->chatAuctionsService->confirmAuction($auction);

        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $token
     * @return View
     */
    public function createOffer(string $token): View
    {
        return view('auctions.create-offer', [
            'chat_auction_firm' => ChatAuctionFirms::getChatAuctionFirmByToken($token),
            'products' => ChatAuctionFirms::getItemsByToken($token),
            'current_firm_offers' => ChatAuctionFirms::getCurrentFirmOffersByToken($token),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $token
     * @param CreateChatAuctionOfferRequest $request
     * @return RedirectResponse
     */
    public function storeOffer(string $token, CreateChatAuctionOfferRequest $request): RedirectResponse
    {
        $firm = ChatAuctionFirm::query()->where('token', $token)->firstorfail();

        $this->chatAuctionOfferService->createOffer(CreateChatAuctionOfferDTO::fromRequest($request->validated() + [
            'firm_id' => $firm->id,
            'chat_auction_id' => $firm->chat_auction_id
        ]));

        return redirect()->back()->with('success', 'Pomyślnie dodano ofertę');
    }

    /**
     * @param ChatAuction $auction
     * @return View
     */
    public function end(ChatAuction $auction): View
    {
        $order = $auction->chat->order;
        $firms = $this->chatAuctionFirmsRepository->getFirmsByChatAuction($auction->id);

        return view('chat.auction-end', [
            'products' => $order->items,
            'offers' => $auction->offers,
        ], compact('order', 'firms', 'auction'));
    }

    /**
     * @param Request $request
     * @param ChatAuction $auction
     * @return RedirectResponse
     * @throws ChatException|Exception
     */
    public function endCreateOrders(Request $request, ChatAuction $auction): RedirectResponse
    {
        $customer = $auction->chat->customers()->first();

        $this->chatAuctionsService->endAuction($auction, $request->get('order'), $customer);

        return redirect()->route('success');
    }

    /**
     * @param UpdateChatAuctionRequest $request
     * @param ChatAuction $auction
     * @return RedirectResponse
     */
    public function update(UpdateChatAuctionRequest $request, ChatAuction $auction): RedirectResponse
    {
        $auction->update(
            $request->validated()
        );

        return redirect()->back();
    }
}

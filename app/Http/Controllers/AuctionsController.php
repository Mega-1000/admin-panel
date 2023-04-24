<?php

namespace App\Http\Controllers;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\DTO\ChatAuctions\CreateChatAuctionOfferDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Entities\ChatAuctionFirm;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Http\Requests\CreateAuctionRequest;
use App\Http\Requests\CreateChatAuctionOfferRequest;
use App\Services\ChatAuctionsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuctionsController extends Controller
{

    public function __construct(
        private readonly ChatAuctionsService $chatAuctionsService
    )
    {
    }


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
     * @param $token
     * @return View
     */
    public function createOffer($token): View
    {
        return view('auctions.create-offer', [
            'chat_auction_firm' => ChatAuctionFirm::query()->where('token', $token)->first(),
            'products' => ChatAuctionFirm::query()->where('token', $token)->first()->chatAuction->chat->order->items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $token
     * @param CreateChatAuctionOfferRequest $request
     * @return RedirectResponse
     */
    public function storeOffer($token, CreateChatAuctionOfferRequest $request): RedirectResponse
    {
        $firm = ChatAuctionFirm::query()->where('token', $token)->firstorfail();

        $this->chatAuctionsService->createOffer(CreateChatAuctionOfferDTO::fromRequest($request->validated() + [
            'firm_id' => $firm->id,
            'chat_auction_id' => $firm->chat_auction_id
        ]));

        return redirect()->back()->with('success', 'Pomyślnie dodano ofertę');
    }
}

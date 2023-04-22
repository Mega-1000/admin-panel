<?php

namespace App\Http\Controllers;

use App\DTO\ChatAuctions\CreateChatAuctionDTO;
use App\Entities\Chat;
use App\Entities\ChatAuction;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Http\Requests\CreateAuctionRequest;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Chat $chat)
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
        $auction = $this->chatAuctionsService->createAuction(CreateChatAuctionDTO::fromRequest($chat, $request->validated()));

        return redirect()->route('auctions.show', $auction->id);
    }

    /**
     * Display the specified resource.
     *
     * @param ChatAuction $auction
     * @return Application|Factory|View
     */
    public function show(ChatAuction $auction): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('auctions.show', [
            'auction' => $auction,
        ]);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

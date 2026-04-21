<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNewsletterPacketRequest;
use App\NewsletterPacket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NewsletterPacketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('newsletter-packet.index', [
            'newsletterPackets' => NewsletterPacket::paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('newsletter-packet.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateNewsletterPacketRequest $request
     * @return RedirectResponse
     */
    public function store(CreateNewsletterPacketRequest $request): RedirectResponse
    {
        NewsletterPacket::create($request->validated());

        return redirect()->route('newsletter-packets.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param NewsletterPacket $newsletterPacket
     * @return View
     */
    public function edit(NewsletterPacket $newsletterPacket): View
    {
        return view('newsletter-packet.show', [
            'newsletterPacket' => $newsletterPacket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateNewsletterPacketRequest $request
     * @param NewsletterPacket $newsletterPacket
     * @return RedirectResponse
     */
    public function update(CreateNewsletterPacketRequest $request, NewsletterPacket $newsletterPacket): RedirectResponse
    {
        $newsletterPacket->update($request->validated());

        return redirect()->route('newsletter-packets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param NewsletterPacket $newsletterPacket
     * @return RedirectResponse
     */
    public function destroy(NewsletterPacket $newsletterPacket): RedirectResponse
    {
        $newsletterPacket->delete();

        return redirect()->route('newsletter-packets.index');
    }
}

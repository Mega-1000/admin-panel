<?php

namespace App\Http\Controllers;

use App\Entities\ProductPacket;
use App\Http\Requests\CreateProductPacketRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProductPacketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('product-packets.index', [
            'packets' => ProductPacket::paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('product-packets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProductPacketRequest $request
     * @return RedirectResponse
     */
    public function store(CreateProductPacketRequest $request): RedirectResponse
    {
        ProductPacket::create($request->validated());

        return redirect()->route('product-packets.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProductPacket $productPacket
     * @return View
     */
    public function edit(ProductPacket $productPacket): View
    {
        return view('product-packets.edit', [
            'packet' => $productPacket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateProductPacketRequest $request
     * @param ProductPacket $productPacket
     * @return RedirectResponse
     */
    public function update(CreateProductPacketRequest $request, ProductPacket $productPacket): RedirectResponse
    {
        $productPacket->update($request->validated());

        return redirect()->route('product-packets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductPacket $productPacket
     * @return RedirectResponse
     */
    public function destroy(ProductPacket $productPacket): RedirectResponse
    {
        $productPacket->delete();

        return redirect()->back();
    }
}

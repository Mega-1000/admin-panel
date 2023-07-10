<?php

namespace App\Http\Controllers;

use App\DTO\Discounts\DiscountDTO;
use App\Entities\Discount;
use App\Entities\Product;
use App\Http\Requests\CreateDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Services\DiscountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService $discountService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('discounts.index', [
            'discounts' => Discount::paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('discounts.create', [
            'products' => Product::all(),
        ]);
    }

    /**
     * @param CreateDiscountRequest $request
     * @return RedirectResponse
     */
    public function store(CreateDiscountRequest $request): RedirectResponse
    {
        $this->discountService->create(
            DiscountDTO::fromRequest($request->validated()),
        );

        return redirect()->route('discounts.index')->with([
            'message' => 'Discount created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Discount  $discount
     * @return View
     */
    public function edit(Discount $discount): View
    {
        return view('discounts.edit', compact('discount'), [
            'products' => Product::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDiscountRequest $request
     * @param Discount $discount
     * @return RedirectResponse
     */
    public function update(UpdateDiscountRequest $request, Discount $discount): RedirectResponse
    {
        $this->discountService->update(
            $discount,
            DiscountDTO::fromRequest($request->validated()),
        );

        return redirect()->route('discounts.index')->with([
            'message' => 'Discount updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Discount  $discount
     * @return RedirectResponse
     */
    public function destroy(Discount $discount): RedirectResponse
    {
        $this->discountService->delete($discount);

        return redirect()->back()->with([
            'message' => 'Discount deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}

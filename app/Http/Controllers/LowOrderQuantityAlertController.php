<?php

namespace App\Http\Controllers;

use App\Entities\EntitiesLowOrderQuantityAlert;
use App\Entities\LowOrderQuantityAlert;
use App\Entities\Product;
use App\Http\Requests\CreateLowOrderQuantityAlertRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LowOrderQuantityAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('low-order-quantity-alert.index', [
            'messages' => LowOrderQuantityAlert::paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('low-order-quantity-alert.create', [
            'names' => Product::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateLowOrderQuantityAlertRequest $request
     * @return RedirectResponse
     */
    public function store(CreateLowOrderQuantityAlertRequest $request): RedirectResponse
    {
        LowOrderQuantityAlert::create(
            $request->validated()
        );

        return redirect()->route('low-quantity-alerts.index')
            ->with(['message' => __('voyager.generic.successfully_created'),
            'alert-type' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $lowOrderQuantityAlert
     * @return View
     */
    public function edit(int $lowOrderQuantityAlert): View
    {
        return view('low-order-quantity-alert.show', [
            'message' => LowOrderQuantityAlert::find($lowOrderQuantityAlert),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateLowOrderQuantityAlertRequest $request
     * @param int $lowOrderQuantityAlert
     * @return RedirectResponse
     */
    public function update(CreateLowOrderQuantityAlertRequest $request, int $lowOrderQuantityAlert): RedirectResponse
    {
        LowOrderQuantityAlert::find($lowOrderQuantityAlert)->update(
            $request->validated()
        );

        return redirect()->route('low-quantity-alerts.index')
            ->with(['message' => __('voyager.generic.successfully_created'),
                'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $lowOrderQuantityAlert
     * @return RedirectResponse
     */
    public function destroy(int $lowOrderQuantityAlert): RedirectResponse
    {
        LowOrderQuantityAlert::find($lowOrderQuantityAlert)->delete();

        return redirect()->back();
    }
}

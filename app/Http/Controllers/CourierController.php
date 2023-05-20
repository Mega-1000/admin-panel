<?php

namespace App\Http\Controllers;

use App\Entities\Courier;
use App\Http\Requests\CourierUpdateRequest;
use App\Repositories\Couriers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CourierController extends Controller
{

    protected Couriers $couriersRepository;

    public function __construct(Couriers $couriersRepository)
    {
        $this->couriersRepository = $couriersRepository;
    }

    /**
     * Show the return form of a specific resource.
     */
    public function index(): View
    {
        $couriers = $this->couriersRepository->getOrderByNumber();
        return view('courier.index', compact('couriers'));
    }

    /**
     * @param Courier $courier
     * @return View
     */
    public function edit(Courier $courier): View
    {
        return view('courier.edit', compact('courier'));
    }

    /**
     * @param CourierUpdateRequest $request
     * @param Courier $courier
     * @return RedirectResponse
     */
    public function update(CourierUpdateRequest $request, Courier $courier): RedirectResponse
    {
        $courier->fill($request->all());
        $courier->save();

        return redirect()->route('courier.index')->with([
            'message' => 'Ustawienia kuriera zapisane poprawnie!',
            'alert-type' => 'success'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Repositories\Couriers;
use Illuminate\Http\Request;
use App\Http\Requests\CourierUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Entities\Courier;

class CourierController extends Controller
{

    /**
     * @var CourierRepository
     */
    protected $repository;

    public function __construct(Couriers $repository) {
        $this->repository = $repository;
    }

    /**
     * Show the return form of a specific resource.
     */
    public function index(): View
    {
        $couriers = $this->repository->getOrderByNumber();
        return view('courier.index', compact('couriers'));
    }

    /**
     * @param Courier $courier
     */
    public function edit(Courier $courier): View {
        return view('courier.edit', compact('courier'));
    }

    /**
     * @param  CourierUpdateRequest $request
     * @param  Courier             $courier
     */
    public function update(CourierUpdateRequest $request, Courier $courier): RedirectResponse {

        $courier->fill($request->all());
        $courier->save();

        return redirect()->route('courier.index')->with([
            'message' => 'Ustawienia kuriera zapisane poprawnie!',
            'alert-type' => 'success'
        ]);
    }
}

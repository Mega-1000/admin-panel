<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CurierUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Entities\Courier;

class CourierController extends Controller
{

    /**
     * Show the return form of a specific resource.
     *
     * @return View
     */
    public function index(): View
    {
        $couriers = Courier::orderBy('item_number')->get();
        return view('courier.index', compact('couriers'));
    }

    /**
     * @param Courier $courier
     *
     * @return View
     */
    public function edit(Courier $courier): View {
        return view('courier.edit', compact('courier'));
    }

    /**
     * @param  CurierUpdateRequest $request
     * @param  Courier             $courier
     * @return RedirectResponse
     */
    public function update(CurierUpdateRequest $request, Courier $courier): RedirectResponse {

        $courier->fill($request->all());
        $courier->save();

        return redirect()->route('courier.index')->with([
            'message' => 'Ustawienia kuriera zapisane poprawnie!',
            'alert-type' => 'success'
        ]);
    }
}

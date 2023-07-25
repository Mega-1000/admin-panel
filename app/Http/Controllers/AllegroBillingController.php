<?php

namespace App\Http\Controllers;

use App\Entities\AllegroGeneralExpense;
use App\Http\Requests\IndexAllegroBillingRequest;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AllegroBillingController
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexAllegroBillingRequest $request
     * @return View
     */
    public function index(IndexAllegroBillingRequest $request): View
    {
        $query = AllegroGeneralExpense::query();

        if ($request->has('order-id')) {
            $query->where('order_id', $request->validated('order-id'));
        }

        return view('allegro-billing.index', [
            'expenses' => $query->paginate(30),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\AllegroGeneralExpense;
use Illuminate\View\View;

class AllegroBillingController
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('allegro-billing.index', [
            'expenses' => AllegroGeneralExpense::paginate(30),
        ]);
    }
}

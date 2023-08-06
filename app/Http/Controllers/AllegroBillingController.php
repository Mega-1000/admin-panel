<?php

namespace App\Http\Controllers;

use App\Entities\AllegroGeneralExpense;
use App\Http\Requests\IndexAllegroBillingRequest;
use App\Repositories\OrderPackageRealCostsForCompany;
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
        $orderId = $request->validated('order-id');

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $realCostsForCompany = OrderPackageRealCostsForCompany::getAllCostsByOrder($orderId);

        $expenses = !$orderId
            ? $query->paginate(30)
            :  $query->get()->toArray() + $realCostsForCompany->toArray();

        return view('allegro-billing.index', [
            'expenses' => $expenses,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use Illuminate\View\View;

class OrderWithDeclaredPaymentsListingController extends Controller
{
    public function __invoke(): View
    {
        return view('order-with-declared-payments-listing', [
            'orders' => Order::query()->whereHas('payments', function ($query) {
                $query->where('declared_sum', '>', '0');
            })->whereDoesntHave('payments', function ($query) {
                $query->where('status', '!=', 'Rozliczona deklarowana');
            })->paginate(30)
        ]);
    }
}

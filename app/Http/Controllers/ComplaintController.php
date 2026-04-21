<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(): View
    {
        return view('complaint.index', [
            'orders' => Order::whereHas('chat', function ($query) {
                $query->where('complaint_form', '!=', '');
            })->paginate(10),
        ]);
    }
}

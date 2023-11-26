<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OrderDatatableController extends Controller
{
    public function __invoke(): View
    {
        return view('order-datatable-index');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OrderDatatableColumnsManagementController extends Controller
{
    public function index(): View
    {
        return view('order-datatable-columns-management.index');
    }
}

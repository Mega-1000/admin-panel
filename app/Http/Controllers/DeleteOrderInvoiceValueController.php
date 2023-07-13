<?php

namespace App\Http\Controllers;

use App\Entities\OrderInvoiceValue;
use Illuminate\Http\RedirectResponse;

class DeleteOrderInvoiceValueController extends Controller
{
    public function __invoke(int $id): RedirectResponse
    {
        OrderInvoiceValue::findOrFail($id)->delete();

        return redirect()->back();
    }
}

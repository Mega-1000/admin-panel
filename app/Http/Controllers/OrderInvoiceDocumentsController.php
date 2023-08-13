<?php

namespace App\Http\Controllers;

use App\Entities\OrderInvoiceDocument;
use App\Http\Requests\CreateOrderInvoiceDocumentRequest;
use Illuminate\Http\RedirectResponse;

class OrderInvoiceDocumentsController extends Controller
{
    public function store(CreateOrderInvoiceDocumentRequest $request)
    {
        OrderInvoiceDocument::create($request->validated());
    }

    public function destroy(int $id): RedirectResponse
    {
        OrderInvoiceDocument::findOrFail($id)->delete();

        return redirect()->back();
    }
}

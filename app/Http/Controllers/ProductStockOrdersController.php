<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\ProductStock;
use App\Entities\Firm;
use App\Http\Requests\CreateProductStockOrdersRequest;
use App\Mail\ProductStockOrderMail;
use Illuminate\Support\Facades\Mail;

class ProductStockOrdersController extends Controller
{
    public function create(ProductStock $productStock): \Illuminate\View\View {
        return view('product_stocks.orders.create', [
            'productStock' => $productStock,
            'firms' => Firm::all(),
        ]);
    }

    public function store(CreateProductStockOrdersRequest $request, ProductStock $productStock): \Illuminate\Http\RedirectResponse {
        $firm = Firm::findorfail($request->firm_id);

        Mail::to($firm->email)->send(new ProductStockOrderMail($productStock, $request->validated()));

        return redirect()->route('product_stocks.show', $productStock->id);
    }

    public function calculateOrderQuantity(ProductStock $productStock, Request $request): \Illuminate\Http\JsonResponse {
        $orderQuantity = $productStock->calculateOrderQuantity($request->time);

        return response()->json([
            'orderQuantity' => $orderQuantity,
        ]);
    }

}

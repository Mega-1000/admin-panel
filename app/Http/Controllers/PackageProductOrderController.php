<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\Product;
use App\Factory\OrderBuilderFactory;
use App\Http\Requests\StorePackageProductOrderRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PackageProductOrderController extends Controller
{
    /**
     * @param Order $order
     * @return View
     */
    public function create(Order $order): View
    {
        return view('package-product-order.create', [
            'packageProducts' => Product::where('is_package', true)->get()
        ], compact('order'));
    }

    /**
     * @param Order $order
     * @param StorePackageProductOrderRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(Order $order, StorePackageProductOrderRequest $request): RedirectResponse
    {
        OrderBuilderFactory::create()
            ->assignItemsToOrder(
                $order,
                [
                    Product::find($request->validated('product_id'))->toArray() +
                    ['amount' => $request->validated('quantity')]
                ],
            );

        return redirect()->route('orders.edit', $order->id);
    }
}

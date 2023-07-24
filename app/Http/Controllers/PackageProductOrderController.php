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
        foreach ($request->validated('quantity') as $key => $quantity) {
            if ($quantity === 0) {
                continue;
            }

            OrderBuilderFactory::create()
                ->assignItemsToOrder(
                    $order,
                    [
                        Product::find($key)->toArray() +
                        ['amount' => $quantity]
                    ],
                    false,
                );
        }

        return redirect()->route('orders.edit', $order->id);
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\Product;
use App\Factory\OrderBuilderFactory;
use App\Http\Requests\StorePackageProductOrderRequest;
use App\Services\PackageProductOrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PackageProductOrderController extends Controller
{
    public function __construct(
        protected PackageProductOrderService $packageProductOrderService,
    ) {}

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
        $this->packageProductOrderService->store(
            $order,
            $request->validated()
        );

        return redirect()->route('orders.edit', $order->id);
    }
}

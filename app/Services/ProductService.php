<?php

namespace App\Services;

use App\Entities\Firm;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\Product;
use App\Entities\Warehouse;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Helpers\ProductSymbolCoreExtractor;
use App\Http\Controllers\OrdersController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

readonly class ProductService
{
    public function checkForSimilarProducts(int $productId): ?Collection
    {
        $product = Product::find($productId);
        $productSymbolCore = ProductSymbolCoreExtractor::getProductSymbolCore($product->symbol);

        return Product::where('symbol', 'LIKE', '%' . $productSymbolCore . '%')->get();
    }

    public function getStockProduct(int $productId): ?Product
    {
        $similarProducts = $this->checkForSimilarProducts($productId);

        return $similarProducts->first(function ($similarProduct) {
            return $similarProduct->stock_product === true;
        });
    }

    /**
     * Get users based of all order variations for auction
     *
     * @param Order $order
     * @return Collection|null
     * @throws DeliverAddressNotFoundException
     */
    public function getUsersFromVariations(Order $order): ?Collection
    {
        $orders = collect($this->getVariations($order))->toArray();
        $orders = array_merge(...$orders);
        $users = new Collection();

        foreach ($orders as $order) {
            if (is_array($order)) {
                $orderObj = Product::find($order['id']);
                $orderObj->firm->employees->each(function ($employee) use ($order, &$users) {
                    if ($employee->status !== 'PENDING') {
                        $users[] = $employee;

                        $user = $users->last();
                        $user->distance = $order['radius'];
                    }
                });
            }
        }

        return $users->unique('email');
    }


    /**
     * GetVariations
     *
     * @param Order $order
     *
     * @return array
     * @throws BindingResolutionException
     */
    public function getVariations(Order $order): array
    {
        $controller = app()->make(OrdersController::class);

        return $controller->getVariations($order);
    }
}

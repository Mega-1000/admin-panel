<?php

namespace App\Http\Controllers\Api;

use App\Entities\Product;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Entities\Set;
use App\Entities\SetItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetsController extends Controller
{
    public function index()
    {
        $sets = [];
        $getSets = Set::leftJoin('products', 'products.id', '=', 'sets.product_id')
            ->select(['sets.id as id', 'products.name as name', 'products.symbol as number', 'products.stock_product as stock', 'products.*', 'sets.*'])
            ->get();
        foreach ($getSets as $item) {
            $stock = ProductStock::where('id', $item->product_id)->get()->first();
            if ($stock) {
                $item->stock = $stock->quantity;
            }
            $sets[$item->id] = [
                'set' => $item,
                'products' => $item->products()
            ];
        }

        return response($sets);
    }

    public function products(Request $request)
    {
        if ($request->has('name') && $request->name != '') {
            return Product::where('name', 'LIKE', '%' . $request->name . '%')->get();
        }
        if ($request->has('symbol') && $request->symbol != '') {
            return Product::where('symbol', 'LIKE', '%' . $request->symbol . '%')->get();
        }
        if ($request->has('manufacturer') && $request->manufacturer != '') {
            return Product::where('manufacturer', 'LIKE', '%' . $request->manufacturer . '%')->get();
        }
        if ($request->has('word') && $request->word != '') {
            return Product::where('name', 'LIKE', '%' . $request->word . '%')
                ->orWhere('symbol', 'LIKE', '%' . $request->word . '%')
                ->orWhere('manufacturer', 'LIKE', '%' . $request->word . '%')->get();
        }

        return Product::get();
    }

    public function set(Set $set)
    {
        $set = Set::where('sets.id', $set->id)
            ->leftJoin('products', 'products.id', '=', 'sets.product_id')
            ->select(['sets.id as id', 'products.name as name', 'products.symbol as number', 'products.stock_product as stock', 'products.*', 'sets.*'])
            ->get()
            ->first();

        $stock = ProductStock::where('id', $set->product_id)->get()->first();
        if ($stock) {
            $set->stock = $stock->quantity;
        }

        return [
            'set' => $set,
            'products' => $set->products()
        ];
    }

    public function productsStocks(Product $product)
    {
        $stock = ProductStock::where('product_id', $product->id)->get();
        return [
            'stock' => $stock,
            'positions' => ProductStockPosition::where('product_stock_id', $stock->first()->id)->get()->all()
        ];
    }

    public function stocksAllSetsProducts(Set $set)
    {
        $products = collect($set->products())->pluck('product_id');
        $stocks = [];
        foreach ($products as $productId) {
            $stock = ProductStock::where('product_id', $productId)->get()->pluck('id');
            $stocks[] = [
                'id' => $productId,
                'stocks' => ProductStockPosition::whereIn('product_stock_id', $stock)->get()->all()
            ];
        }

        return $stocks;
    }

    public function store(Request $request)
    {
        if ($request->has('product_id')) {
            $product = Product::where('id', $request->product_id)->get()->first();
            $stock = ProductStock::where('product_id', $request->product_id)->get()->first();

            $set = new Set;
            $set->name = $product->name;
            $set->number = $product->symbol;
            $set->stock = $stock->quantity;
            $set->product_id = $product->id;
            if ($set->save()) {
                return response(json_encode([
                    'set' => $set,
                ]), 200);
            }
            return response(json_encode([
                'error_code' => 500,
                'error_message' => __('sets.messages.error')
            ]), 500);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }

    public function addProduct(Request $request, Set $set)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'stock' => 'required',
        ]);

        //The number of products must be greater than the number of sets multiplied by the number of products in one package.
        $setItem = new SetItem;
        $setItem->product_id = $request->product_id;
        $setItem->set_id = $set->id;
        $setItem->stock = $request->stock;


        if ($setItem->save()) {
            return response(json_encode($setItem), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);


    }

    public function editProduct(Set $set, SetItem $product, Request $request)
    {
        $this->validate($request, [
            'stock' => 'required'
        ]);

        $product->stock = $request->stock;

        if ($product->update()) {
            return response(json_encode($product), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }

    public function update(Set $set, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'number' => 'required',
        ]);

        $set->name = $request->name;
        $set->number = $request->number;
        $product = Product::where('id', $set->product_id)->get();
        $product->name = $request->name;
        $product->symbol = $request->number;

        if ($set->update() && $product->update()) {
            return response(json_encode([
                'set' => $set,
            ]), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }

    public function deleteProduct(Set $set, SetItem $product)
    {
        $productStock = $set->stock * $product->stock;
        $this->updateProductStock($product->product_id, -$productStock);

        if ($product->delete()) {
            return response(json_encode([]), 200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }

    private function updateProductStock(int $productId, int $numberProducts): void
    {
        $stock = ProductStock::where('product_id', $productId)->get()->first();
        $stock->quantity = $stock->quantity - $numberProducts;
        if ($stock->update()) {
            if ($numberProducts < 0) {
                $position = ProductStockPosition::where('product_stock_id', $stock->id)->get()->first();
                if ($position) {
                    $position->position_quantity += ($numberProducts * -1);
                    $position->update();
                } else {
                    redirect()->back()->with([
                        'message' => __('sets.messages.empty_id'),
                        'alert-type' => 'error'
                    ]);
                }

            } else {
                $positions = ProductStockPosition::where('product_stock_id', $stock->id)->get()->all();
                $countProducts = $numberProducts;
                foreach ($positions as $position) {
                    $quantity = $position->position_quantity;
                    if ($countProducts == 0) {
                        break;
                    } elseif ($quantity < $countProducts) {
                        $position->position_quantity = 0;
                        $countProducts -= $quantity;
                        $position->update();
                    } else {
                        $position->position_quantity -= $countProducts;
                        $countProducts = 0;
                        $position->update();
                    }
                }
            }
        }
    }

    public function delete(Set $set)
    {
        if (SetItem::where('set_id', $set->id)->delete()) {
            if ($set->delete()) {
                return response(json_encode([]), 200);
            }
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }

    public function completing(Set $set, Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
        ]);

        $setProductStock = ProductStock::where('product_id', $set->product_id)->get()->first();

        foreach ($set->products() as $product) {
            $stock = ProductStock::where('product_id', $product->product_id)->get()->first();
            $requiredStock = $product->stock * $request->number;
            if ($stock->quantity < $requiredStock) {
                return response(json_encode([
                    'error_code' => 500,
                    'error_message' => __('sets.messages.not_enough_product') . "'" . $product->name . "'" . $product->id . "   " . $stock->quantity . '     ' . $product->stock * $request->number
                ]), 500);
            }
        }

        $setProductStock->quantity = $setProductStock->quantity + $request->number;


        if ($setProductStock->update()) {
            foreach ($set->products() as $product) {
                $requiredStock = $product->stock * $request->number;
                $this->updateProductStock($product->id, $requiredStock);
            }
            return response(json_encode([
                'message' => __('sets.messages.completing_success') . ' ' . $request->number . ' ' . __('sets.sets')
            ]), 200);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }

    public function disassembly(Set $set, Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
        ]);

        $setProductStock = ProductStock::where('product_id', $set->product_id)->get()->first();
        $position = ProductStockPosition::where('product_stock_id', $set->product_id)->get()->first();
        $setsNumber = $setProductStock->quantity - $request->number;
        $oldStock = $setProductStock->quantity;
        $firstPositionNumber = $position->position_quantity - $request->number;
        if ($firstPositionNumber < 0) {
            return response(json_encode([
                'error_code' => 500,
                'error_message' => "Przenieś na pierwszą pozycje w magazynie komplety aby móc je zdekompletować"
            ]), 500);
        }

        $setProductStock->quantity = ($setsNumber < 0) ? 0 : $setsNumber;
        $position->position_quantity = $firstPositionNumber;
        if ($setProductStock->update() && $position->update()) {
            foreach ($set->products() as $product) {
                $requiredStock = $product->stock * (($setsNumber < 0) ? $oldStock : $setsNumber);
                $this->updateProductStock($product->id, -$requiredStock);
            }
            return response(json_encode([
                'message' => __('sets.messages.disassembly_success') . ' ' . $request->number . ' ' . __('sets.sets')
            ]), 200);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]), 500);
    }
}

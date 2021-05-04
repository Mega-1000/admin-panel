<?php
namespace App\Http\Controllers\Api;

use App\Entities\ProductPrice;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Entities\Set;
use App\Entities\SetItem;
use App\Entities\Product;

class SetsController extends Controller
{
    public function index()
    {
        $sets = [];
        foreach (Set::get() as $item) {
            $sets[$item->id] = [
                'set' => [$item],
                'products' => $item->products()
            ];
        }

        return  $sets;
    }

    public function set(Set $set)
    {
        $set = Set::where('id', $set->id)->get()->first();

        return  [
            'set' => $set,
            'products' => $set->products()
        ];
    }

    public function products(Request $request)
    {
        if($request->has('name') && $request->name!='') {
            return Product::where('name', 'LIKE', '%'.$request->name.'%')->get();
        }
        if($request->has('symbol') && $request->symbol != '') {
            return Product::where('symbol', 'LIKE', '%'.$request->symbol.'%')->get();
        }
        if($request->has('manufacturer') && $request->manufacturer != '') {
            return Product::where('manufacturer', 'LIKE', '%'.$request->manufacturer.'%')->get();
        }
        if($request->has('word') && $request->word != '') {
            return Product::where('name', 'LIKE', '%'.$request->word.'%')
                            ->orWhere('symbol', 'LIKE', '%'.$request->word.'%')
                            ->orWhere('manufacturer', 'LIKE', '%'.$request->word.'%')->get();
        }

        return  Product::get();
    }

    public function productsStocks(Product $product)
    {
        $stock = ProductStock::where('product_id', $product->id)->get();
        return [
            'stock' => $stock,
            'positions' => ProductStockPosition::where('product_stock_id', $stock->first()->id)->get()->all()
        ];
    }

    public function store(Request $request)
    {
        if($request->has('product_id')) {
            $product = Product::find('id', $request->product_id)->get()->first();
            $stock = ProductStock::where('product_id', $request->product_id)->get()->first();

            $set = new Set;
            $set->name = $product->name;
            $set->number = $product->symbol;
            $set->stock = $stock->quantity;
            $set->product_id = $product->id;
            if ($set->save()) {
                return response(json_encode([
                    'set' => $set,
                ]),200);
            }
            return response(json_encode([
                'error_code' => 500,
                'error_message' => __('sets.messages.error')
            ]),500);
        }
        if($request->has('name') && $request->has('symbol') && $request->has('price')) {
            $product = new Product;
            $product->name = $request->name;
            $product->symbol = $request->symbol;
            $product->trade_group_name = '';

            if ($product->save()) {
                $productPrice = new ProductPrice;
                $productPrice->product_id = $product->id;
                $productPrice->vat = 23;
                $productPrice->allegro_selling_gross_commercial_price = $request->price;

                if ($productPrice->save()) {
                    $set = new Set;
                    $set->name = $product->name;
                    $set->number = $product->symbol;
                    $set->stock = 0;
                    $set->product_id = $product->id;

                    if ($set->save()) {
                        return response(json_encode([
                            'set' => $set,
                        ]), 200);
                    }
                }
            }
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }

    public function update(Set $set, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'number' => 'required',
        ]);

        $set->name = $request->name;
        $set->number = $request->number;
        $set->stock = $request->stock;

        if ($set->update()) {
            return response(json_encode([
                'set' => $set,
            ]),200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }

    public function delete(Set $set)
    {
        if ($set->delete()) {
            return response(json_encode([]),200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }

    public function addProduct(Request $request, Set $set)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'stock' => 'required',
        ]);

        $stock = ProductStock::find($request->product_id)->get()->first();
        $requiredStock = $set->stock * $request->stock;

        //The number of products must be greater than the number of sets multiplied by the number of products in one package.
        if ($stock->quantity >= $requiredStock) {
            $setItem = new SetItem;
            $setItem->product_id = $request->product_id;
            $setItem->set_id = $set->id;
            $setItem->stock = $request->stock;

            $this->updateProductStock($request->product_id, $requiredStock);

            if ($setItem->save()) {
                return response(json_encode($setItem),200);
            }
            return response(json_encode([
                'error_code' => 500,
                'error_message' => __('sets.messages.error')
            ]),500);
        } else {
            return response(json_encode([
                'error_code' => 500,
                'error_message' => __('sets.messages.not_enough_product')
            ]),500);
        }
    }

    public function editProduct(Set $set, SetItem $product, Request $request)
    {
        $this->validate($request, [
            'stock' => 'required'
        ]);

        $stock = ProductStock::where('product_id', $product->product_id)->get()->first();
        $lastSetStock = $product->stock * $set->stock;
        $requiredStock = $set->stock * $request->stock;
        $updatedStock = $requiredStock - $lastSetStock;

        if ($stock->quantity >= $updatedStock) {

            $this->updateProductStock($product->product_id, $updatedStock);
            $product->stock = $request->stock;

            if ($product->update()) {
                return response(json_encode($product),200);
            }
            return response(json_encode([
                'error_code' => 500,
                'error_message' => __('sets.messages.error')
            ]),500);
        } else {
            return response(json_encode([
                'error_code' => 500,
                'error_message' => __('sets.messages.not_enough_product')
            ]),500);
        }
    }

    public function deleteProduct(Set $set, SetItem $product)
    {
        $productStock = $set->stock * $product->stock;
        $this->updateProductStock($product->product_id, -$productStock);

        if ($product->delete()) {
            return response(json_encode([]),200);
        }
        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }

    public function completing(Set $set, Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
        ]);

        foreach ($set->products() as $product) {
            $stock = ProductStock::where('product_id', $product->id)->get()->first();
            $requiredStock = $product->stock * $request->number;
            if($stock->quantity < $requiredStock) {
                return response(json_encode([
                    'error_code' => 500,
                    'error_message' => __('sets.messages.not_enough_product')."'".$product->name."'"
                ]),500);
            }
        }

        $set->stock = $set->stock + $request->number;

        if ($set->update()) {
            foreach ($set->products() as $product) {
                $requiredStock = $product->stock * $request->number;
                $this->updateProductStock($product->id, $requiredStock);
            }
            return response(json_encode([
                'message' => __('sets.messages.completing_success').' '.$request->number.' '.__('sets.sets')
            ]),200);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }

    public function disassembly(Set $set, Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
        ]);

        $setsNumber = $set->stock - $request->number;
        $oldStock = $set->stock;

        $set->stock = ($setsNumber < 0) ? 0 : $setsNumber;

        if ($set->update()) {
            foreach ($set->products() as $product) {
                $requiredStock = $product->stock * (($setsNumber < 0) ? $oldStock : $setsNumber);
                $this->updateProductStock($product->id, -$requiredStock);
            }
            return response(json_encode([
                'message' => __('sets.messages.disassembly_success').' '.$request->number.' '.__('sets.sets')
            ]),200);
        }

        return response(json_encode([
            'error_code' => 500,
            'error_message' => __('sets.messages.error')
        ]),500);
    }

    private function updateProductStock(int $productId, int $numberProducts) {
        $stock = ProductStock::where('product_id', $productId)->get()->first();
        $stock->quantity = $stock->quantity - $numberProducts;
        if($stock->update()) {
            if($numberProducts < 0) {
                $position = ProductStockPosition::where('product_stock_id', $stock->id)->get()->first();
                if($position) {
                    $position->position_quantity += ($numberProducts * -1);
                    $position->update();
                } else {
                    return redirect()->back()->with([
                        'message' => __('sets.messages.empty_id'),
                        'alert-type' => 'error'
                    ]);
                }

            } else {
                $positions = ProductStockPosition::where('product_stock_id', $stock->id)->get()->all();
                $countProducts = $numberProducts;
                foreach ($positions as $position) {
                    $quantity = $position->position_quantity;
                    if($countProducts == 0) {
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
}

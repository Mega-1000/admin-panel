<?php
namespace App\Http\Controllers\Api;

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
    public function __construct()
    {
    }

    public function index()
    {
        $sets = Set::get()->all();

        return  view('product_stocks.sets.index', compact('sets'));
    }

    public function create()
    {
        return view('product_stocks.sets.create');
    }

    public function edit(Set $set)
    {
        $set = Set::find($set->id)->get()->first();
        $products = Product::get()->all();
        $setItems = $set->products();

        return  view('product_stocks.sets.edit', compact(['set', 'products', 'setItems']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'number' => 'required',
        ]);

        $set = new Set;
        $set->name = $request->name;
        $set->number = $request->number;
        $set->stock = 0;

        if ($set->save()) {
            return redirect()->route('sets.index')->with([
                'message' => __('sets.message.store'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.messages.error'),
            'alert-type' => 'error'
        ]);
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
            return redirect()->route('sets.edit', ['set' => $set->id])->with([
                'message' => __('sets.message.store'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.messages.error'),
            'alert-type' => 'error'
        ]);
    }

    public function delete(Set $set)
    {
        if ($set->delete()) {
            return redirect()->route('sets.index')->with([
                'message' => __('sets.message.delete'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.messages.error'),
            'alert-type' => 'error'
        ]);
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
                return redirect()->route('sets.edit', ['set' => $set->id])->with([
                    'message' => __('sets.message.store'),
                    'alert-type' => 'success'
                ]);
            }
            return redirect()->back()->with([
                'message' => __('sets.messages.error'),
                'alert-type' => 'error'
            ]);
        } else {
            return redirect()->back()->with([
                'message' => __('sets.messages.not_enough_product'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function editProduct(Set $set, SetItem $productSet, Request $request)
    {
        $this->validate($request, [
            'stock' => 'required'
        ]);

        $stock = ProductStock::where('product_id', $productSet->product_id)->get()->first();
        $lastSetStock = $productSet->stock * $set->stock;
        $requiredStock = $set->stock * $request->stock;
        $updatedStock = $requiredStock - $lastSetStock;

        if ($stock->quantity >= $updatedStock) {

            $this->updateProductStock($productSet->product_id, $updatedStock);
            $productSet->stock = $request->stock;

            if ($productSet->update()) {
                return redirect()->route('sets.edit', ['set' => $set->id])->with([
                    'message' => __('sets.message.store'),
                    'alert-type' => 'success'
                ]);
            }
            return redirect()->back()->with([
                'message' => __('sets.messages.error'),
                'alert-type' => 'error'
            ]);
        } else {
            return redirect()->back()->with([
                'message' => __('sets.messages.not_enough_product'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function deleteProduct(Set $set, SetItem $productSet)
    {
        $productStock = $set->stock * $productSet->stock;
        $this->updateProductStock($productSet->product_id, -$productStock);

        if ($productSet->delete()) {
            return redirect()->route('sets.edit', ['set' => $set->id])->with([
                'message' => __('sets.message.store'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.messages.error'),
            'alert-type' => 'error'
        ]);
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
                return redirect()->back()->with([
                    'message' => __('sets.messages.not_enough_product')."'".$product->name."'",
                    'alert-type' => 'error'
                ]);
            }
        }

        $set->stock = $set->stock + $request->number;

        if ($set->update()) {
            foreach ($set->products() as $product) {
                $requiredStock = $product->stock * $request->number;
                $this->updateProductStock($product->id, $requiredStock);
            }
            return redirect()->route('sets.index')->with([
                'message' => __('sets.messages.disassembly_success').' '.$request->number.' '.__('sets.sets'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->route('sets.index')->with([
            'message' => __('sets.messages.error'),
            'alert-type' => 'error'
        ]);
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
            return redirect()->route('sets.index')->with([
                'message' => __('sets.messages.disassembly_success').' '.$request->number.' '.__('sets.sets'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->route('sets.index')->with([
            'message' => __('sets.messages.error'),
            'alert-type' => 'error'
        ]);
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

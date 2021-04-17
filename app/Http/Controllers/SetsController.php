<?php
namespace App\Http\Controllers;

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
        $set = Set::where('id', $set->id)->get()->first();
        $products = Product::get()->all();
        $setItems = $set->products();

        return  view('product_stocks.sets.edit', compact(['set', 'products', 'setItems']));
    }

    public function store(Request $request)
    {
        $this->validate(request(), [
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
            'message' => __('sets.message.error'),
            'alert-type' => 'error'
        ]);
    }

    public function update(Set $set, Request $request)
    {
        $this->validate(request(), [
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
            'message' => __('sets.message.error'),
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
            'message' => __('sets.message.error'),
            'alert-type' => 'error'
        ]);
    }

    public function addProduct(Request $request, Set $set)
    {
        $this->validate(request(), [
            'product_id' => 'required',
            'stock' => 'required',
        ]);

        $setItem = new SetItem;
        $setItem->product_id = $request->product_id;
        $setItem->set_id = $set->id;
        $setItem->stock = $request->stock;

        if ($setItem->save()) {
            return redirect()->route('sets.edit', ['set' => $set->id])->with([
                'message' => __('sets.message.store'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.message.error'),
            'alert-type' => 'error'
        ]);
    }

    public function editProduct(Set $set, SetItem $productSet, Request $request)
    {
        $this->validate(request(), [
            'stock' => 'required'
        ]);

        $productSet->stock = $request->stock;

        if ($productSet->update()) {
            return redirect()->route('sets.edit', ['set' => $set->id])->with([
                'message' => __('sets.message.store'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.message.error'),
            'alert-type' => 'error'
        ]);
    }

    public function deleteProduct(Set $set, SetItem $productSet)
    {
        if ($productSet->delete()) {
            return redirect()->route('sets.edit', ['set' => $set->id])->with([
                'message' => __('sets.message.store'),
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => __('sets.message.error'),
            'alert-type' => 'error'
        ]);
    }
}

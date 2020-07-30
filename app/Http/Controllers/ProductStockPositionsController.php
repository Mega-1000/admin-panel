<?php

namespace App\Http\Controllers;

use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Http\Requests\ProductStockPositionCreate;
use App\Http\Requests\ProductStockPositionUpdate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class ProductStockPositionsController
 * @package App\Http\Controllers
 */
class ProductStockPositionsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('product_stocks.positions.create', compact('id'));
    }

    /**
     * @param ProductStockPositionCreate $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductStockPositionCreate $request)
    {
        $productStockPosition = ProductStockPosition::create(
            array_merge(['product_stock_id' => $request->id], $request->all())
        );
        $positionQuantity = $request->position_quantity;
        $productStock = ProductStock::find($productStockPosition->product_stock_id);
        if (empty($productStock)) {
            abort(404);
        }
        $quantity = $productStock->quantity + $positionQuantity;
        $productStock->update(['quantity' => $quantity]);
        $this->createLog('+' . $request->position_quantity, $productStock->id, $productStockPosition->id);

        return redirect()->route('product_stocks.edit', ['id' => $request->id, 'tab' => 'positions'])->with([
            'message' => __('product_stock_positions.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $position_id)
    {
        $productStockPosition = ProductStockPosition::find($position_id);

        return view('product_stocks.positions.edit', compact('id', 'productStockPosition'));
    }

    /**
     * @param ProductStockPositionUpdate $request
     * @param $id
     * @param $position_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductStockPositionUpdate $request, $id, $position_id)
    {
        $productStockPosition = ProductStockPosition::find($position_id);

        if (empty($productStockPosition)) {
            abort(404);
        }
        $productStock = ProductStock::where(['product_id' => $id])->first();


        if ($request->different !== null) {
            if (strstr($request->different, '+') == true) {
                $val = explode('+', $request->different);
                $calc = $productStock->quantity + (int)$val[1];
            } else {
                if (strstr($request->different, '-') == true) {
                    $val = explode('-', $request->different);
                    if ($val[1] > $productStockPosition->position_quantity) {
                        return redirect()->back()->with([
                            'message' => __('product_stocks.message.error_quantity'),
                            'alert-type' => 'error'
                        ]);
                    } else {
                        $calc = $productStock->quantity - (int)$val[1];
                    }
                }
            }
            $productStock->update(['quantity' => $calc]);
            $this->createLog($request->different, $productStock->id, $productStockPosition->id);
        }
        $productStockPosition->update($request->all());


        return redirect()->route('product_stocks.edit', ['id' => $request->id, 'tab' => 'positions'])->with([
            'message' => __('product_stock_positions.message.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @param $position_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, $position_id)
    {
        $productStockPosition = ProductStockPosition::find($position_id);

        if (empty($productStockPosition)) {
            abort(404);
        }
        $positionQuantity = $productStockPosition->position_quantity;
        $productStock = ProductStock::find($productStockPosition->product_stock_id);
        if (empty($productStock)) {
            abort(404);
        }
        $quantity = $productStock->quantity - $positionQuantity;
        $productStock->update(['quantity' => $quantity]);
        $productStockPosition->delete($productStockPosition->id);
        $this->createLog('-' . $positionQuantity, $productStock->id, $productStockPosition->id);
        return redirect()->route('product_stocks.edit', ['id' => $id, 'tab' => 'positions'])->with([
            'message' => __('product_stock_positions.message.delete'),
            'alert-type' => 'info'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable($id)
    {
        $collection = $this->prepareCollection($id);

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection($id)
    {
        $collection = ProductStockPosition::where(['product_stock_id' => $id])->get();

        return $collection;
    }

    public function quantityMove(Request $request, $from, $to)
    {
        $fromPosition = ProductStockPosition::find($from);
        $toPosition = ProductStockPosition::find($to);

        $fromPosition->update([
            'position_quantity' => $fromPosition->position_quantity - $request->input('quantity__move')
        ]);

        $toPosition->update([
            'position_quantity' => $toPosition->position_quantity + $request->input('quantity__move')
        ]);
    }

}

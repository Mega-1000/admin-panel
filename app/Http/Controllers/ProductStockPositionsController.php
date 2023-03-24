<?php

namespace App\Http\Controllers;

use App\DTO\Label\LabelSessionRemoveLabelDTO;
use App\Entities\OrderReturn;
use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Http\Requests\ProductStockPositionCreate;
use App\Http\Requests\ProductStockPositionUpdate;
use App\Services\Label\RemoveLabelService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class ProductStockPositionsController
 * @package App\Http\Controllers
 */
class ProductStockPositionsController extends Controller
{

    /**
     * @param ProductStockPositionCreate $request
     * @return RedirectResponse
     */
    public function store(ProductStockPositionCreate $request)
    {
        $existingRecord = ProductStockPosition::where('lane', '=', $request->lane)
            ->where('bookstand', '=', $request->bookstand)
            ->where('shelf', '=', $request->shelf)
            ->where('position', '=', $request->position)
            ->with(['stock' => function ($q) {
                $q->with('product');
            }])
            ->first();

        if (!empty($existingRecord)) {
            return redirect()->back()->with([
                'message' => __('product_stock_positions.message.position_exist') . ' Symbol:' . $existingRecord->stock->product->symbol,
                'alert-type' => 'error'
            ]);
        }

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
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create($id)
    {
        return view('product_stocks.positions.create', compact('id'));
    }

    /**
     * @param ProductStockPositionUpdate $request
     * @param $id
     * @param $position_id
     * @return RedirectResponse
     */
    public function update(ProductStockPositionUpdate $request, $id, $position_id)
    {
        $productStockPosition = ProductStockPosition::find($position_id);

        if (empty($productStockPosition)) {
            abort(404);
        }

        $productStockPosition->fill([
            'lane' => $request->lane,
            'bookstand' => $request->bookstand,
            'shelf' => $request->shelf,
            'position' => $request->position
        ]);
        if (array_intersect(['lane', 'bookstand', 'shelf', 'position'], array_keys($productStockPosition->getDirty()))) {
            $existingRecord = ProductStockPosition::query()
                ->where('lane', '=', $request->lane)
                ->where('bookstand', '=', $request->bookstand)
                ->where('shelf', '=', $request->shelf)
                ->where('position', '=', $request->position)
                ->with(['stock' => function ($q) {
                    $q->with('product');
                }])
                ->first();

            if ($existingRecord) {
                return redirect()->back()->with([
                    'message' => __('product_stock_positions.message.position_exist') . ' Symbol:' . $existingRecord->stock->product->symbol,
                    'alert-type' => 'error'
                ]);
            }
        }

        $productStock = ProductStock::where(['product_id' => $id])->first();


        if ($request->different !== null) {
            if (str_contains($request->different, '+') === true) {
                $val = explode('+', $request->different);
                $calc = $productStock->quantity + (int)$val[1];
            } else if (str_contains($request->different, '-') === true) {
                $val = explode('-', $request->different);
                if ($val[1] > $productStockPosition->position_quantity) {
                    return redirect()->back()->with([
                        'message' => __('product_stocks.message.error_quantity'),
                        'alert-type' => 'error'
                    ]);
                }
                $calc = $productStock->quantity - (int)$val[1];
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
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param $position_id
     * @return Application|Factory|View
     */
    public function edit(int $id, $position_id)
    {
        $productStockPosition = ProductStockPosition::find($position_id);

        return view('product_stocks.positions.edit', compact('id', 'productStockPosition'));
    }

    /**
     * @param $id
     * @param $position_id
     * @return RedirectResponse
     */
    public function destroy($id, $position_id)
    {
        $productStockPosition = ProductStockPosition::find($position_id);

        if ($productStockPosition->position_quantity != 0) {
            return redirect()->route('product_stocks.edit', ['id' => $id, 'tab' => 'positions'])->with([
                'message' => __('product_stock_positions.message.quantity_set'),
                'alert-type' => 'error'
            ]);
        }

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
        $productStockPosition->delete();
        $this->createLog('-' . $positionQuantity, $productStock->id, $productStockPosition->id);
        return redirect()->route('product_stocks.edit', ['id' => $id, 'tab' => 'positions'])->with([
            'message' => __('product_stock_positions.message.delete'),
            'alert-type' => 'info'
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function datatable($id)
    {
        $collection = $this->prepareCollection($id);

        return DataTables::collection($collection)->skipPaging()->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection($id)
    {
        $collection = ProductStockPosition::where(['product_stock_id' => $id])->get();

        foreach ($collection as $row) {
            $row->damaged = OrderReturn::where('product_stock_position_id', $row['id'])->sum('quantity_damaged');
        }

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

        if (Session::exists('removeLabelJobAfterProductStockMove')) {
            $labelSessionRemoveLabelDTOs = Session::get('removeLabelJobAfterProductStockMove');
            /** @var LabelSessionRemoveLabelDTO $labelSessionRemoveLabelDTO */
            foreach ($labelSessionRemoveLabelDTOs as $labelSessionRemoveLabelDTO) {
                $loopPreventionArray = $labelSessionRemoveLabelDTO->getLoopPreventionArray();
                $response = RemoveLabelService::removeLabels(
                    $labelSessionRemoveLabelDTO->getOrder(),
                    $labelSessionRemoveLabelDTO->getLabelIdsToRemove(),
                    $loopPreventionArray,
                    $labelSessionRemoveLabelDTO->getCustomLabelIdsToAddAfterRemoval(),
                    null
                );
                if (!array_key_exists('success', $response)) {
                    Session::forget('removeLabelJobAfterProductStockMove');
                }
            }
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockPositionCreate;
use App\Http\Requests\ProductStockPositionUpdate;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class ProductStockPositionsController
 * @package App\Http\Controllers
 */
class ProductStockPositionsController extends Controller
{
    /**
     * @var ProductStockPositionRepository
     */
    protected $repository;

    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * ProductStockPositionsController constructor.
     * @param ProductStockPositionRepository $repository
     * @param ProductStockRepository $productStockRepository
     */
    public function __construct(
        ProductStockPositionRepository $repository,
        ProductStockRepository $productStockRepository
    ) {
        $this->repository = $repository;
        $this->productStockRepository = $productStockRepository;
    }

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
        $productStockPosition = $this->repository->create(
            array_merge(['product_stock_id' => $request->id], $request->all())
        );
        $positionQuantity = $request->position_quantity;
        $productStock = $this->productStockRepository->find($productStockPosition->product_stock_id);
        if (empty($productStock)) {
            abort(404);
        }
        $quantity = $productStock->quantity + $positionQuantity;
        $this->productStockRepository->update(['quantity' => $quantity], $productStock->id);
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
        $productStockPosition = $this->repository->find($position_id);

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
        $productStockPosition = $this->repository->find($position_id);

        if (empty($productStockPosition)) {
            abort(404);
        }
        $productStock = $this->productStockRepository->findByField('product_id', $id);

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
            $this->productStockRepository->update(['quantity' => $calc], $productStock->id);
            $this->createLog($request->different, $productStock->id, $productStockPosition->id);
        }
        $this->repository->update($request->all(), $productStockPosition->id);


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
        $productStockPosition = $this->repository->find($position_id);

        if (empty($productStockPosition)) {
            abort(404);
        }
        $positionQuantity = $productStockPosition->position_quantity;
        $productStock = $this->productStockRepository->find($productStockPosition->product_stock_id);
        if (empty($productStock)) {
            abort(404);
        }
        $quantity = $productStock->quantity - $positionQuantity;
        $this->productStockRepository->update(['quantity' => $quantity], $productStock->id);
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
        $collection = $this->repository->findByField('product_stock_id', $id)->all();

        return $collection;
    }

}

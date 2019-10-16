<?php

namespace App\Http\Controllers;

use App\Repositories\ProductStockLogRepository;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class ProductStockLogsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductStockLogsController extends Controller
{
    /**
     * @var ProductStockLogRepository
     */
    protected $repository;

    public function __construct(ProductStockLogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id, $logId)
    {
        $productStockLog = $this->repository->find($logId);

        return view('product_stocks.logs.show', compact('productStockLog', 'id'));
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
        $collection = $this->repository->with('user')->findByField('product_stock_id', $id)->all();

        return $collection;
    }
}

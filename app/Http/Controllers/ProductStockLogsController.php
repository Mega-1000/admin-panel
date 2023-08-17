<?php

namespace App\Http\Controllers;

use App\Entities\ProductStockLog;
use App\Http\Requests\UpdateProductStockLogsRequest;
use App\Repositories\ProductStockLogRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
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
     * @param int $id
     * @param $logId
     * @return Application|Factory|View
     */
    public function show(int $id, $logId): Application|Factory|View
    {
        $productStockLog = $this->repository->find($logId);

        return view('product_stocks.logs.show', compact('productStockLog', 'id'));
    }


    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function datatable($id): JsonResponse
    {
        $collection = $this->prepareCollection($id);

        return DataTables::collection($collection)->make();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function prepareCollection($id): mixed
    {
        return $this->repository->with('user')->findByField('product_stock_id', $id)->all();
    }

    public function update(UpdateProductStockLogsRequest $request, ProductStockLog $productStockLog): RedirectResponse
    {
        $productStockLog->update($request->validated());

        return redirect()->route('productStockLogs.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\ProductStock;
use App\Entities\ProductStockLog;
use App\Http\Requests\ProductStockUpdateRequest;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class ProductStocksController extends Controller
{
    /**
     * @var ProductStockRepository
     */
    protected $repository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductStockPositionRepository
     */
    protected $productStockPositionRepository;

    /**
     * @var ProductStockLogRepository
     */
    protected $productStockLogRepository;

    protected $productService;

    public function __construct(
        ProductStockRepository $repository,
        ProductRepository $productRepository,
        ProductStockPositionRepository $productStockPositionRepository,
        ProductStockLogRepository $productStockLogRepository,
        ProductService $productService
    )
    {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->productStockPositionRepository = $productStockPositionRepository;
        $this->productStockLogRepository = $productStockLogRepository;
        $this->productService = $productService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('product_stocks'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }
        return view('product_stocks.index', compact('visibilities'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $productStocks = ProductStock::where('product_id', $id)->first();
        $similarProducts = $this->productService->checkForSimilarProducts($id);
        $visibilitiesLogs = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('product_stock_logs'));

        foreach ($visibilitiesLogs as $key => $row) {
            $visibilitiesLogs[$key]->show = json_decode($row->show, true);
            $visibilitiesLogs[$key]->hidden = json_decode($row->hidden, true);
        }
        $visibilitiesPosition = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('product_stock_positions'));
        foreach ($visibilitiesPosition as $key => $row) {
            $visibilitiesPosition[$key]->show = json_decode($row->show, true);
            $visibilitiesPosition[$key]->hidden = json_decode($row->hidden, true);
        }
        return view('product_stocks.edit', compact('visibilitiesLogs', 'visibilitiesPosition', 'productStocks', 'id', 'similarProducts'));
    }

    /**
     * @param ProductStockUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductStockUpdateRequest $request)
    {
        if ($request->select_position !== null) {

            $itemPosition = $this->productStockPositionRepository->find($request->select_position);
            if (empty($itemPosition)) {
                abort(404);
            }
            if (strstr($request->different, '+') == true) {
                $val = explode('+', $request->different);
                $calc = $itemPosition->position_quantity + (int)$val[1];
            } else {
                if (strstr($request->different, '-') == true) {
                    $val = explode('-', $request->different);
                    if ($val[1] > $itemPosition->position_quantity) {
                        return redirect()->back()->with([
                            'message' => __('product_stocks.message.error_quantity'),
                            'alert-type' => 'error'
                        ]);
                    } else {
                        $calc = $itemPosition->position_quantity - (int)$val[1];
                    }
                }
            }
            $this->productStockPositionRepository->update(['position_quantity' => $calc], $request->select_position);
            $this->createLog($request->different, $request->id, $itemPosition->id, $calc);
        }
        $productStock = $this->repository->find($request->id);

        if (empty($productStock)) {
            abort(404);
        }
        $request->merge([
            'stock_product' => $request->get('stock_product') ? true : false,
        ]);

        $this->repository->update($request->all(), $productStock->id);
        $this->productRepository->update($request->all(), $productStock->product_id);

        return redirect()->back()->with([
            'message' => __('product_stocks.message.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable(Request $request)
    {
        $data = $request->all();
        $collection = $this->prepareCollection($data);

        return DataTables::of($collection[0])->with(['recordsFiltered' => $collection[1]])->skipPaging()->setTotalRecords($collection[1])->make(true);

    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function print()
    {
        $query = \DB::table('product_stocks')
            ->distinct()
            ->select('*')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->whereNull('deleted_at');


        $query->whereRaw('product_stocks.quantity <> ?', [0]);


        $collection = $query->get();


        foreach ($collection as $row) {
            $row->positions = \DB::table('product_stock_positions')->where('product_stock_id', $row->id)->get();
        }

        return View::make('product_stocks.print', [
            'products' => $collection,
        ]);
    }

    /**
     * @return mixed
     */
    public function prepareCollection($data)
    {
        $query = \DB::table('product_stocks')
            ->distinct()
            ->select('*', 'product_stocks.id as stock_id')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->leftJoin('product_prices', 'product_stocks.product_id', '=', 'product_prices.product_id')
            ->whereNull('deleted_at');


        $notSearchable = [17, 19];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if (array_key_exists($column['name'], $notSearchable)) {

                } else {
                    $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
                }
            } else if ($column['name'] == 'quantity' && !empty($column['search']['value'])) {
                switch ($column['search']['value']) {
                    case "all":
                        break;
                    case "on_stock":
                        $query->whereRaw('product_stocks.quantity <> ?', [0]);
                        break;
                    case "without-dash":
                        $query->whereRaw('INSTR(`symbol`, "-") = 0');
                        break;
                    default:
                        break;
                }
            }
        }

        $collectionCount = $query
            ->count();

        $collection = $query
            ->limit($data['length'])->offset($data['start'])
            ->get();

        foreach ($collection as $row) {
            $row->positions = \DB::table('product_stock_positions')->where('product_stock_id', $row->stock_id)->get();
        }


        return [$collection, $collectionCount];
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $productStocks = $this->repository->find($id);

        if (empty($productStocks)) {
            abort(404);
        }
        $product = $this->productRepository->find($productStocks->product_id);
        $dataToStore = [];
        $dataToStore['status'] = $product['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->productRepository->update($dataToStore, $product->id);

        return redirect()->back()->with([
            'message' => __('product_stocks.message.change_status'),
            'alert-type' => 'success'
        ]);
    }

    public function productsStocksChanges(Request $request)
    {
        $startDate = $request->input('products-stocks-changes-start-date');
        $endDate = $request->input('products-stocks-changes-end-date');

        $productsStocksChanges = ProductStockLog::where([['created_at', '>=', $startDate], ['created_at', '<=', $endDate]])->get();

        $groupedProductsStocksChanges = [];

        foreach ($productsStocksChanges as $productsStocksChange) {
            $groupedProductsStocksChanges[$productsStocksChange->product_stock_id][] = $productsStocksChange;
        }

        return view('product_stocks.changes', compact('groupedProductsStocksChanges', 'startDate', 'endDate'));
    }
}

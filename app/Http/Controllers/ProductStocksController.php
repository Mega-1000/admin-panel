<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Customer;
use App\Entities\FirmSource;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\OrderReturn;
use App\Entities\Product;
use App\Entities\ProductStock;
use App\Entities\ProductStockLog;
use App\Entities\ProductStockPosition;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\GetCustomerForAdminEdit;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\TransportSumCalculator;
use App\Http\Requests\CalculateAdminOrderRequest;
use App\Http\Requests\CreateAdminOrderRequest;
use App\Http\Requests\ProductStockUpdateRequest;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockLogs;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use App\Services\OrderService;
use App\Services\ProductService;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class ProductStocksController extends Controller
{
    public function __construct(
        protected readonly ProductStockRepository         $repository,
        protected readonly ProductRepository              $productRepository,
        protected readonly ProductStockPositionRepository $productStockPositionRepository,
        protected readonly ProductStockLogRepository      $productStockLogRepository,
        protected readonly ProductService                 $productService,
        protected readonly OrderService                   $orderService
    )
    {
    }

    /**
     * @return Factory|\Illuminate\View\View
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('product_stocks'));
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        return view('product_stocks.index', compact('visibilities'));
    }

    /**
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $productStocks = ProductStock::where('product_id', $id)->first();
        $similarProducts = $this->productService->checkForSimilarProducts($id);
        $visibilitiesLogs = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('product_stock_logs'));

        foreach ($visibilitiesLogs as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        $visibilitiesPosition = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('product_stock_positions'));
        foreach ($visibilitiesPosition as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        return view('product_stocks.edit', compact('visibilitiesLogs', 'visibilitiesPosition', 'productStocks', 'id', 'similarProducts'));
    }

    /**
     * @return JsonResponse
     */
    public function datatable(Request $request)
    {
        $data = $request->all();
        $collection = $this->prepareCollection($data);

        return DataTables::of($collection[0])->with(['recordsFiltered' => $collection[1]])->skipPaging()->setTotalRecords($collection[1])->make(true);

    }

    /**
     * @return mixed
     */
    public function prepareCollection($data)
    {
        $query = DB::table('product_stocks')
            ->distinct()
            ->select('*', 'product_stocks.id as stock_id')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->leftJoin('product_prices', 'product_stocks.product_id', '=', 'product_prices.product_id')
            ->whereNull('products.deleted_at');

        $notSearchable = [17, 19];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if (array_key_exists($column['name'], $notSearchable) === false) {
                    $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
                }
            } else if ($column['name'] == 'quantity' && !empty($column['search']['value'])) {
                switch ($column['search']['value']) {
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
            $positions = ProductStockPosition::where('product_stock_id', $row->stock_id)->get();
            $row->positions = $positions;
            $damaged = 0;
            foreach ($positions as $p) {
                $damaged = $damaged + OrderReturn::where('product_stock_position_id', $p->id)->sum('quantity_damaged');
            }

            $row->damaged = $damaged;
        }


        return [$collection, $collectionCount];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function print()
    {
        $query = DB::table('product_stocks')
            ->distinct()
            ->select('*', 'product_stocks.id as stockId')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('product_packings', 'products.id', '=', 'product_packings.id')
            ->whereNull('deleted_at');

        $query->whereRaw('product_stocks.quantity <> ?', [0]);

        $collection = $query->get();

        foreach ($collection as $row) {
            $row->positions = DB::table('product_stock_positions')->where('product_stock_id', $row->stockId)->get();
        }

        return View::make('product_stocks.print', [
            'products' => $collection,
        ]);
    }

    /**
     * Raport pozycji stanów magazynowych
     */
    public function printReport()
    {
        $result = ProductStockPosition::whereHas('stock', function ($stockQuery) {
            $stockQuery->where('quantity', '<>', '0');
            $stockQuery->whereHas('product', function ($productQuery) {
                $productQuery->whereNull('deleted_at');
            });
        })
            ->orderBy('lane', 'asc')
            ->orderBy('bookstand', 'asc')
            ->orderBy('shelf', 'asc')
            ->orderBy('position', 'asc')
            ->get();

        return View::make('product_stocks.printReport', [
            'productsStockPositions' => $result,
        ]);
    }

    /**
     * @param $id
     * @return RedirectResponse
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

    /**
     * @param ProductStockUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProductStockUpdateRequest $request)
    {
        if ($request->select_position !== null) {

            $itemPosition = $this->productStockPositionRepository->find($request->select_position);
            if (empty($itemPosition)) {
                abort(404);
            }
            if (str_contains($request->different, '+') === true) {
                $val = explode('+', $request->different);
                $calc = $itemPosition->position_quantity + (int)$val[1];
            } else if (str_contains($request->different, '-') === true) {
                $val = explode('-', $request->different);
                if ($val[1] > $itemPosition->position_quantity) {
                    return redirect()->back()->with([
                        'message' => __('product_stocks.message.error_quantity'),
                        'alert-type' => 'error'
                    ]);
                }
                $calc = $itemPosition->position_quantity - (int)$val[1];

            }

            $this->productStockPositionRepository->update(['position_quantity' => $calc], $request->select_position);
            $this->createLog($request->different, $request->id, $itemPosition->id);
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

    /**
     * @param ProductStock $productStock
     * @return \Illuminate\Contracts\View\View
     */
    public function placeAdminSideOrder(ProductStock $productStock): \Illuminate\Contracts\View\View
    {
        return view('product_stocks.place_admin_side_order', compact('productStock'));
    }

    /**
     * Calculate order quantity for admin side order
     *
     * @param CalculateAdminOrderRequest $request
     * @param ProductStock $productStock
     * @return JsonResponse
     */
    public function calculateAdminOrder(CalculateAdminOrderRequest $request, ProductStock $productStock): JsonResponse
    {
        return response()->json([
            'orderQuantity' =>  $this->orderService->calculateOrderData($productStock, $request->validated('daysBack'), $request->validated('daysToFuture')),
        ]);
    }

    /**
     * create order
     *
     * @param CreateAdminOrderRequest $request
     * @param ProductStock $productStock
     * @param ProductService $productService
     * @return JsonResponse
     */
    public function createAdminOrder(CreateAdminOrderRequest $request, ProductStock $productStock, ProductService $productService): JsonResponse
    {
        $data = $request->validated();

        $order = $this->orderService->createOrder($productStock, $data, $productService);

        return response()->json([
            'order' => $order,
        ]);
    }
}

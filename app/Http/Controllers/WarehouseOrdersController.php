<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Jobs\SendWarehouseOrderEmailJob;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WarehouseOrdersItemsRepository;
use App\Repositories\WarehouseOrdersRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use Yajra\DataTables\Facades\DataTables;

class WarehouseOrdersController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $repository;

    /**
     * @var WarehouseOrdersRepository
     */
    protected $warehouseOrdersRepository;

    /**
     * @var WarehouseOrdersItemsRepository
     */
    protected $warehouseOrdersItemsRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var WarehouseRepository
     */
    protected $warehouseRepository;

    /**
     * @var OrderAddressRepository
     */
    protected $orderAddressRepository;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * WarehouseOrderController constructor.
     * @param ProductRepository $repository
     * @param WarehouseOrdersRepository $warehouseOrdersRepository
     * @param WarehouseOrdersItemsRepository $warehouseOrdersItemsRepository
     * @param OrderRepository $orderRepository
     * @param OrderItemRepository $orderItemRepository
     * @param WarehouseRepository $warehouseRepository
     * @param OrderAddressRepository $orderAddressRepository
     * @param CustomerAddressRepository $customerAddressRepository
     */
    public function __construct(
        ProductRepository $repository,
        WarehouseOrdersRepository $warehouseOrdersRepository,
        WarehouseOrdersItemsRepository $warehouseOrdersItemsRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        WarehouseRepository $warehouseRepository,
        OrderAddressRepository $orderAddressRepository,
        CustomerAddressRepository $customerAddressRepository
    ) {
        $this->repository = $repository;
        $this->warehouseOrdersRepository = $warehouseOrdersRepository;
        $this->warehouseOrdersItemsRepository = $warehouseOrdersItemsRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->customerAddressRepository = $customerAddressRepository;
    }

    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('products'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }

        return view('warehouse_orders.index', compact('visibilities'));
    }

    public function edit($id)
    {
        $warehouseOrder = $this->warehouseOrdersRepository->find($id);

        return view('warehouse_orders.edit', compact('warehouseOrder'));
    }

    public function update($id, Request $request)
    {
        $this->warehouseOrdersRepository->update([
            'status' => $request->input('status'),
            'company' => $request->input('company'),
            'email' => $request->input('warehouse_mail'),
            'shipment_date' => $request->input('shipment_date'),
            'comments_for_warehouse' => $request->input('comments_for_warehouse'),
            'warehouse_comments' => $request->input('warehouse_comment'),
        ], $id);
        foreach ($request->input('itemPrice') as $key => $value) {
            $this->warehouseOrdersItemsRepository->update([
                'price' => $value,
            ], $key);
        }

        foreach ($request->input('itemQuantity') as $key => $value) {
            $this->warehouseOrdersItemsRepository->update([
                'quantity' => $value,
            ], $key);
        }

        $warehouseOrder = $this->warehouseOrdersRepository->find($id);
        return view('warehouse_orders.edit', compact('warehouseOrder'));
    }

    public function makeOrder(Request $request)
    {
        $products = json_decode($request->input('products'));
        foreach($products as $product) {
            foreach($product as $key => $value) {
                if($value->warehouse != null) {
                    $warehouseName = $value->warehouse;
                }
            }
        }

        $warehouse = $this->warehouseRepository->findByField('symbol', $warehouseName)->first();

        $warehouseOrder = $this->orderRepository->create([
            'customer_id' => '4128',
            'warehouse_id' => $warehouse->id,
            'status_id' => 1,
        ]);


        $orderSum = 0;
        $orderWeight = 0;

        foreach($products as $product) {
            foreach($product as $key => $value) {
                $this->orderItemRepository->create([
                    'order_id' => $warehouseOrder->id,
                    'product_id' => $key,
                    'quantity' => $value->quantity,
                    'price' => $this->repository->find((int)$key)->price->net_purchase_price_commercial_unit,
                    'net_selling_price_commercial_unit' => $this->repository->find((int)$key)->price->net_purchase_price_commercial_unit,
                    'net_selling_price_basic_unit' => $this->repository->find((int)$key)->price->net_selling_price_basic_unit,
                    'net_selling_price_calculated_unit' => $this->repository->find((int)$key)->price->net_selling_price_calculated_unit,
                    'net_selling_price_aggregate_unit' => $this->repository->find((int)$key)->price->net_selling_price_aggregate_unit,
                    'net_purchase_price_commercial_unit' => $value->commercialAfter,
                    'net_purchase_price_basic_unit' => $value->basicAfter,
                    'net_purchase_price_calculated_unit' => $value->calculationAfter,
                    'net_purchase_price_aggregate_unit' => $value->collectiveAfter,
                    'net_purchase_price_the_largest_unit' => $value->transportAfter,
                    'net_purchase_price_commercial_unit_after_discounts' => $value->commercialAfter,
                    'net_purchase_price_basic_unit_after_discounts' => $value->basicAfter,
                    'net_purchase_price_calculated_unit_after_discounts' => $value->calculationAfter,
                    'net_purchase_price_aggregate_unit_after_discounts' => $value->collectiveAfter,
                    'net_purchase_price_the_largest_unit_after_discounts' => $value->transportAfter,
                ]);

                $orderSum += $value->quantity * $value->commercialAfter;
                $orderWeight += $this->repository->find((int)$key)->weight_trade_unit * $value->quantity;
            }
        }

        $this->orderRepository->update([
            'total_price' => $orderSum,
            'weight' => $orderWeight
        ], $warehouseOrder->id);

        $address = $this->customerAddressRepository->findWhere(['customer_id' => $warehouseOrder->id])->first();

        $this->orderAddressRepository->create([
            'firmname' => $address->firmname,
            'address' => $address->address,
            'flat_number' => $warehouseOrder->customer->flat_number,
            'city' => $warehouseOrder->customer->city,
            'postal_code' => $warehouseOrder->customer->postal_code,
            'email' => $warehouseOrder->customer->email,
            'order_id' => $warehouseOrder->id,
            'type' => 'DELIVERY_ADDRESS'
        ]);

        $this->orderAddressRepository->create([
            'firmname' => $address->firmname,
            'address' => $address->address,
            'flat_number' => $address->flat_number,
            'city' => $address->city,
            'postal_code' => $address->postal_code,
            'email' => $address->email,
            'order_id' => $warehouseOrder->id,
            'type' => 'INVOICE_ADDRESS'
        ]);
        return route('orders.edit', $warehouseOrder->id);
    }

    public function all()
    {
        return view('warehouse_orders.all');
    }

    public function sendEmail(Request $request)
    {
        $data = $request->input('data');
        $warehouseOrder = $this->warehouseOrdersRepository->find($data["id"]);

        dispatch_now(new SendWarehouseOrderEmailJob($warehouseOrder->id, $data["email"], "Test"));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatableAll(Request $request)
    {
        $data = $request->all();
        $collection = $this->prepareCollectionAll($data);
        $countFiltred = $this->countFilteredAll($data);

        $count = $this->warehouseOrdersRepository->all();

        $count = count($count);

        return DataTables::of($collection)->with(['recordsFiltered' => $countFiltred])->skipPaging()->setTotalRecords($count)->make(true);

    }

     /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable(Request $request)
    {
        $data = $request->all();
        $collection = $this->prepareCollection($data);
        $countFiltred = $this->countFiltered($data);

        $count = $this->repository->all();

        $count = count($count);

        return DataTables::of($collection)->with(['recordsFiltered' => $countFiltred])->skipPaging()->setTotalRecords($count)->make(true);

    }

    /**
     * @return mixed
     */
    public function prepareCollectionAll($data)
    {
        $query = \DB::table('warehouse_orders')
            ->select('*');



        $notSearchable = [17, 19];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if(array_key_exists($column['name'], $notSearchable)) {

                } else {
                    $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
                }

            }
        }

        if ($data['search']['value']) {
            foreach ($data['columns'] as $column) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        $collection = $query
            ->limit($data['length'])->offset($data['start'])
            ->get();

        return $collection;
    }

    /**
     * @return mixed
     */
    public function prepareCollection($data)
    {
        $query = \DB::table('products')
            ->select('*', 'product_stocks.id as stockId')
            ->leftJoin('product_stocks', 'product_stocks.product_id', '=', 'products.id')
            ->leftJoin('product_packings', 'product_packings.product_id', '=', 'products.id')
            ->leftJoin('product_prices', 'product_prices.product_id', '=', 'products.id');




        $notSearchable = [17, 19];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if(array_key_exists($column['name'], $notSearchable)) {

                } else {
                    $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
                }

            }
        }

        if ($data['search']['value']) {
            foreach ($data['columns'] as $column) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        $query->whereRaw('products.symbol NOT LIKE "%-%"');

        $collection = $query
            ->limit($data['length'])->offset($data['start'])
            ->get();

        foreach ($collection as $row) {
            $row->positions = \DB::table('product_stock_positions')->where('product_stock_id', $row->stockId)->get();
        }

        return $collection;
    }

     /**
     * @return mixed
     */
    public function countFiltered($data)
    {
        $query = \DB::table('products')
            ->select('*')
            ->leftJoin('product_stocks', 'product_stocks.product_id', '=', 'products.id');

        $notSearchable = [17, 19];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if(array_key_exists($column['name'], $notSearchable)) {

                } else {
                    $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
                }

            }
        }

        if ($data['search']['value']) {
            foreach ($data['columns'] as $column) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        $query->whereRaw('products.symbol NOT LIKE "%-%"');

        $collection = $query->count();

        return $collection;
    }

    /**
     * @return mixed
     */
    public function countFilteredAll($data)
    {
        $query = \DB::table('warehouse_orders')
            ->select('*');

        $notSearchable = [17, 19];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if(array_key_exists($column['name'], $notSearchable)) {

                } else {
                    $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
                }

            }
        }

        if ($data['search']['value']) {
            foreach ($data['columns'] as $column) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        $collection = $query->count();

        return $collection;
    }

}

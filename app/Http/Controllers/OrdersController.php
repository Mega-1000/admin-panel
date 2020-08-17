<?php

namespace App\Http\Controllers;

use App\Entities\Auth_code;
use App\Entities\ColumnVisibility;
use App\Entities\Deliverer;
use App\Entities\InvoiceRequest;
use App\Entities\Label;
use App\Entities\LabelGroup;
use App\Entities\Order;
use App\Entities\OrderFiles;
use App\Entities\OrderInvoice;
use App\Entities\OrderItem;
use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Entities\PackageTemplate;
use App\Entities\Product;
use App\Entities\Role;
use App\Entities\Task;
use App\Entities\UserSurplusPayment;
use App\Entities\UserSurplusPaymentHistory;
use App\Entities\Warehouse;
use App\Helpers\BackPackPackageDivider;
use App\Helpers\EmailTagHandlerHelper;
use App\Helpers\GetCustomerForNewOrder;
use App\Helpers\LabelsHelper;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderCalcHelper;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\OrdersHelper;
use App\Helpers\TaskHelper;
use App\Helpers\TransportSumCalculator;
use App\Http\Requests\CreatePaymentsRequest;
use App\Http\Requests\NoticesRequest;
use App\Http\Requests\OrdersFindPackageRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Jobs\AddLabelJob;
use App\Jobs\AllegroTrackingNumberUpdater;
use App\Jobs\ImportOrdersFromSelloJob;
use App\Jobs\Orders\MissingDeliveryAddressSendMailJob;
use App\Jobs\OrderStatusChangedNotificationJob;
use App\Jobs\RemoveFileLockJob;
use App\Jobs\RemoveLabelJob;
use App\Jobs\SendRequestForCancelledPackageJob;
use App\Jobs\UpdatePackageRealCostJob;
use App\Mail\SendOfferToCustomerMail;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\FirmRepository;
use App\Repositories\LabelGroupRepository;
use App\Repositories\LabelRepository;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductPackingRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockLogRepository;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use App\Repositories\SpeditionExchangeRepository;
use App\Repositories\StatusRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Repositories\WarehouseRepository;
use App\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use iio\libmergepdf\Merger;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Mailer;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class OrderController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersController extends Controller
{
    const ALLSMALLPRINTS_PDF = 'allsmallprints.pdf';
    /**
     * @var FirmRepository
     */
    protected $repository;

    /**
     * @var WarehouseRepository
     */
    protected $warehouseRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EmployeeRepository
     */
    protected $employeeRepository;

    /**
     * @var OrderPaymentRepository
     */
    protected $orderPaymentRepository;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var StatusRepository
     */
    protected $statusRepository;

    /**
     * @var OrderMessageRepository
     */
    protected $orderMessageRepository;

    /**
     * @var OrderAddressRepository
     */
    protected $orderAddressRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ProductPackingRepository
     */
    protected $productPackingRepository;

    /**
     * @var LabelGroupRepository
     */
    protected $labelGroupRepository;

    /**
     * @var LabelRepository
     */
    protected $labelRepository;

    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * @var ProductStockPositionRepository
     */
    protected $productStockPositionRepository;

    /**
     * @var ProductStockLogRepository
     */
    protected $productStockLogRepository;

    /**
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /** @var SpeditionExchangeRepository */
    protected $speditionExchangeRepository;

    /** @var OrderPackageRepository */
    protected $orderPackageRepository;

    /** @var TaskRepository */
    protected $taskRepository;
    protected $dtColumns = [
        'clientFirstname' => 'customer_addresses.firstname',
        'clientLastname' => 'customer_addresses.lastname',
        'clientEmail' => 'customer_addresses.email',
        'clientPhone' => 'customer_addresses.phone',
        'statusName' => 'statuses.name',
        'name' => 'users.name',
        'orderId' => 'orders.id',
        'orderDate' => 'orders.created_at',
    ];
    private $replaceSearch = [
        'sello_payment' => 'sel_tr__transaction.tr_CheckoutFormPaymentId'
    ];

    /**
     * OrdersController constructor.
     * @param FirmRepository $repository
     * @param WarehouseRepository $warehouseRepository
     * @param OrderRepository $orderRepository
     * @param CustomerRepository $customerRepository
     * @param EmployeeRepository $employeeRepository
     * @param OrderPaymentRepository $orderPaymentRepository
     * @param CustomerAddressRepository $customerAddressRepository
     * @param OrderItemRepository $orderItemRepository
     * @param StatusRepository $statusRepository
     * @param OrderMessageRepository $orderMessageRepository
     * @param OrderAddressRepository $orderAddressRepository
     * @param UserRepository $userRepository
     * @param ProductPackingRepository $productPackingRepository
     * @param LabelGroupRepository $labelGroupRepository
     * @param LabelRepository $labelRepository
     * @param ProductStockRepository $productStockRepository
     * @param ProductStockPositionRepository $productStockPositionRepository
     * @param ProductStockLogRepository $productStockLogRepository
     * @param FirmRepository $firmRepository
     * @param ProductRepository $productRepository
     * @param SpeditionExchangeRepository $speditionExchangeRepository
     * @param OrderPackageRepository $orderPackageRepository
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        FirmRepository $repository,
        WarehouseRepository $warehouseRepository,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        EmployeeRepository $employeeRepository,
        OrderPaymentRepository $orderPaymentRepository,
        CustomerAddressRepository $customerAddressRepository,
        OrderItemRepository $orderItemRepository,
        StatusRepository $statusRepository,
        OrderMessageRepository $orderMessageRepository,
        OrderAddressRepository $orderAddressRepository,
        UserRepository $userRepository,
        ProductPackingRepository $productPackingRepository,
        LabelGroupRepository $labelGroupRepository,
        LabelRepository $labelRepository,
        ProductStockRepository $productStockRepository,
        ProductStockPositionRepository $productStockPositionRepository,
        ProductStockLogRepository $productStockLogRepository,
        FirmRepository $firmRepository,
        ProductRepository $productRepository,
        SpeditionExchangeRepository $speditionExchangeRepository,
        OrderPackageRepository $orderPackageRepository,
        TaskRepository $taskRepository
    )
    {
        $this->repository = $repository;
        $this->warehouseRepository = $warehouseRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeRepository = $employeeRepository;
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->statusRepository = $statusRepository;
        $this->orderMessageRepository = $orderMessageRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->userRepository = $userRepository;
        $this->productPackingRepository = $productPackingRepository;
        $this->labelGroupRepository = $labelGroupRepository;
        $this->labelRepository = $labelRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockPositionRepository = $productStockPositionRepository;
        $this->productStockLogRepository = $productStockLogRepository;
        $this->firmRepository = $firmRepository;
        $this->productRepository = $productRepository;
        $this->speditionExchangeRepository = $speditionExchangeRepository;
        $this->orderPackageRepository = $orderPackageRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @return Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $labelGroups = LabelGroup::all();
        $labels = Label::all();
        $couriers = \DB::table('order_packages')->distinct()->select('delivery_courier_name')->get();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');
        $storekeepers = User::where('role_id', User::ROLE_STOREKEEPER)->get();

        $allWarehouses = Warehouse::all();
        $customColumnLabels = [];
        foreach ($labelGroups as $labelGroup) {
            $customColumnLabels[$labelGroup->name] = [];
        }

        $groupedLabels = [];
        foreach ($labels as $label) {
            if ($label->label_group_id) {
                $labelGroup = $labelGroups->where('id', $label->label_group_id)->first();
                $groupedLabels[$labelGroup->name][] = $label;
            } else {
                $groupedLabels["bez grupy"][] = $label;
            }
        }
        $loggedUser = $request->user();
        if ($loggedUser->role_id == Role::ADMIN || $loggedUser->role_id == Role::SUPER_ADMIN) {
            $admin = true;
        }
        $labIds = array(
            'production' => Label::PRODUCTION_IDS_FOR_TABLE,
            'payments' => Label::PAYMENTS_IDS_FOR_TABLE,
            'transport' => Label::TRANSPORT_IDS_FOR_TABLE,
            'info' => Label::ADDITIONAL_INFO_IDS_FOR_TABLE,
            'invoice' => Label::INVOICE_IDS_FOR_TABLE,
        );
        $usersQuery = User::with(['orders' => function ($q) {
            $q->where('created_at', '>', Carbon::now()->subMonths(2));
            $q->select('id', 'employee_id', 'remainder_date');
            $q->with(['labels' => function ($q) {
                $q->select('labels.id', 'order_id');
            }]);
        }]);
        if (empty($admin)) {
            $users = $usersQuery->where('id', $loggedUser->id)->get();
        } else {
            $users = $usersQuery->get();
        }
        $out = [];
        $today = Carbon::now();
        foreach ($users as $user) {
            $out[$user->id]['user'] = $user;
            $out[$user->id]['outdated'] = 0;
            foreach ($user->orders as $order) {
                $out[$user->id]['outdated'] += $today->greaterThan($order->remainder_date);
                foreach ($order->labels as $label) {
                    if (empty($out[$user->id][$label->id])) {
                        $out[$user->id][$label->id] = 0;
                    }
                    $out[$user->id][$label->id]++;
                }
            }
        }
        //pobieramy widzialności dla danego moduły oraz użytkownika
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('orders'));
        foreach ($visibilities as $key => $row) {
            $visibilities[$key]->show = json_decode($row->show, true);
            $visibilities[$key]->hidden = json_decode($row->hidden, true);
        }
        $templateData = PackageTemplate::orderBy('list_order', 'asc')->get();
        $deliverers = Deliverer::all();
        $glsCount = $this->getTaskQuery(['GLS'])->count();
        $dpdCount = $this->getTaskQuery(['DPD'])->count();
        $pocztexCount = $this->getTaskQuery(['POCZTEX'])->count();
        $inpostCount = $this->getTaskQuery(['INPOST', 'ALLEGRO-INPOST'])->count();
        $couriersCount = ['gls' => $glsCount, 'dpd' => $dpdCount, 'pocztex' => $pocztexCount, 'inpost' => $inpostCount];
        return view('orders.index', compact('customColumnLabels', 'groupedLabels', 'visibilities', 'couriers', 'warehouses'))
            ->withOuts($out)
            ->withLabIds($labIds)
            ->withLabels($labels)
            ->withDeliverers($deliverers)
            ->withTemplateData($templateData)
            ->withUsers($storekeepers)
            ->withCouriersCount($couriersCount)
            ->withAllWarehouses($allWarehouses);
    }

    /**
     * @return Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('orders.create');
    }

    public function editPackages($id)
    {
        Session::put('uri', 'orderPackages');
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $order = $this->orderRepository->with(['customer', 'items', 'labels', 'subiektInvoices', 'sellInvoices'])->find($id);
        $orderId = $id;

        $customerInfo = $this->customerAddressRepository->findWhere([
            "customer_id" => $order->customer->id,
            'type' => 'STANDARD_ADDRESS',
        ])->first();
        $orderInvoiceAddress = $this->orderAddressRepository->findWhere([
            "order_id" => $order->id,
            'type' => 'INVOICE_ADDRESS',
        ])->first();
        $orderDeliveryAddress = $this->orderAddressRepository->findWhere([
            "order_id" => $order->id,
            'type' => 'DELIVERY_ADDRESS',
        ])->first();
        $customerDeliveryAddress = $this->customerAddressRepository->findWhere([
            "customer_id" => $order->customer->id,
            'type' => 'DELIVERY_ADDRESS',
        ])->first();
        $messages = $this->orderMessageRepository->orderBy('type')->findWhere(["order_id" => $order->id]);
        $emails = DB::table('emails_messages')->where('order_id', $orderId)->get();
        $orderItems = $order->items;
        $productsArray = [];
        foreach ($orderItems as $item) {
            $productsArray[] = $item->product_id;
        }

        foreach ($order->items as $item) {
            $productsArray[] = $item->product_id;
        }

        $labelsButtons = Label::whereIn('id', [Label::MASTER_MARK, Label::WAREHOUSE_MARK, Label::CONSULTANT_MARK, Label::SHIPPING_MARK])->get();
        $labelsButtons = $labelsButtons->reduce(function ($reduced, $current) {
            $reduced[$current->id] = $current;
            return $reduced;
        }, []);
        $allProductsFromSupplier = [];
        $productsVariation = $this->getVariations($order);
        foreach ($productsVariation as $variation) {
            foreach ($variation as $item) {
                if (isset($allProductsFromSupplier[$item['product_name_supplier']])) {
                    $sum = (float)$allProductsFromSupplier[$item['product_name_supplier']]['sum'];
                    $sum += $item['sum'];
                } else {
                    $sum = $item['sum'];
                }
                $arr = [
                    'sum' => $sum,
                    'different' => number_format($order->total_price - $sum, 2, '.', ''),
                    'radius' => $item['radius'],
                    'phone' => $item['phone'],
                    'product_name_supplier' => $item['product_name_supplier']
                ];
                $allProductsFromSupplier[$item['product_name_supplier']] = $arr;
            }
        }

        if (!empty($allProductsFromSupplier)) {
            $allProductsFromSupplier = collect($allProductsFromSupplier)->sortBy('different', 1, true);
        } else {
            $allProductsFromSupplier = null;
        }

        $productPacking = $this->productPackingRepository->findWhereIn('product_id', $productsArray);
        $warehouses = $this->warehouseRepository->all();
        if ($order->warehouse_id != null) {
            $warehouse = $this->warehouseRepository->find($order->warehouse_id);
        } else {
            $warehouse = null;
        }

        $users = $this->userRepository->orderBy('name')->findWhereNotIn('role_id', [1])->all();
        $statuses = $this->statusRepository->all();

        //pobieramy widzialności dla danego moduły oraz użytkownika
        $visibilitiesPayments = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('order_payments'));
        foreach ($visibilitiesPayments as $key => $row) {
            $visibilitiesPayments[$key]->show = json_decode($row->show, true);
            $visibilitiesPayments[$key]->hidden = json_decode($row->hidden, true);
        }
        $visibilitiesPackage = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('order_packages'));
        foreach ($visibilitiesPackage as $key => $row) {
            $visibilitiesPackage[$key]->show = json_decode($row->show, true);
            $visibilitiesPackage[$key]->hidden = json_decode($row->hidden, true);
        }
        $visibilitiesTask = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('order_tasks'));
        foreach ($visibilitiesTask as $key => $row) {
            $visibilitiesTask[$key]->show = json_decode($row->show, true);
            $visibilitiesTask[$key]->hidden = json_decode($row->hidden, true);
        }
        $firms = $this->firmRepository->all();

        $customerOrdersToPay = $this->orderRepository->findWhere([
            'customer_id' => $order->customer_id,
            'status_id' => 5,
        ]);

        $clientTotalCost = $order->packages->reduce(function ($prev, OrderPackage $next) {
            return $prev + $next->getClientCosts();
        }, 0);

        $ourTotalCost = $order->packages->reduce(function ($prev, OrderPackage $next) {
            return $prev + $next->getOurCosts();
        }, 0);

        $selInvoices = $order->sellInvoices ?? [];
        $subiektInvoices = $order->subiektInvoices ?? [];
        $orderHasSentLP = $order->hasOrderSentLP();
        if ($order->customer_id == 4128) {
            return view('orders.edit_self',
                compact('visibilitiesTask', 'visibilitiesPackage', 'visibilitiesPayments', 'warehouses', 'order',
                    'users', 'customerInfo', 'orderInvoiceAddress', 'selInvoices', 'subiektInvoices',
                    'orderDeliveryAddress', 'orderItems', 'warehouse', 'statuses', 'messages', 'productPacking',
                    'customerDeliveryAddress', 'firms', 'productsVariation', 'allProductsFromSupplier', 'orderId',
                    'customerOrdersToPay', 'clientTotalCost', 'ourTotalCost', 'labelsButtons'));
        } else {
            return view('orders.edit',
                compact('visibilitiesTask', 'visibilitiesPackage', 'visibilitiesPayments', 'warehouses', 'order',
                    'users', 'customerInfo', 'orderInvoiceAddress', 'selInvoices', 'subiektInvoices',
                    'orderDeliveryAddress', 'orderItems', 'warehouse', 'statuses', 'messages', 'productPacking',
                    'customerDeliveryAddress', 'firms', 'productsVariation', 'allProductsFromSupplier', 'orderId',
                    'customerOrdersToPay', 'orderHasSentLP', 'emails', 'clientTotalCost', 'ourTotalCost', 'labelsButtons'));
        }
    }

    private function getVariations($order)
    {
        $productsVariation = [];

        $orderDeliveryAddress = $this->orderAddressRepository->findWhere([
            "order_id" => $order->id,
            'type' => 'DELIVERY_ADDRESS',
        ])->first();

        foreach ($order->items as $product) {
            if ($product->product->product_group == null) {
                continue;
            }
            $productVar = $this->productRepository->findByField('product_group', $product->product->product_group);
            foreach ($productVar as $prod) {
                $firm = $this->firmRepository->findByField('symbol', $prod->product_name_supplier);
                if ($firm->isEmpty() || $firm->first->id->warehouses->isEmpty()) {
                    continue;
                }
                $radius = 0;
                $warehousePostalCode = $firm->first->id->warehouses->first->id->address->postal_code;
                $firmLatLon = DB::table('postal_code_lat_lon')->where('postal_code', $warehousePostalCode)->get()->first();
                $deliveryAddressLatLon = DB::table('postal_code_lat_lon')->where('postal_code', $orderDeliveryAddress->postal_code)->get()->first();

                if ($firmLatLon != null && $deliveryAddressLatLon != null) {
                    $radius = 73 * sqrt(
                            pow($firmLatLon->latitude - $deliveryAddressLatLon->latitude, 2) +
                            pow($firmLatLon->longitude - $deliveryAddressLatLon->longitude, 2)
                        );
                }
                switch ($prod->variation_unit) {
                    case 'UB':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity * $prod->packing->numbers_of_basic_commercial_units_in_pack;
                        break;
                    case 'UC':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity;
                        break;
                    case 'UCA':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity * $prod->packing->numbers_of_basic_commercial_units_in_pack / $prod->packing->unit_consumption;
                        break;
                    case 'UCO':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity / $prod->packing->number_of_sale_units_in_the_pack;
                        break;
                    default:
                        Log::info(
                            'Invalid variation unit: ' . $prod->variation_unit,
                            ['product_id' => $prod->id, 'class' => get_class($this), 'line' => __LINE__]
                        );
                }

                if ($radius > $firm->first->id->warehouses->first->id->radius ||
                    $prod->price->gross_selling_price_commercial_unit === null ||
                    $prod->price->gross_selling_price_basic_unit === null ||
                    $prod->price->gross_selling_price_calculated_unit === null
                ) {
                    continue;
                }
                if ($prod->id == $product->product->id) {
                    $diff = 0.0;
                } else {
                    $diff = $product->price - number_format($unitData, 2, '.', '');
                }
                $array = [
                    'id' => $prod->id,
                    'name' => $prod->name,
                    'gross_selling_price_commercial_unit' => $prod->price->gross_selling_price_commercial_unit,
                    'gross_selling_price_basic_unit' => $prod->price->gross_selling_price_basic_unit,
                    'gross_selling_price_calculated_unit' => $prod->price->gross_selling_price_calculated_unit,
                    'sum' => number_format($unitData, 2, '.', ''),
                    'different' => $diff,
                    'radius' => $radius,
                    'product_name_supplier' => $prod->product_name_supplier,
                    'phone' => $firm->first->id->phone,
                    'review' => $prod->review,
                    'quality' => $prod->quality,
                    'quality_to_price' => $prod->quality_to_price,
                    'comments' => $prod->comments,
                    'variation_group' => $prod->variation_group,
                    'value_of_the_order_for_free_transport' => $prod->value_of_the_order_for_free_transport
                ];
                $productsVariation[$product->product->id][] = $array;
            }
            foreach ($productsVariation as $variation) {
                if (isset($productsVariation[$product->product->id])) {
                    $productsVariation[$product->product->id] = collect($variation)->sortBy('different', 1, true);
                }
            }
        }

        return $productsVariation;
    }

    public function acceptDeny(OrdersFindPackageRequest $request)
    {
        $finalPdfFileName = self::ALLSMALLPRINTS_PDF;
        $data = $request->validated();
        $skip = $data['skip'] ?? 1;
        if ($data['action'] == 'accept') {
            $skip = $skip - 1;
            try {
                $task = $this->prepareTask($data['package_type'], $skip);
                $similar = OrdersHelper::findSimilarOrders($task->order);
                $this->attachTaskForUser($task, $data['user_id'], $similar);
            } catch (Exception $e) {
                return redirect()->back()->with([
                    'message' => $e->getMessage(),
                    'alert-type' => 'error',
                ]);
            }
            $pdf = Storage::disk('public')->get($finalPdfFileName);
            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline'
            ]);
        } else {
            return $this->findPackage($request);
        }
    }

    /**
     * @param $package_type
     * @param $skip
     * @return mixed
     * @throws Exception
     */
    private function prepareTask($package_type, $skip)
    {
        $courierArray = [];
        switch ($package_type) {
            case 'paczkomat':
                $courierArray = ['INPOST', 'ALLEGRO-INPOST'];
                break;
            case 'gls':
                $courierArray = ['GLS'];
                break;
            case 'dpd':
                $courierArray = ['DPD'];
                break;
            case 'pocztex':
                $courierArray = ['POCZTEX'];
                break;
            default:
                throw new \Exception(__('order_packages.message.package_error'));
        }
        $task = $this->getTaskQuery($courierArray)->offset($skip)->first();
        return $task;
    }

    /**
     * @param $task
     * @param int $user_id
     * @param array $similar
     */
    private function attachTaskForUser($task, int $user_id, array $similar): void
    {
        $newGroup = Task::whereIn('order_id', $similar)->get();
        $newGroup = $newGroup->concat([$task]);
        $duration = $newGroup->reduce(function ($prev, $next) {
            $time = $next->taskTime;
            $finishTime = new Carbon($time->date_start);
            $startTime = new Carbon($time->date_end);
            $totalDuration = $finishTime->diffInMinutes($startTime);
            return $prev + $totalDuration;
        }, 0);
        $dt = Carbon::now();
        $dt->second = 0;
        $data = [
            'start' => $dt->toDateTimeString(),
            'end' => $dt->addMinutes($duration)->toDateTimeString(),
            'id' => $user_id,
            'user_id' => $user_id
        ];
        foreach ($similar as $order_id) {
            dispatch_now(new RemoveLabelJob($order_id, [Label::BLUE_HAMMER_ID]));
        }
        dispatch_now(new RemoveLabelJob($task->order->id, [Label::BLUE_HAMMER_ID]));

        if ($newGroup->count() > 1) {
            TaskHelper::createNewGroup($newGroup, $task, $duration, $data);
        } else {
            TaskHelper::updateAbandonedTaskTime($newGroup->first(), $duration, $data);
        }

        $tsk = Task::find($task->id);
        if ($tsk->parent->count()) {
            $tsk = $tsk->parent->first();
        }
        $tsk->user_id = $user_id;
        $tsk->save();
    }

    public function findPackage(OrdersFindPackageRequest $request)
    {
        $finalPdfFileName = self::ALLSMALLPRINTS_PDF;
        $lockName = 'file.lock.small';
        $data = $request->validated();
        $skip = $data['skip'] ?? 0;
        dispatch((new RemoveFileLockJob($lockName))->delay(360));
        if (File::exists(public_path($lockName)) == true) {
            return response(['error' => 'file_exist']);
        }
        file_put_contents($lockName, '');
        try {
            $task = $this->prepareTask($data['package_type'], $skip);
        } catch (Exception $e) {
            unlink(public_path($lockName));
            return redirect()->back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
        if (empty($task)) {
            unlink(public_path($lockName));
            return redirect()->back()->with([
                'message' => 'Brak nieprzydzielonych paczek dla: ' . $data['package_type'] . ' spróbuj wygenerować paczki dla innego kuriera',
                'alert-type' => 'error',
            ]);
        }
        $user = User::find($data['user_id']);
        $similar = OrdersHelper::findSimilarOrders($task->order);
        if (!$user->can_decline) {
            $this->attachTaskForUser($task, $data['user_id'], $similar);
        }
        $views = $this->createListOfWz($similar, $task, $finalPdfFileName, $lockName);
        $pdf = Storage::disk('public')->get($finalPdfFileName);
        if (!$user->can_decline) {
            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline'
            ]);
        }
        $view = View::make('orders.confirm', ['user_id' => $user->id, 'skip' => $skip + 1, 'package_type' => $data['package_type']]);
        return response($views . $view, 200);
    }

    /**
     * @param array $similar
     * @param $task
     * @param string $finalPdfFileName
     * @param string $lockName
     */
    private function createListOfWz(array $similar, $task, string $finalPdfFileName, string $lockName)
    {
        $allOrders = array_merge($similar, [$task->order->id]);
        $tagHelper = new EmailTagHandlerHelper();
        $merger = new Merger;
        $i = 0;
        $views = '';
        foreach ($allOrders as $ord) {
            $dompdf = new Dompdf(['enable_remote' => true]);
            $order = Order::find($ord);
            $tagHelper->setOrder($order);
            $similar = OrdersHelper::findSimilarOrders($order);

            $view = View::make('orders.print', [
                'similar' => $similar,
                'order' => $order,
                'tagHelper' => $tagHelper,
                'showPosition' => true,
                'notPrint' => true
            ]);
            $views .= $view;
            if (empty($view)) {
                continue;
            }
            $dompdf->loadHTML($view);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents("spec_usr_$i.pdf", $output);
            $merger->addFile(public_path("spec_usr_$i.pdf"));
            $i++;
        }
        $file = $merger->merge();
        file_put_contents(public_path("storage/$finalPdfFileName"), $file);
        while ($i >= 0) {
            File::delete(public_path("spec_usr_$i.pdf"));
            $i--;
        }
        unlink(public_path($lockName));
        return $views;
    }

    public function updateNotices(NoticesRequest $request)
    {
        $request->validated();
        $user = Auth::user();
        if (empty($user)) {
            return response(['errors' => ['message' => "Użytkownik nie jest zalogowany"]], 400);
        }
        $order = Order::find($request->order_id);
        switch ($request->type) {
            case Order::COMMENT_SHIPPING_TYPE:
                $order->spedition_comment .= Order::formatMessage($user, $request->message);
                break;
            case Order::COMMENT_WAREHOUSE_TYPE:
                $order->warehouse_notice .= Order::formatMessage($user, $request->message);
                break;
            case Order::COMMENT_CONSULTANT_TYPE:
                $order->consultant_notices .= Order::formatMessage($user, $request->message);
                break;
            case Order::COMMENT_FINANCIAL_TYPE:
                $order->financial_comment .= Order::formatMessage($user, $request->message);
                break;
            default:
                return response(['errors' => ['message' => "Zły typ komentarza"]], 400);
        }
        $order->save();
        return \response('success');
    }

    /**
     * @param OrderUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        switch ($request->submit) {
            case 'update':
                break;
            case 'store':
                $this->store($request->all());
                break;
        }


        $order = $this->orderRepository->find($id);
        if (empty($order)) {
            abort(404);
        }

        if ($request->input('status') != $order->status_id && empty(Auth::user()->userEmailData) && $request->input('shouldBeSent') == 'on') {
            return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                'message' => __('order_messages.message.email_failure'),
                'alert-type' => 'error',
            ]);
        }
        $totalPrice = 0;
        $profit = 0;
        foreach ($request->input('id') as $productId) {
            $totalPrice += (float)$request->input('net_selling_price_commercial_unit')[$productId] * (int)$request->input('quantity_commercial')[$productId] * 1.23;
            $profit += (((float)$request->input('net_selling_price_commercial_unit')[$productId] * (int)$request->input('quantity_commercial')[$productId]) - ((float)$request->input('net_purchase_price_commercial_unit')[$productId] * (int)$request->input('quantity_commercial')[$productId])) * 1.23;
        }

        $warehouse = $this->warehouseRepository->findWhere(["symbol" => $request->input('delivery_warehouse')])->first();
        if ($request->input('employee') == 'none') {

            $employee = null;
        } else {
            $employee = $request->input('employee');
        }
        if ($order->status->id != $request->input('status')) {
            $date = Carbon::now();
            $statusUpdateDate = $date->toDateTimeString();
        } else {
            $statusUpdateDate = $order->last_status_update_date;
        }

        $oldStatus = $order->status_id;

        $this->orderRepository->update([
            'total_price' => $totalPrice,
            'weight' => $request->input('weight'),
            'additional_cash_on_delivery_cost' => $request->input('additional_cash_on_delivery_cost'),
            'document_number' => $request->input('document_number'),
            'warehouse_cost' => $request->input('warehouse_cost'),
            'additional_service_cost' => $request->input('additional_service_cost'),
            'shipment_price_for_client' => $request->input('shipment_price_for_client'),
            'shipment_price_for_us' => $request->input('shipment_price_for_us'),
            'correction_description' => $request->input('correction_description'),
            'correction_amount' => $request->input('correction_amount'),
            'allegro_transaction_id' => $request->input('allegro_transaction_id'),
            'employee_id' => $employee,
            'warehouse_id' => $warehouse ? $warehouse->id : null,
            'status_id' => $request->input('status'),
            'proposed_payment' => $request->input('proposed_payment'),
            'last_status_update_date' => $statusUpdateDate,
            'remainder_date' => $request->input('remainder_date'),
            'shipment_date' => $request->input('shipment_date'),
            'consultant_notice' => $request->input('consultant_notice'),
            'consultant_value' => $request->input('consultant_value'),
            'refund_id' => $request->input('refund_id'),
            'refunded' => $request->input('refunded'),
            'warehouse_value' => $request->input('warehouse_value'),
            'production_date' => $request->input('production_date'),
        ], $id);

        $orderObj = Order::find($id);
        $orderObj->initial_sending_date_client = $request->input('initial_sending_date_client');
        $orderObj->initial_sending_date_consultant = $request->input('initial_sending_date_consultant');
        $orderObj->initial_sending_date_magazine = $request->input('initial_sending_date_magazine');
        $orderObj->confirmed_sending_date_con_mag = $request->input('confirmed_sending_date_con_mag');
        $orderObj->initial_pickup_date_client = $request->input('initial_pickup_date_client');
        $orderObj->confirmed_pickup_date_client = $request->input('confirmed_pickup_date_client');
        $orderObj->confirmed_pickup_date_con_mag = $request->input('confirmed_pickup_date_con_mag');
        $orderObj->initial_delivery_date_con_mag = $request->input('initial_delivery_date_con_mag');
        $orderObj->confirmed_delivery_date = $request->input('confirmed_delivery_date');
        $orderObj->save();
        $task = $this->taskRepository->findByField(['order_id' => $order->id]);

        if ($task->first->id != null) {
            $task->first->id->taskSalaryDetail->update([
                'consultant_notice' => $request->input('consultant_notice'),
                'warehouse_notice' => $request->input('warehouse_notice'),
                'consultant_value' => $request->input('consultant_value'),
                'warehouse_value' => $request->input('warehouse_value')
            ]);
        }

        if ($request->input('status') == Order::STATUS_WITHOUT_REALIZATION) {
            $order->packages->map(function ($package) {
                $cannotCancel = [
                    PackageTemplate::WAITING_FOR_CANCELLED,
                    PackageTemplate::SENDING,
                    PackageTemplate::DELIVERED,
                    PackageTemplate::CANCELLED];
                if (in_array($package->status, $cannotCancel)) {
                    return;
                }
                if ($package->status == PackageTemplate::STATUS_NEW
                    && $package->delivery_courier_name != 'POCZTEX'
                    && $package->service_courier_name != 'POCZTEX') {
                    $package->delete();
                } else {
                    dispatch_now(new SendRequestForCancelledPackageJob($package->id));
                    $package->status = PackageTemplate::WAITING_FOR_CANCELLED;
                    $package->save();
                }
            });
            $order->labels->map(function ($label) use ($order) {
                if (!in_array($label->pivot->label_id, Label::DIALOG_TYPE_LABELS_IDS)) {
                    $order->labels()->detach($label);
                }
            });
        }
        $orderItems = $order->items;
        $itemsArray = [];
        $orderItemKMD = 0;
        foreach ($orderItems as $item) {
            if ($item->product->symbol == 'KMD') {
                $orderItemKMD = $item->quantity;
            }
            $itemsArray[] = $item->product_id;
        }

        if ($order->status_id === 4) {
            $consultantVal = OrderCalcHelper::calcConsultantValue($orderItemKMD, number_format($profit, 2, '.', ''));
        } else {
            $consultantVal = 0;
        }
        $this->orderRepository->update(['consultant_value' => $consultantVal, 'total_price' => $totalPrice], $id);
        foreach ($request->input('product_id') as $key => $value) {
            if (!in_array($value, $itemsArray)) {

                $this->orderItemRepository->create([
                    'net_purchase_price_commercial_unit_after_discounts' => (float)$request->input('net_purchase_price_commercial_unit')[$key],
                    'net_purchase_price_basic_unit_after_discounts' => (float)$request->input('net_purchase_price_basic_unit')[$key],
                    'net_purchase_price_calculated_unit_after_discounts' => (float)$request->input('net_purchase_price_calculated_unit')[$key],
                    'net_purchase_price_aggregate_unit_after_discounts' => (float)$request->input('net_purchase_price_aggregate_unit')[$key],
                    'gross_selling_price_commercial_unit' => (float)$request->input('gross_selling_price_commercial_unit')[$key],
                    'gross_selling_price_basic_unit' => (float)$request->input('gross_selling_price_basic_unit')[$key],
                    'gross_selling_price_calculated_unit' => (float)$request->input('gross_selling_price_calculated_unit')[$key],
                    'gross_selling_price_aggregate_unit' => (float)$request->input('gross_selling_price_aggregate_unit')[$key],
                    'net_selling_price_commercial_unit' => (float)$request->input('net_selling_price_commercial_unit')[$key],
                    'net_selling_price_basic_unit' => (float)$request->input('net_selling_price_basic_unit')[$key],
                    'net_selling_price_calculated_unit' => (float)$request->input('net_selling_price_calculated_unit')[$key],
                    'net_selling_price_aggregate_unit' => (float)$request->input('net_selling_price_aggregate_unit')[$key],
                    'quantity' => (int)$request->input('quantity_commercial')[$key],
                    'price' => (float)$request->input('gross_selling_price_commercial_unit')[$key] * (int)$request->input('quantity_commercial')[$key],
                    'order_id' => $order->id,
                    'product_id' => $value,
                ]);
            }
        }

        if (!empty($request->input('id'))) {
            foreach ($request->input('id') as $id) {
                if ($request->input('quantity_commercial')[$id] > 0) {
                    $this->orderItemRepository->update([
                        'net_purchase_price_commercial_unit_after_discounts' => (float)$request->input('net_purchase_price_commercial_unit')[$id],
                        'net_purchase_price_basic_unit_after_discounts' => (float)$request->input('net_purchase_price_basic_unit')[$id],
                        'net_purchase_price_calculated_unit_after_discounts' => (float)$request->input('net_purchase_price_calculated_unit')[$id],
                        'net_purchase_price_aggregate_unit_after_discounts' => (float)$request->input('net_purchase_price_aggregate_unit')[$id],
                        'gross_selling_price_commercial_unit' => (float)$request->input('gross_selling_price_commercial_unit')[$id],
                        'gross_selling_price_basic_unit' => (float)$request->input('gross_selling_price_basic_unit')[$id],
                        'gross_selling_price_calculated_unit' => (float)$request->input('gross_selling_price_calculated_unit')[$id],
                        'gross_selling_price_aggregate_unit' => (float)$request->input('gross_selling_price_aggregate_unit')[$id],
                        'net_selling_price_commercial_unit' => (float)$request->input('net_selling_price_commercial_unit')[$id],
                        'net_selling_price_basic_unit' => (float)$request->input('net_selling_price_basic_unit')[$id],
                        'net_selling_price_calculated_unit' => (float)$request->input('net_selling_price_calculated_unit')[$id],
                        'net_selling_price_aggregate_unit' => (float)$request->input('net_selling_price_aggregate_unit')[$id],
                        'quantity' => (int)$request->input('quantity_commercial')[$id],
                        'price' => (float)$request->input('gross_selling_price_commercial_unit')[$id] * (int)$request->input('quantity_commercial')[$id],
                    ], $id);
                } else {
                    $orderItem = $this->orderItemRepository->find($id);
                    $orderItem->delete();
                }
            }
        }

        if (!empty($this->orderAddressRepository->findWhere([
            'order_id' => $order->id,
            'type' => 'DELIVERY_ADDRESS',
        ])->first())) {
            $this->orderAddressRepository->findWhere([
                'order_id' => $order->id,
                'type' => 'DELIVERY_ADDRESS',
            ])->first()->update(
                [
                    'address' => $request->input('order_delivery_address_address'),
                    'flat_number' => $request->input('order_delivery_address_flat_number'),
                    'postal_code' => $request->input('order_delivery_address_postal_code'),
                    'city' => $request->input('order_delivery_address_city'),
                    'phone' => $request->input('order_delivery_address_phone'),
                    'firstname' => $request->input('order_delivery_address_firstname'),
                    'lastname' => $request->input('order_delivery_address_lastname'),
                    'firmname' => $request->input('order_delivery_address_firmname'),
                    'email' => $request->input('order_delivery_address_email'),
                ]
            );
        } else {
            $this->orderAddressRepository->create(
                [
                    'type' => 'DELIVERY_ADDRESS',
                    'order_id' => $order->id,
                    'address' => $request->input('order_delivery_address_address'),
                    'flat_number' => $request->input('order_delivery_address_flat_number'),
                    'postal_code' => $request->input('order_delivery_address_postal_code'),
                    'city' => $request->input('order_delivery_address_city'),
                    'phone' => $request->input('order_delivery_address_phone'),
                    'firstname' => $request->input('order_delivery_address_firstname'),
                    'lastname' => $request->input('order_delivery_address_lastname'),
                    'firmname' => $request->input('order_delivery_address_firmname'),
                    'email' => $request->input('order_delivery_address_email'),
                ]
            );
        }

        if (!empty($this->orderAddressRepository->findWhere([
            'order_id' => $order->id,
            'type' => 'INVOICE_ADDRESS',
        ])->first())) {
            $this->orderAddressRepository->findWhere([
                'order_id' => $order->id,
                'type' => 'INVOICE_ADDRESS',
            ])->first()->update(
                [
                    'address' => $request->input('order_invoice_address_address'),
                    'flat_number' => $request->input('order_invoice_address_flat_number'),
                    'postal_code' => $request->input('order_invoice_address_postal_code'),
                    'city' => $request->input('order_invoice_address_city'),
                    'phone' => $request->input('order_invoice_address_phone'),
                    'firstname' => $request->input('order_invoice_address_firstname'),
                    'lastname' => $request->input('order_invoice_address_lastname'),
                    'firmname' => $request->input('order_invoice_address_firmname'),
                    'email' => $request->input('order_invoice_address_email'),
                    'nip' => $request->input('order_invoice_address_nip'),
                ]
            );
        } else {
            $this->orderAddressRepository->create(
                [
                    'type' => 'INVOICE_ADDRESS',
                    'order_id' => $order->id,
                    'address' => $request->input('order_invoice_address_address'),
                    'flat_number' => $request->input('order_invoice_address_flat_number'),
                    'postal_code' => $request->input('order_invoice_address_postal_code'),
                    'city' => $request->input('order_invoice_address_city'),
                    'phone' => $request->input('order_invoice_address_phone'),
                    'firstname' => $request->input('order_invoice_address_firstname'),
                    'lastname' => $request->input('order_invoice_address_lastname'),
                    'firmname' => $request->input('order_invoice_address_firmname'),
                    'email' => $request->input('order_invoice_address_email'),
                    'nip' => $request->input('order_invoice_address_nip'),
                ]
            );
        }

        $sumOfOrdersReturn = $this->sumOfOrders($order);
        $sumToCheck = $sumOfOrdersReturn[0];

        if ($order->status_id == 5 || $order->status_id == 6) {
            if ($sumToCheck > 5 || $sumToCheck < -5) {
                foreach ($sumOfOrdersReturn[1] as $ordId) {
                    dispatch_now(new RemoveLabelJob($ordId, [133]));
                    dispatch_now(new AddLabelJob($ordId, [134]));
                }
            } else {
                foreach ($sumOfOrdersReturn[1] as $ordId) {
                    dispatch_now(new RemoveLabelJob($ordId, [134]));
                    dispatch_now(new AddLabelJob($ordId, [133]));
                }
            }
        } else {
            dispatch_now(new RemoveLabelJob($order, [134]));
            dispatch_now(new RemoveLabelJob($order, [133]));
        }

        if ($order->status_id == 8) {
            $order->labels()->detach();
            $order->taskSchedule()->delete();
            $order->shipment_date = null;
        }

        if ($request->input('status') != $order->status_id && $request->input('shouldBeSent') == 'on') {
            dispatch_now(new OrderStatusChangedNotificationJob($order->id, $request->input('mail_message'), $oldStatus));
        }

        if ($request->input('status') != $order->status_id && $request->input('status') == 3) {      //mozliwa do realizacji
            dispatch_now(new MissingDeliveryAddressSendMailJob($order));
        }

        if ($request->input('status') != $order->status_id && $request->input('status') == 4) {      //mozliwa do realizacji
            dispatch_now(new MissingDeliveryAddressSendMailJob($order));
        }

        if ($request->submit == 'updateAndStay') {
            return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                'message' => __('orders.message.update'),
                'alert-type' => 'success',
            ]);
        }
        return redirect()->route('orders.index', ['order_id' => $order->id])->with([
            'message' => __('orders.message.update'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param $data
     * @return RedirectResponse
     */
    public function store($data)
    {
        unset($data['allegro_transaction_id']);
        $data['total_price'] = 0;
        foreach ($data['id'] as $productId) {
            $data['total_price'] += (float)$data['gross_selling_price_commercial_unit'][$productId] * (int)$data['quantity_commercial'][$productId];
        }
        $order = $this->orderRepository->create($data);
        if (!empty($data['id'])) {
            foreach ($data['id'] as $id) {
                $this->orderItemRepository->create([
                    'net_purchase_price_commercial_unit' => (float)$data['net_purchase_price_commercial_unit'][$id],
                    'net_purchase_price_basic_unit' => (float)$data['net_purchase_price_basic_unit'][$id],
                    'net_purchase_price_calculated_unit' => (float)$data['net_purchase_price_calculated_unit'][$id],
                    'net_purchase_price_aggregate_unit' => (float)$data['net_purchase_price_aggregate_unit'][$id],
                    'net_selling_price_commercial_unit' => (float)$data['net_selling_price_commercial_unit'][$id],
                    'net_selling_price_basic_unit' => (float)$data['net_selling_price_basic_unit'][$id],
                    'net_selling_price_calculated_unit' => (float)$data['net_selling_price_calculated_unit'][$id],
                    'net_selling_price_aggregate_unit' => (float)$data['net_selling_price_aggregate_unit'][$id],
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'][$id],
                    'quantity' => $data['quantity_commercial'][$id],
                    'price' => (float)$data['gross_selling_price_commercial_unit'][$id] * (int)$data['quantity_commercial'][$id],
                ]);
            }
        }

        $this->orderAddressRepository->create([
            'order_id' => $order->id,
            'type' => 'DELIVERY_ADDRESS',
            'address' => $data['order_delivery_address_address'],
            'flat_number' => $data['order_delivery_address_flat_number'],
            'postal_code' => $data['order_delivery_address_postal_code'],
            'city' => $data['order_delivery_address_city'],
            'phone' => $data['order_delivery_address_phone'],
        ]);
        $this->orderAddressRepository->create([
            'order_id' => $order->id,
            'type' => 'INVOICE_ADDRESS',
            'address' => $data['order_invoice_address_address'],
            'flat_number' => $data['order_invoice_address_flat_number'],
            'postal_code' => $data['order_invoice_address_postal_code'],
            'city' => $data['order_invoice_address_city'],
            'phone' => $data['order_invoice_address_phone'],
        ]);

        return redirect()->route('orders.index')->with([
            'message' => __('orders.message.store'),
            'alert-type' => 'success',
        ]);
    }

    public function sumOfOrders($order)
    {
        if ($order->master_order_id != null) {
            $order = $this->orderRepository->find($order->master_order_id);
        } else {
            $order = $this->orderRepository->find($order->id);
        }
        $ids = [];
        $orderItems = $order->items;
        $sum = 0;
        foreach ($orderItems as $item) {
            $sum += $item->gross_selling_price_commercial_unit * $item->quantity;
        }
        $sum += $order->additional_service_cost + $order->additional_cash_on_delivery_cost + $order->shipment_price_for_client;
        $sum = round($sum, 2);
//        dd($sum, $order->additional_service_cost,$order->additional_cash_on_delivery_cost,$order->shipment_price_for_client);
        if ($order->bookedPaymentsSum() - $order->promisePaymentsSum() < -5) {
            $sumOfPayments = $order->promisePaymentsSum() - $order->bookedPaymentsSum();
        } else if ($order->bookedPaymentsSum() - $order->promisePaymentsSum() > 5) {
            $sumOfPayments = $order->bookedPaymentsSum() + $order->promisePaymentsSum();
        } else {
            $sumOfPayments = $order->bookedPaymentsSum();
        }
        if ($sum - $sumOfPayments < 2 && $sum - $sumOfPayments > -2) {
            $mainOrderSum = $sum - $sumOfPayments;
        } else {
            $sumOfPackages = $order->packagesCashOnDeliverySum();
            $mainOrderSum = $sum - ($sumOfPayments + $sumOfPackages);
        }

        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);

        $connectedSum = 0;
        $ids[] = $order->id;
        foreach ($connectedOrders as $connectedOrder) {
            $orderItems = $connectedOrder->items;
            $sum = 0;
            foreach ($orderItems as $item) {
                $sum += $item->gross_selling_price_commercial_unit * $item->quantity;
            }
            $sum += $connectedOrder->additional_service_cost + $connectedOrder->additional_cash_on_delivery_cost + $connectedOrder->shipment_price_for_client;
            $sum = round($sum, 2);

            if ($connectedOrder->bookedPaymentsSum() - $connectedOrder->promisePaymentsSum() < -5) {
                $sumOfPayments = $connectedOrder->promisePaymentsSum();
            } else if ($connectedOrder->bookedPaymentsSum() - $connectedOrder->promisePaymentsSum() > 5) {
                $sumOfPayments = $connectedOrder->bookedPaymentsSum() + $connectedOrder->promisePaymentsSum();
            } else {
                $sumOfPayments = $connectedOrder->bookedPaymentsSum();
            }
            if ($sum - $sumOfPayments < 2 && $sum - $sumOfPayments > -2) {
                $connOrderSum = $sum - $sumOfPayments;
            } else {
                $sumOfPackages = $connectedOrder->packagesCashOnDeliverySum();
                $connOrderSum = $sum - ($sumOfPayments + $sumOfPackages);
            }

            $connectedSum += $connOrderSum;
            $ids[] = $connectedOrder->id;
        }

        if ($mainOrderSum < 0) {
            $sumToCheck = $mainOrderSum + $connectedSum;
        } else if ($connectedSum < 0) {
            $sumToCheck = $mainOrderSum + $connectedSum;
        } else {
            $sumToCheck = $mainOrderSum - $connectedSum;
        }

        return [$sumToCheck, $ids];
    }

    public function setWarehouseAndLabels(Request $request)
    {
        $orderId = $request->order_id;
        $warehouseId = $request->warehouse_id;
        if (empty($orderId)) {
            return response('Błędne zamówienie', 404);
        }
        if (empty($warehouseId)) {
            return response('Błędny magazyn', 404);
        }
        $order = Order::find($orderId);
        $warehouse = Warehouse::find($warehouseId);
        if (empty($order)) {
            return response('Błędne zamówienie', 404);
        }
        if (empty($warehouse)) {
            return response('Błędny magazyn', 404);
        }
        $order->warehouse()->associate($warehouse);
        $order->save();
        $loop = [];

        return dispatch_now(new RemoveLabelJob($order, [$request->label], $loop, $request->labelsToAddIds));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateSelf(Request $request, $id)
    {

        $order = $this->orderRepository->find($id);
        if (empty($order)) {
            abort(404);
        }


        $totalPrice = 0;
        foreach ($request->input('id') as $productId) {
            $totalPrice += (float)$request->input('gross_selling_price_commercial_unit')[$productId] * (int)$request->input('quantity_commercial')[$productId];

        }
        $warehouse = $this->warehouseRepository->findWhere(["symbol" => $request->input('delivery_warehouse')])->first();


        $this->orderRepository->update([
            'total_price' => $totalPrice,
            'weight' => $request->input('weight'),
            'warehouse_id' => $warehouse ? $warehouse->id : null,
            'status_id' => $request->input('status'),
            'additional_info' => $request->input('additional_info'),
            'document_number' => $request->input('document_number'),
            'invoice_number' => $request->input('invoice_number'),
        ], $id);


        $orderItems = $order->items;
        $itemsArray = [];
        foreach ($orderItems as $item) {
            $itemsArray[] = $item->product_id;
        }
        foreach ($request->input('product_id') as $key => $value) {
            if (!in_array($value, $itemsArray)) {
                $this->orderItemRepository->create([
                    'net_purchase_price_commercial_unit' => (float)$request->input('net_purchase_price_commercial_unit')[$key],
                    'net_purchase_price_basic_unit' => (float)$request->input('net_purchase_price_basic_unit')[$key],
                    'net_purchase_price_calculated_unit' => (float)$request->input('net_purchase_price_calculated_unit')[$key],
                    'net_purchase_price_aggregate_unit' => (float)$request->input('net_purchase_price_aggregate_unit')[$key],
                    'quantity' => (int)$request->input('quantity_commercial')[$key],
                    'price' => (float)$request->input('net_purchase_price_commercial_unit')[$key] * (int)$request->input('quantity_commercial')[$key] * 1.23,
                    'order_id' => $order->id,
                    'product_id' => $value,
                ]);
            }
        }

        if (!empty($request->input('id'))) {
            foreach ($request->input('id') as $id) {
                if ($request->input('quantity_commercial')[$id] > 0) {
                    $this->orderItemRepository->update([
                        'net_purchase_price_commercial_unit' => (float)$request->input('net_purchase_price_commercial_unit')[$id],
                        'net_purchase_price_basic_unit' => (float)$request->input('net_purchase_price_basic_unit')[$id],
                        'net_purchase_price_calculated_unit' => (float)$request->input('net_purchase_price_calculated_unit')[$id],
                        'net_purchase_price_aggregate_unit' => (float)$request->input('net_purchase_price_aggregate_unit')[$id],
                        'quantity' => (int)$request->input('quantity_commercial')[$id],
                        'price' => (float)$request->input('net_purchase_price_commercial_unit')[$id] * (int)$request->input('quantity_commercial')[$id] * 1.23,
                    ], $id);
                } else {
                    $orderItem = $this->orderItemRepository->find($id);
                    $orderItem->delete();
                }
            }
        }


        return redirect()->route('orders.index', ['order_id' => $order->id])->with([
            'message' => __('orders.message.update'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $roleId = Auth::user()->role_id;
        if ($roleId != 1 && $roleId != 2) {
            abort(403, 'Nieautoryzowana akcja');
        }
        $order = $this->orderRepository->find($id);
        foreach ($order->packages as $package) {
            $package->delete();
        }
        $deleted = $this->orderRepository->delete($id);

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('orders.message.not_delete'),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->back()->with([
            'message' => __('orders.message.delete'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param Request $request
     * @param $orderId
     * @param $labelId
     */
    public function swapLabelsAfterLabelRemoval(Request $request, $orderId, $labelId)
    {
        $order = $this->orderRepository->find($orderId);
        $labelsToAddAfterRemoval = [];
        $preventionArray = [];

        if (!empty($request->input('manuallyChosen'))) {
            $labelsToAddAfterRemoval = $request->input('labelsToAddIds');
        }
        if ($labelId == LabelsHelper::VALIDATE_ORDER
            && in_array(LabelsHelper::SEND_TO_WAREHOUSE_FOR_VALIDATION, $labelsToAddAfterRemoval)
            && empty($order->warehouse)) {
            return response('warehouse not found', 400);
        }

        $time = $request->input('time');

        return dispatch_now(new RemoveLabelJob($order, [$labelId], $preventionArray, $labelsToAddAfterRemoval, $time));
    }

    public function setPaymentDeadline(Request $request)
    {
        try {
            $data = $request->all();
            $order = Order::findOrFail($data['order_id']);
            $date = $data['date'];
            $d = Carbon::createFromDate($date['year'], $date['month'], $date['day']);
        } catch (Exception $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response('Dane zamówienie nie istnieje', 400);
            }
            return response('Błędny format daty', 400);
        }
        $dat = $d->format('Y-m-d');
        $order->payment_deadline = $dat;
        $order->save();
        return ['status' => true];
    }

    /**
     * @param Request $request
     * @param $labelId
     */
    public function swapLabelsAfterLabelAddition(Request $request, $labelId)
    {
        $orderIds = $request->input('orderIds');
        $preventionArray = [];

        if (count($orderIds) < 1) {
            return;
        }

        if ($request->input('time') !== null) {
            $time = $request->input('time');
        } else {
            $time = false;
        }

        $orders = $this->orderRepository->findWhereIn('id', $orderIds);
        foreach ($orders as $order) {
            dispatch_now(new AddLabelJob($order, [$labelId], $preventionArray, [], null, $time));
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function splitOrders(Request $request)
    {

        $order = $this->orderRepository->find($request->input('orderId'));
        $order->clearPackages();

        if ($request->input('firstOrderExist') == 1) {
            $this->createSplittedOrder($request, $order, 'first');
        }
        if ($request->input('secondOrderExist') == 1) {
            $this->createSplittedOrder($request, $order, 'second');
        }
        if ($request->input('thirdOrderExist') == 1) {
            $this->createSplittedOrder($request, $order, 'third');
        }
        if ($request->input('fourthOrderExist') == 1) {
            $this->createSplittedOrder($request, $order, 'fourth');
        }
        if ($request->input('fifthOrderExist') == 1) {
            $this->createSplittedOrder($request, $order, 'fifth');
        }

        BackPackPackageDivider::calculatePackagesForOrder($order);
        return redirect()->route('orders.index')->with([
            'message' => __('orders.message.split'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param $request
     * @param $order
     * @param $orderType
     */
    public function createSplittedOrder($request, $order, $orderType)
    {
        $data = $request->all();
        $newOrder = $this->orderRepository->create([
            'additional_cash_on_delivery_cost' => $request->input('additional_cash_on_delivery_cost_' . $orderType . 'Order'),
            'additional_service_cost' => $request->input('additional_service_cost_' . $orderType . 'Order'),
            'shipment_price_for_client' => $request->input('shipment_price_for_client_' . $orderType . 'Order'),
            'shipment_price_for_us' => $request->input('shipment_price_for_us_' . $orderType . 'Order'),
            'customer_id' => $order->customer->id,
            'proposed_payment' => $request->input('proposed_payment_' . $orderType . 'Order'),
            'status_id' => $order->status_id,
            'employee_id' => $order->employee_id,
            'warehouse_id' => $order->warehouse_id,
            'master_order_id' => $order->id
        ]);
        $newOrder->getToken();
        $deliveryAddress = $this->orderAddressRepository->findWhere([
            'order_id' => $order->id,
            'type' => 'DELIVERY_ADDRESS',
        ])->first();

        $this->orderAddressRepository->create([
            'order_id' => $newOrder->id,
            'type' => 'DELIVERY_ADDRESS',
            'firstname' => $deliveryAddress->firstname,
            'lastname' => $deliveryAddress->lastname,
            'firmname' => $deliveryAddress->firmname,
            'nip' => $deliveryAddress->nip,
            'email' => $order->customer->login,
            'address' => $deliveryAddress->address,
            'flat_number' => $deliveryAddress->flat_number,
            'postal_code' => $deliveryAddress->postal_code,
            'city' => $deliveryAddress->city,
            'phone' => $deliveryAddress->phone,
        ]);

        $invoiceAddress = $this->orderAddressRepository->findWhere([
            'order_id' => $order->id,
            'type' => 'INVOICE_ADDRESS',
        ])->first();

        $this->orderAddressRepository->create([
            'order_id' => $newOrder->id,
            'type' => 'INVOICE_ADDRESS',
            'firstname' => $invoiceAddress->firstname,
            'lastname' => $invoiceAddress->lastname,
            'firmname' => $invoiceAddress->firmname,
            'nip' => $invoiceAddress->nip,
            'email' => $order->customer->login,
            'address' => $invoiceAddress->address,
            'flat_number' => $invoiceAddress->flat_number,
            'postal_code' => $invoiceAddress->postal_code,
            'city' => $invoiceAddress->city,
            'phone' => $invoiceAddress->phone,
        ]);
        $productsSum = 0;
        $productsWeightSum = 0;
        foreach ($request->input($orderType . 'OrderQuantity') as $id => $quantity) {
            if ($quantity != null) {
                if ($request->input('splitAndUpdate') == 'on') {
                    $item = $this->orderItemRepository->findWhere(['order_id' => $order->id, 'product_id' => $data['product_id'][$id]])->first();

                    $item->update([
                        'quantity' => $item->quantity - $quantity,
                        'price' => (float)$data['gross_selling_price_commercial_unit'][$id] * ($item->quantity - $quantity)
                    ]);
                    $this->orderRepository->update([
                        'weight' => $order->weight - ($item->product->weight_trade_unit * $quantity),
                    ], $order->id);
                }
                $item = new OrderItem();
                $item->net_purchase_price_commercial_unit_after_discounts = (float)$data['net_purchase_price_commercial_unit'][$id];
                $item->net_purchase_price_basic_unit_after_discounts = (float)$data['net_purchase_price_basic_unit'][$id];
                $item->net_purchase_price_calculated_unit_after_discounts = (float)$data['net_purchase_price_calculated_unit'][$id];
                $item->net_purchase_price_aggregate_unit_after_discounts = (float)$data['net_purchase_price_aggregate_unit'][$id];
                $item->net_selling_price_commercial_unit = (float)$data['net_selling_price_commercial_unit'][$id];
                $item->net_selling_price_basic_unit = (float)$data['net_selling_price_basic_unit'][$id];
                $item->net_selling_price_calculated_unit = (float)$data['net_selling_price_calculated_unit'][$id];
                $item->net_selling_price_aggregate_unit = (float)$data['net_selling_price_aggregate_unit'][$id];
                $item->order_id = $newOrder->id;
                $item->product_id = $data['product_id'][$id];
                $item->quantity = $quantity;
                $item->price = (float)$data['gross_selling_price_commercial_unit'][$id] * $quantity;
                $item->save();
                $productsWeightSum += (float)$data['modal_weight'][$id] * $quantity;
                $productsSum += (float)$data['gross_selling_price_commercial_unit'][$id] * $quantity;

            }
        }

        $this->orderRepository->update([
            'total_price' => $productsSum,
            'weight' => $productsWeightSum,
        ], $newOrder->id);
        BackPackPackageDivider::calculatePackagesForOrder($newOrder);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function acceptItemsToStock(Request $request)
    {
        foreach ($request->input('item_quantity') as $id => $quantity) {
            if ($quantity != null) {
                $orderItem = $this->orderItemRepository->findWhere([
                    'id' => $id,
                    'order_id' => $request->input('orderId'),
                ])->first();

                $productStock = $this->productStockRepository->findByField('product_id',
                    $orderItem->product_id)->first();

                $productStockPositionExist = $this->productStockPositionRepository->findWhere(
                    [
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane')[$id],
                        'bookstand' => $request->input('item_bookstand')[$id],
                        'shelf' => $request->input('item_shelf')[$id],
                        'position' => $request->input('item_position')[$id],
                    ]
                )->first();
                if (empty($productStockPositionExist)) {
                    $productStockPosition = $this->productStockPositionRepository->create([
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane')[$id],
                        'bookstand' => $request->input('item_bookstand')[$id],
                        'shelf' => $request->input('item_shelf')[$id],
                        'position' => $request->input('item_position')[$id],
                        'position_quantity' => $quantity,
                    ]);
                } else {
                    $productStockPosition = $this->productStockPositionRepository->update([
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane')[$id],
                        'bookstand' => $request->input('item_bookstand')[$id],
                        'shelf' => $request->input('item_shelf')[$id],
                        'position' => $request->input('item_position')[$id],
                        'position_quantity' => $productStockPositionExist->position_quantity + $quantity,
                    ], $productStockPositionExist->id);
                }


                $this->productStockLogRepository->create([
                    'product_stock_id' => $productStock->id,
                    'product_stock_position_id' => $productStockPosition->id,
                    'order_id' => $request->input('orderId'),
                    'action' => 'ADD',
                    'quantity' => $quantity,
                    'user_id' => Auth::id(),
                ]);

                $this->productStockRepository->update([
                    'quantity' => $productStock->quantity + $quantity,
                ], $productStock->id);


                $this->orderItemRepository->update([
                    'quantity' => $request->input('overallQuantity')[$id],
                ], $orderItem->id);
            }
        }

        foreach ($request->input('item_quantity_second') as $id => $quantity) {
            if ($quantity != null) {
                $orderItem = $this->orderItemRepository->findWhere([
                    'id' => $id,
                    'order_id' => $request->input('orderId'),
                ])->first();
                $productStock = $this->productStockRepository->findByField('product_id',
                    $orderItem->product_id)->first();

                $productStockPositionExist = $this->productStockPositionRepository->findWhere(
                    [
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane_second')[$id],
                        'bookstand' => $request->input('item_bookstand_second')[$id],
                        'shelf' => $request->input('item_shelf_second')[$id],
                        'position' => $request->input('item_position_second')[$id],
                    ]
                )->first();
                if (empty($productStockPositionExist)) {
                    $productStockPosition = $this->productStockPositionRepository->create([
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane_second')[$id],
                        'bookstand' => $request->input('item_bookstand_second')[$id],
                        'shelf' => $request->input('item_shelf_second')[$id],
                        'position' => $request->input('item_position_second')[$id],
                        'position_quantity' => $quantity,
                    ]);
                } else {
                    $productStockPosition = $this->productStockPositionRepository->update([
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane_second')[$id],
                        'bookstand' => $request->input('item_bookstand_second')[$id],
                        'shelf' => $request->input('item_shelf_second')[$id],
                        'position' => $request->input('item_position_second')[$id],
                        'position_quantity' => $productStockPositionExist->position_quantity + $quantity,
                    ], $productStockPositionExist->id);
                }

                $this->productStockLogRepository->create([
                    'product_stock_id' => $productStock->id,
                    'product_stock_position_id' => $productStockPosition->id,
                    'order_id' => $request->input('orderId'),
                    'action' => 'ADD',
                    'quantity' => $quantity,
                    'user_id' => Auth::id(),
                ]);

                $this->productStockRepository->update([
                    'quantity' => $productStock->quantity + $quantity,
                ], $productStock->id);


                $this->orderItemRepository->update([
                    'quantity' => $request->input('overallQuantity')[$id],
                ], $orderItem->id);
            }
        }

        foreach ($request->input('item_quantity_third') as $id => $quantity) {
            if ($quantity != null) {
                $orderItem = $this->orderItemRepository->findWhere([
                    'id' => $id,
                    'order_id' => $request->input('orderId'),
                ])->first();

                $productStock = $this->productStockRepository->findByField('product_id',
                    $orderItem->product_id)->first();

                $productStockPositionExist = $this->productStockPositionRepository->findWhere(
                    [
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane_second')[$id],
                        'bookstand' => $request->input('item_bookstand_second')[$id],
                        'shelf' => $request->input('item_shelf_second')[$id],
                        'position' => $request->input('item_position_second')[$id],
                    ]
                )->first();
                if (empty($productStockPositionExist)) {
                    $productStockPosition = $this->productStockPositionRepository->create([
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane_third')[$id],
                        'bookstand' => $request->input('item_bookstand_third')[$id],
                        'shelf' => $request->input('item_shelf_third')[$id],
                        'position' => $request->input('item_position_third')[$id],
                        'position_quantity' => $quantity,
                    ]);
                } else {
                    $productStockPosition = $this->productStockPositionRepository->update([
                        'product_stock_id' => $productStock->id,
                        'lane' => $request->input('item_lane_third')[$id],
                        'bookstand' => $request->input('item_bookstand_third')[$id],
                        'shelf' => $request->input('item_shelf_third')[$id],
                        'position' => $request->input('item_position_third')[$id],
                        'position_quantity' => $productStockPositionExist->position_quantity + $quantity,
                    ], $productStockPositionExist->id);
                }

                $this->productStockLogRepository->create([
                    'product_stock_id' => $productStock->id,
                    'product_stock_position_id' => $productStockPosition->id,
                    'order_id' => $request->input('orderId'),
                    'action' => 'ADD',
                    'quantity' => $quantity,
                    'user_id' => Auth::id(),
                ]);

                $this->productStockRepository->update([
                    'quantity' => $productStock->quantity + $quantity,
                ], $productStock->id);


                $this->orderItemRepository->update([
                    'quantity' => $request->input('overallQuantity')[$id],
                ], $orderItem->id);
            }
        }

        return redirect()->back()->with([
            'message' => __('orders.message.delete'),
            'alert-type' => 'success',
        ]);

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnItemsFromStock(Request $request)
    {
        foreach ($request->input('item_quantity') as $id => $quantity) {
            if ($quantity != null) {
                $orderItem = $this->orderItemRepository->findWhere([
                    'id' => $id,
                    'order_id' => $request->input('orderId'),
                ])->first();

                $productStock = $this->productStockRepository->findByField('product_id',
                    $orderItem->product_id)->first();

                $productStockPosition = $this->productStockPositionRepository->update([
                    'product_stock_id' => $productStock->id,
                    'position_quantity' => $productStock->quantity - $quantity,
                ], $productStock->position->first()->id);

                $this->productStockLogRepository->create([
                    'product_stock_id' => $productStock->id,
                    'product_stock_position_id' => $productStockPosition->id,
                    'order_id' => $request->input('orderId'),
                    'action' => 'DELETE',
                    'quantity' => $quantity,
                    'user_id' => Auth::id(),
                ]);

                $this->productStockRepository->update([
                    'quantity' => $productStock->quantity - $quantity,
                ], $productStock->id);


                $this->orderItemRepository->update([
                    'quantity' => $quantity,
                ], $orderItem->id);
            }
        }

        return redirect()->back()->with([
            'message' => __('orders.message.delete'),
            'alert-type' => 'success',
        ]);

    }

    /**
     * @param $orderId
     * @return RedirectResponse
     */
    public function sendSelfOrderToWarehouse($orderId)
    {
        $order = $this->orderRepository->find($orderId);
        $prev = [];
        dispatch_now(new AddLabelJob($order, [52], $prev, [], true));

        return redirect()->back()->with([
            'message' => __('orders.message.delete'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function datatable(Request $request)
    {
        $data = $request->all();
        list($collection, $countFiltred) = $this->prepareCollection($data);
        $count = $this->orderRepository->all();
        $count = count($count);
        $collection = $this->prepareAdditionalOrderData($collection);

        return DataTables::of($collection)->with(['recordsFiltered' => $countFiltred])->skipPaging()->setTotalRecords($count)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection($data, $withoutPagination = false, $minId = false)
    {
        $sortingColumnId = $data['order'][0]['column'];
        $sortingColumnDirection = $data['order'][0]['dir'];

        $sortingColumns = [
            4 => 'users.name',
            5 => 'orders.created_at',
            6 => 'orders.id',
            9 => 'statuses.name',
            10 => 'symbol',
            11 => 'customer_notices',
            12 => 'customer_addresses.phone',
            13 => 'customer_addresses.email',
            14 => 'customer_addresses.firstname',
            15 => 'customer_addresses.lastname',
            46 => 'sel_tr__transaction.tr_CheckoutFormPaymentId'
        ];

        if (array_key_exists($sortingColumnId, $sortingColumns)) {
            $sortingColumn = $sortingColumns[$sortingColumnId];
        } else {
            $sortingColumn = 'orders.id';
        }
        $query = $this->getQueryForDataTables()->orderBy($sortingColumn, $sortingColumnDirection);


        $notSearchable = [

        ];

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if (array_key_exists($column['name'], $notSearchable) || $column['name'] == "shipment_date") {
                } else {
                    if (array_key_exists($column['name'], $this->dtColumns)) {
                        if ($column['name'] == 'statusName' && $column['search']['regex'] == true) {
                            $query->whereRaw($this->dtColumns[$column['name']] . ' REGEXP ' . "'{$column['search']['value']}'");
                        } else {
                            $query->where($this->dtColumns[$column['name']], 'LIKE', "%{$column['search']['value']}%");
                        }
                    } else {
                        if ($column['name'] == 'sello_payment') {
                            $columnName = $this->replaceSearch[$column['name']] ?? $column['name'];
                            $query->where(function ($query) use ($column, $columnName) {
                                $query->where($columnName, 'LIKE', "%{$column['search']['value']}%");
                                $query->orWhere('orders.return_payment_id', 'LIKE', "%{$column['search']['value']}%");
                            });
                        } else {
                            $columnName = $this->replaceSearch[$column['name']] ?? $column['name'];
                            $query->where($columnName, 'LIKE', "%{$column['search']['value']}%");
                        }
                    }
                }
            } else {
                if ($column['name'] == "shipment_date" && !empty($column['search']['value'])) {
                    $now = new Carbon();

                    switch ($column['search']['value']) {
                        case "yesterday":
                            $query->where("orders.shipment_date", '<', $now->toDateString());
                            $query->where("orders.shipment_date", '>=', $now->subDay(1)->toDateString());
                            break;
                        case "today":
                            $query->where("orders.shipment_date", '>=', $now->toDateString());
                            $query->where("orders.shipment_date", '<', $now->addDay(1)->toDateString());
                            break;
                        case "tomorrow":
                            $query->where("orders.shipment_date", '>=', $now->addDay(1)->toDateString());
                            $query->where("orders.shipment_date", '<', $now->addDay(1)->toDateString());
                            break;
                        case "from_tomorrow":
                            $query->where("orders.shipment_date", '>=', $now->addDay(1)->toDateString());
                            break;
                        case "all":
                        default:
                            break;
                    }
                } elseif ($column['name'] == "remainder_date" && isset($column['search']['value'])) {
                    $val = filter_var($column['search']['value'], FILTER_VALIDATE_BOOLEAN);
                    if ($val) {
                        $query->whereRaw('remainder_date < Now()');
                    }
                } elseif ($column['name'] == "search_on_lp" && !empty($column['search']['value'])) {
                    $query->leftJoin('order_packages', 'orders.id', '=', 'order_packages.order_id');
                    $query->whereRaw('order_packages.letter_number' . ' REGEXP ' . "'{$column['search']['value']}'");
                } elseif (in_array($column['name'], $this->getLabelGroupsNames()) && !empty($column['search']['value'])) {
                    $query->whereExists(function ($innerQuery) use ($column) {
                        $innerQuery->select("*")
                            ->from('order_labels')
                            ->whereRaw("order_labels.order_id = orders.id and order_labels.label_id = {$column['search']['value']}");
                    });
                } elseif ($column['name'] == "packages_sent" && !empty($column['search']['value'])) {
                    $query->whereExists(function ($innerQuery) use ($column) {
                        $innerQuery->select("*")
                            ->from('order_packages')
                            ->whereRaw("order_packages.order_id = orders.id AND order_packages.delivery_courier_name LIKE '{$column['search']['value']}' AND order_packages.status IN ('SENDING', 'DELIVERED')");
                    });
                } elseif ($column['name'] == "packages_not_sent" && !empty($column['search']['value'])) {
                    $query->whereExists(function ($innerQuery) use ($column) {
                        $innerQuery->select("*")
                            ->from('order_packages')
                            ->whereRaw("order_packages.order_id = orders.id AND order_packages.delivery_courier_name LIKE '{$column['search']['value']}' AND order_packages.status NOT IN ('SENDING', 'DELIVERED', 'CANCELLED')");
                    });
                } elseif ($column['name'] == 'sum_of_payments' && !empty($column['search']['value'])) {
                    $query->whereRaw('(select sum(amount) from order_payments where order_payments.order_id = orders.id)' . ' LIKE ' . "'%{$column['search']['value']}%'");
                } elseif ($column['name'] == 'sum_of_gross_values' && !empty($column['search']['value'])) {
                    $query->whereRaw('CAST((select sum(net_selling_price_commercial_unit * quantity * 1.23) from order_items where order_id = orders.id) AS DECIMAL (12,2)) + IFNULL(orders.additional_service_cost, 0) + IFNULL(orders.additional_cash_on_delivery_cost, 0) + IFNULL(orders.shipment_price_for_client, 0)' . ' LIKE ' . "'%{$column['search']['value']}%'");
                } elseif ($column['name'] == 'left_to_pay' && !empty($column['search']['value'])) {
                    $sumQuery = 'IFNULL((CAST((select sum(gross_selling_price_commercial_unit * quantity) from order_items where order_id = orders.id) AS DECIMAL (12,2)) + IFNULL(orders.additional_service_cost, 0) + IFNULL(orders.additional_cash_on_delivery_cost, 0) + IFNULL(orders.shipment_price_for_client, 0)) - ifnull((select sum(amount) from order_payments where order_payments.order_id = orders.id), 0),0)';
                    if ($data['differenceMode'] == true) {
                        $differenceUp = $column['search']['value'] + 2;
                        $differenceDown = $column['search']['value'] - 2;
                        $query->whereRaw($sumQuery . ' < ' . "{$differenceUp}" . ' AND ' . $sumQuery . ' > ' . $differenceDown);
                    } else {
                        $query->whereRaw($sumQuery . ' LIKE ' . "'%{$column['search']['value']}%'");
                    }
                }
            }
        }

        if ($minId) {
            $query->where($sortingColumns[6], '>', $minId);
        }

        $count = $query->count();

        if ($withoutPagination) {
            $collection = $query
                ->get();
        } else {
            $collection = $query
                ->limit($data['length'])->offset($data['start'])
                ->get();
        }

        foreach ($collection as $row) {
            $orderId = $row->orderId;
            $row->items = \DB::table('order_items')->where('order_id', $row->orderId)->get();
            $row->connected = \DB::table('orders')->where('master_order_id', $row->orderId)->get();
            $row->payments = \DB::table('order_payments')->where('order_id', $row->orderId)->get();
            $row->packages = \DB::table('order_packages')->where('order_id', $row->orderId)->get();
            $row->otherPackages = \DB::table('order_other_packages')->where('order_id', $row->orderId)->get();
            $row->addresses = \DB::table('order_addresses')->where('order_id', $row->orderId)->get();
            $row->history = Order::where('customer_id', $row->customer_id)->with('labels')->get();
            $invoices = \DB::table('order_order_invoices')->where('order_id', $row->orderId)->get(['id']);
            $arrInvoice = [];
            foreach ($invoices as $invoice) {
                array_push($arrInvoice, $invoice->id);
            }
            if (count($arrInvoice) > 0) {
                $row->invoices = \DB::table('order_invoices')->whereIn('id', $arrInvoice)->where('invoice_type', 'buy')->get();
            }

            $labels = [];
            $labelsIds = \DB::table('order_labels')->where('order_id', $row->orderId)->get();
            if (!empty($labelsIds)) {
                foreach ($labelsIds as $labelId) {
                    $labels[] = \DB::table('labels')->where('id', $labelId->label_id)->get();
                }
                foreach ($labels as $label) {
                    if (isset($label[0])) {
                        if (!empty($label[0]->label_group_id)) {
                            $label[0]->label_group = \DB::table('label_groups')->where('id',
                                $label[0]->label_group_id)->get();
                        }

                        foreach ($labelsIds as $labelId) {
                            if ($labelId->label_id === $label[0]->id) {
                                $label[0]->added_type = $labelId->added_type;
                                break;
                            }
                        }
                    }
                }
            }
            $row->labels = $labels;
            $row->closest_label_schedule_type_c = \DB::table('order_label_schedulers')
                ->where('order_id', $row->orderId)
                ->where('type', 'C')
                ->where('triggered_at', null)
                ->orderBy('trigger_time')
                ->first();
            $row->generalMessage = $this->generateMessagesForTooltip($row->orderId, "GENERAL");
            $row->shippingMessage = $this->generateMessagesForTooltip($row->orderId, "SHIPPING", false);
            $row->warehouseMessage = $this->generateMessagesForTooltip($row->orderId, "WAREHOUSE");
            $row->complaintMessage = $this->generateMessagesForTooltip($row->orderId, "COMPLAINT");
            $row->transport_exchange_offers = $this->speditionExchangeRepository
                ->with(['speditionOffers', 'chosenSpedition'])
                ->whereHas('items', function ($q) use ($orderId) {
                    $q->where('order_id', '=', $orderId);
                })
                ->all();
            $row->files = OrderFiles::where('order_id', $row->orderId)->get();
        }

        return [$collection, $count];
    }

    /**
     * @return mixed
     */
    private function getQueryForDataTables()
    {
        $query = \DB::table('orders')
            ->distinct()
            ->select('*', 'orders.created_at as orderDate', 'orders.id as orderId',
                'customer_addresses.email as clientEmail', 'statuses.name as statusName',
                'customer_addresses.firstname as clientFirstname', 'customer_addresses.lastname as clientLastname',
                'customer_addresses.phone as clientPhone', 'sel_tr__transaction.tr_CheckoutFormPaymentId as sello_payment',
                'task_times.date_start as production_date', 'taskUser.firstname as taskUserFirstName')
            //poniższe left joiny mają na celu wyświetlenie czasów oraz wykonwaców zadań z tabeli tasks na "gridzie"
            ->leftJoin('tasks', 'orders.id', '=', 'tasks.order_id')
            ->leftJoin('tasks as parentTask', 'parentTask.id', '=', 'tasks.parent_id')
            ->leftJoin('users as taskUser', \DB::raw('COALESCE(parentTask.user_id, tasks.user_id)'), '=', 'taskUser.id')
            ->leftJoin('task_times', 'task_times.task_id', '=', \DB::raw('COALESCE(parentTask.id, tasks.id)'))
            //tasks - koniec
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('warehouses', 'orders.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('statuses', 'orders.status_id', '=', 'statuses.id')
            ->leftJoin('sel_tr__transaction', 'orders.sello_id', '=', 'sel_tr__transaction.id')
            ->leftJoin('users', 'orders.employee_id', '=', 'users.id')
            ->leftJoin('customer_addresses', function ($join) {
                $join->on('customers.id', '=', 'customer_addresses.customer_id')
                    ->where('type', '=', 'STANDARD_ADDRESS');
            })->where(function ($query) {
                if (Auth::user()->role_id == 4) {
                    $query->where('orders.employee_id', '=', Auth::user()->id);
                }
            });
        return $query;
    }

    /**
     * @return array
     */
    private function getLabelGroupsNames()
    {
        return [
            'label_platnosci',
            'label_produkcja',
            'label_transport',
            'label_info_dodatkowe',
            'label_fakury_zakupu',
        ];
    }

    /**
     * @param $orderId
     * @param $type
     * @param bool $allSources
     * @return string
     */
    protected function generateMessagesForTooltip($orderId, $type, $allSources = true)
    {
        $messagesDb = \DB::table('order_messages')->where('order_id', $orderId)->where('type', $type);
        if ($allSources) {
            $messagesDb = $messagesDb->get();
        } else {
            $messagesDb = $messagesDb->where('source', 'FORM')->get();
        }

        $messages = [];
        if (count($messagesDb)) {
            foreach ($messagesDb as $message) {
                $messages[] = $message->message;
            }
        }

        return implode(' ----------- ', $messages);
    }

    public function prepareAdditionalOrderData($collection)
    {
        foreach ($collection as $order) {
            $additional_service = $order->additional_service_cost ?? 0;
            $additional_cod_cost = $order->additional_cash_on_delivery_cost ?? 0;
            $shipment_price_client = $order->shipment_price_for_client ?? 0;
            $totalProductPrice = 0;
            foreach ($order->items as $item) {
                $price = $item->gross_selling_price_commercial_unit ?: $item->net_selling_price_commercial_unit ?: 0;
                $quantity = $item->quantity ?? 0;
                $totalProductPrice += $price * $quantity;
            }
            $products_value_gross = round($totalProductPrice, 2);
            $sum_of_gross_values = round($totalProductPrice + $additional_service + $additional_cod_cost + $shipment_price_client, 2);
            $order->values_data = array(
                'sum_of_gross_values' => $sum_of_gross_values,
                'products_value_gross' => $products_value_gross,
                'shipment_price_for_client' => $order->shipment_price_for_client ?? 0,
                'additional_cash_on_delivery_cost' => $order->additional_cash_on_delivery_cost ?? 0,
                'additional_service_cost' => $order->additional_service_cost ?? 0
            );
            $order->sello_payment = $order->sello_payment ?? $order->return_payment_id;
        }
        return $collection;
    }

    public function sendVisibleCouriers(Request $request)
    {
        $data = $request->all();
        list($collection, $countFiltred) = $this->prepareCollection($data);
        if (!$countFiltred) {
            return \response("Brak zamówień");
        }
        $messages = [];
        foreach ($collection as $ord) {
            $message = $this->sendPackagesForOrder($ord);
            if (!empty($message)) {
                $messages = array_merge($messages, $message);
            }
        }
        return \response(['errors' => $messages], 200);
    }

    private function sendPackagesForOrder($ord)
    {
        $order = Order::find($ord->orderId);
        $messages = [];
        $packages = $order->packages->where('status', 'NEW');
        foreach ($packages as $package) {
            try {
                list($message, $messages) = app(OrdersPackagesController::class)->sendPackage($package, $messages);
                if (!empty($message)) {
                    $messages [] = $message;
                }
            } catch (\Exception $e) {
                \Log::error('błąd przy nadawaniu hurtowym paczki', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
                $messages [] = $e->getMessage();
            }
        }
        return $messages;
    }

    public function printAll(Request $request)
    {
        $finalPdfFileName = 'allPrints.pdf';
        $lockName = 'file.lock';
        dispatch((new RemoveFileLockJob($lockName))->delay(360));
        if (File::exists(public_path($lockName))) {
            return response(['error' => 'file_exist']);
        }
        file_put_contents($lockName, '');
        $data = $request->all();
        list($collection, $count) = $this->prepareCollection($data, true);
        $tagHelper = new EmailTagHandlerHelper();
        $merger = new Merger;
        $i = 0;
        foreach ($collection as $ord) {
            $dompdf = new Dompdf(['enable_remote' => true]);

            $order = Order::find($ord->orderId);
            $tagHelper->setOrder($order);
            $similar = OrdersHelper::findSimilarOrders($order);
            $view = View::make('orders.print', [
                'similar' => $similar,
                'order' => $order,
                'tagHelper' => $tagHelper,
                'showPosition' => true
            ]);
            if (empty($view)) {
                continue;
            }
            $dompdf->loadHTML($view);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents("spec_$i.pdf", $output);
            $merger->addFile(public_path("spec_$i.pdf"));
            $i++;
        }
        $file = $merger->merge();
        file_put_contents(public_path("storage/"), $file);
        while ($i >= 0) {
            File::delete(public_path("spec_$i.pdf"));
            $i--;
        }
        unlink(public_path($lockName));
        return Storage::file($finalPdfFileName, now());
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getStatusMessage($id)
    {
        $status = $this->statusRepository->find($id);
        $message = $status->message;

        return response()->json($message);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $data = Product::where('symbol', 'LIKE', "%{$request->input('term')}%")->limit(10)->pluck('symbol')->toArray();

        return response()->json($data);
    }

    /**
     * @param $symbol
     * @return JsonResponse
     */
    public function addProduct($symbol)
    {
        $product = Product::where('symbol', 'LIKE', "%{$symbol}%")->get();

        return response()->json([
            'product' => $product->first->id,
            'price' => $product->first->id->price,
            'packing' => $product->first->id->packing,
            'imageUrl' => $product->getImageUrl()
        ]);
    }

    /**
     * @param $token
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function print($token)
    {
        $order = $this->orderRepository->where('token', $token)->first();

        if (empty($order)) {
            abort(404);
        }
        $tagHelper = new EmailTagHandlerHelper();
        $tagHelper->setOrder($order);
        $order->print_order = true;
        $order->update();
        $showPosition = is_a(Auth::user(), User::class);
        $similar = OrdersHelper::findSimilarOrders($order);
        return View::make('orders.print', [
            'order' => $order,
            'similar' => $similar ?? [],
            'tagHelper' => $tagHelper,
            'showPosition' => $showPosition
        ]);
    }

    /**
     * @param $orderIdToGet
     * @param $orderIdToSend
     * @return JsonResponse
     */
    public function moveData($orderIdToGet, $orderIdToSend)
    {
        $orderToGetData = $this->orderRepository->find($orderIdToGet);
        $orderToSendData = $this->orderRepository->find($orderIdToSend);
        if (empty($orderToGetData) || empty($orderToSendData)) {
            abort(404);
        }
        foreach ($orderToGetData->addresses as $address) {
            if ($address->type == 'DELIVERY_ADDRESS') {
                $deliveryAddress = [
                    'firstname' => $address->firstname,
                    'lastname' => $address->lastname,
                    'firmname' => $address->firmname,
                    'nip' => $address->nip,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'flat_number' => $address->flat_number,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                    'email' => $address->email
                ];
            } else {
                if ($address->type == 'INVOICE_ADDRESS') {
                    $invoiceAddress = [
                        'firstname' => $address->firstname,
                        'lastname' => $address->lastname,
                        'firmname' => $address->firmname,
                        'nip' => $address->nip,
                        'phone' => $address->phone,
                        'address' => $address->address,
                        'flat_number' => $address->flat_number,
                        'city' => $address->city,
                        'postal_code' => $address->postal_code,
                        'email' => $address->email
                    ];
                }
            }
        }

        foreach ($orderToSendData->addresses as $address) {
            if ($address->type == 'DELIVERY_ADDRESS') {
                $this->orderAddressRepository->update($deliveryAddress, $address->id);
            } else {
                if ($address->type == 'INVOICE_ADDRESS') {
                    $this->orderAddressRepository->update($invoiceAddress, $address->id);
                }
            }
        }

        return response()->json('done', 200);
    }

    /**
     * @param $orderIdToGet
     * @param $orderIdToSend
     * @return JsonResponse
     */
    public function movePaymentData(Request $request, $orderIdToGet, $orderIdToSend)
    {
        $orderToGetData = $this->orderRepository->find($orderIdToGet);
        $orderToSendData = $this->orderRepository->find($orderIdToSend);
        $paymentAmount = $request->input('paymentAmount');
        if (empty($orderToGetData) || empty($orderToSendData)) {
            abort(404);
        }

        $masterPaymentId = 0;
        foreach ($orderToGetData->payments()->where('promise', '=', '')->get() as $orderGetPayment) {
            if ($orderGetPayment->amount >= $paymentAmount) {
                $orderGetPayment->update([
                    'amount' => $orderGetPayment->amount - $paymentAmount
                ]);
                $masterPaymentId = $orderGetPayment->master_payment_id;
                break;
            }
        }

        $payment = OrderPayment::create([
            'amount' => str_replace(",", ".", $paymentAmount),
            'master_payment_id' => $masterPaymentId,
            'order_id' => $orderToSendData->id,
            'promise' => '',
            'promise_date' => null,
        ]);

        return response()->json('done', 200);
    }

    public function moveSurplus(Request $request, $orderId)
    {
        $order = $this->orderRepository->find($orderId);
        if (empty($order)) {
            abort(404);
        }
        $surplusAmount = $request->input('surplusAmount');

        $userSurplusPayment = UserSurplusPayment::create([
            'user_id' => $order->customer_id,
            'surplus_amount' => $surplusAmount,
            'order_id' => $order->id
        ]);

        $orderPayment = $order->payments()->where('promise', '')->first();


        $orderPayment->update([
            'amount' => $orderPayment->amount - $surplusAmount
        ]);

        $userSurplusPaymentHistory = UserSurplusPaymentHistory::create([
            'user_id' => $order->customer_id,
            'surplus_amount' => $surplusAmount,
            'operation' => 'INCREASE',
            'order_id' => $order->id,
            'user_surplus_payment' => $userSurplusPayment->id
        ]);

        foreach ($order->customer->orders as $order) {
            dispatch_now(new AddLabelJob($order->id, [Label::ORDER_SURPLUS]));
        }

        return response()->json('done', 200);
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function getDataFromLastOrder($id)
    {
        $order = $this->orderRepository->find($id);

        if (empty($order)) {
            abort(404);
        }

        if ($order->addresses->first->id->email !== null) {
            $orderDeliveryAddresses = $this->orderAddressRepository->findWhere([
                'email' => $order->customer->login,
                'type' => 'DELIVERY_ADDRESS'
            ])->sortBy('created_at')->last();

            $orderInvoiceAddresses = $this->orderAddressRepository->findWhere([
                'email' => $order->customer->login,
                'type' => 'INVOICE_ADDRESS'
            ])->sortBy('created_at')->last();
            $curAddressDelivery = $this->orderAddressRepository->findWhere([
                'order_id' => $order->id,
                'type' => 'DELIVERY_ADDRESS'
            ])->first();
            $curAddressInvoice = $this->orderAddressRepository->findWhere([
                'order_id' => $order->id,
                'type' => 'INVOICE_ADDRESS'
            ])->first();

            if (!empty($orderDeliveryAddresses)) {
                $this->orderAddressRepository->update([
                    'type' => 'DELIVERY_ADDRESS',
                    'firstname' => $orderDeliveryAddresses->firstname,
                    'lastname' => $orderDeliveryAddresses->lastname,
                    'firmname' => $orderDeliveryAddresses->firmname,
                    'nip' => $orderDeliveryAddresses->nip,
                    'phone' => $orderDeliveryAddresses->phone === null ? $orderInvoiceAddresses->phone : $orderDeliveryAddresses->phone,
                    'address' => $orderDeliveryAddresses->address === null ? $orderInvoiceAddresses->address : $orderDeliveryAddresses->address,
                    'flat_number' => $orderDeliveryAddresses->flat_number === null ? $orderInvoiceAddresses->flat_number : $orderDeliveryAddresses->flat_number,
                    'city' => $orderDeliveryAddresses->city === null ? $orderInvoiceAddresses->city : $orderDeliveryAddresses->city,
                    'postal_code' => $orderDeliveryAddresses->postal_code === null ? $orderInvoiceAddresses->postal_code : $orderDeliveryAddresses->postal_code,
                    'email' => $orderDeliveryAddresses->email,
                ], $curAddressDelivery->id);
                $this->orderAddressRepository->update([
                    'type' => 'INVOICE_ADDRESS',
                    'firstname' => $orderInvoiceAddresses->firstname,
                    'lastname' => $orderInvoiceAddresses->lastname,
                    'firmname' => $orderInvoiceAddresses->firmname,
                    'nip' => $orderInvoiceAddresses->nip,
                    'phone' => $orderInvoiceAddresses->phone === null ? $orderDeliveryAddresses->phone : $orderInvoiceAddresses->phone,
                    'address' => $orderInvoiceAddresses->address === null ? $orderDeliveryAddresses->address : $orderInvoiceAddresses->address,
                    'flat_number' => $orderInvoiceAddresses->flat_number === null ? $orderDeliveryAddresses->flat_number : $orderInvoiceAddresses->flat_number,
                    'city' => $orderInvoiceAddresses->city === null ? $orderDeliveryAddresses->city : $orderInvoiceAddresses->city,
                    'postal_code' => $orderInvoiceAddresses->postal_code === null ? $orderDeliveryAddresses->postal_code : $orderInvoiceAddresses->postal_code,
                    'email' => $orderInvoiceAddresses->email,
                ], $curAddressInvoice->id);

            }

            return redirect()->back()->with([
                'message' => __('Poprawnie pobrano dane.'),
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'message' => __('Proszę uzupełnić pole email.'),
            'alert-type' => 'error',
        ]);
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function getDataFromCustomer($id)
    {
        $order = $this->orderRepository->find($id);
        if (empty($order)) {
            abort(404);
        }

        if ($order->addresses->first->id->email !== null) {
            $customer = $this->customerRepository->findByField('login', $order->addresses->first->id->email)->first();
            if ($customer != null) {
                foreach ($customer->addresses as $address) {
                    if ($address->type === 'STANDARD_ADDRESS') {
                        $deliveryAddress = $address->toArray();
                    } else {
                        if ($address->type === 'INVOICE_ADDRESS') {
                            $invoiceAddress = $address->toArray();
                        }
                    }
                }
                foreach ($order->addresses as $orderAddress) {
                    if ($orderAddress == 'DELIVERY_ADDRESS') {
                        $orderAddress->update($deliveryAddress);
                    } else {
                        $orderAddress->update($invoiceAddress);
                    }
                }
                return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                    'message' => 'Pomyślnie pobrano dane klienta',
                    'alert-type' => 'success',
                ]);
            } else {
                return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                    'message' => 'Nie znaleziono poprzedniego zamówienia tego klienta',
                    'alert-type' => 'error',
                ]);
            }
        } else {
            return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                'message' => 'Proszę uzupełnić pole email',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * @param $id
     * @param $symbol
     * @return RedirectResponse
     */
    public function getDataFromFirm($id, $symbol)
    {
        $order = $this->orderRepository->find($id);
        if (empty($order)) {
            abort(404);
        }

        $firm = $this->firmRepository->findByField('symbol', $symbol)->first();
        if ($firm != null) {
            $address = $firm->address->toArray();
            $address['firmname'] = $firm->name;
            $address['email'] = $firm->email;
            foreach ($order->addresses as $orderAddress) {
                $orderAddress->update($address);
            }
            return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                'message' => 'Pomyślnie pobrano dane firmy',
                'alert-type' => 'success',
            ]);
        } else {
            return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
                'message' => 'Nie znaleziono firmy',
                'alert-type' => 'error',
            ]);
        }
    }

    public function sendOfferToCustomer($id)
    {
        $order = $this->orderRepository->with(['customer', 'items', 'labels'])->find($id);
        if (empty($order)) {
            abort(404);
        }

        $productsArray = [];
        foreach ($order->items as $item) {
            $productsArray[] = $item->product_id;
        }

        $productsVariation = $this->getVariations($order);
        $allProductsFromSupplier = [];
        $tempVariationCounter = [];
        foreach ($productsVariation as $key => $variation) {
            $variations = current($variation);
            if (isset($tempVariationCounter[current($variations)['variation_group']])) {
                $tempVariationCounter[current($variations)['variation_group']] += 1;
            } else {
                $tempVariationCounter[current($variations)['variation_group']] = 1;
            }

            foreach ($variation as $item) {
                if ($item['variation_group'] == null) {
                    continue;
                }
                if (isset($allProductsFromSupplier[$item['product_name_supplier']])) {
                    $sum = (float)$allProductsFromSupplier[$item['product_name_supplier']][$item['variation_group']]['sum'];
                    $count = (float)$allProductsFromSupplier[$item['product_name_supplier']][$item['variation_group']]['count'];
                    $sum += $item['sum'];
                    $count += 1;
                } else {
                    $sum = $item['sum'];
                    $count = 1;
                }

                $arr = [
                    'missed_product' => $count < $tempVariationCounter[$item['variation_group']],
                    'count' => $count,
                    'sum' => $sum,
                    'different' => number_format($order->total_price - $sum, 2, '.', ''),
                    'radius' => $item['radius'],
                    'phone' => $item['phone'],
                    'product_name_supplier' => $item['product_name_supplier'],
                    'review' => $item['review'],
                    'quality' => $item['quality'],
                    'quality_to_price' => $item['quality_to_price'],
                    'comments' => $item['comments'],
                    'value_of_the_order_for_free_transport' => number_format((float)$item['value_of_the_order_for_free_transport'] - $order->total_price,
                        2, '.', '') <= 0 ? 'Darmowy transport!' : number_format((float)$item['value_of_the_order_for_free_transport'] - $order->total_price,
                        2, '.', '')

                ];
                $allProductsFromSupplier[$item['product_name_supplier']][$item['variation_group']] = $arr;
            }
        }

        if (!empty($allProductsFromSupplier)) {
            $allProductsFromSupplier = collect($allProductsFromSupplier)->sortBy('different', 1, true);
        } else {
            $allProductsFromSupplier = null;
        }
        $productPacking = $this->productPackingRepository->findWhereIn('product_id', $productsArray);

        if (!strpos($order->customer->login, 'allegromail.pl')) {
            Mailer::create()
                ->to($order->customer->login)
                ->send(new SendOfferToCustomerMail('Oferta nr: ' . $order->id, $order, $productsVariation,
                    $allProductsFromSupplier, $productPacking));
        }

        return redirect()->route('orders.edit', ['order_id' => $order->id])->with([
            'message' => 'Pomyślnie wysłano ofertę do klienta',
            'alert-type' => 'success',
        ]);
    }

    public function getCalendar(int $id)
    {
        $warehouse = $this->warehouseRepository->find($id);

        if (empty($warehouse)) {
            abort(404);
        }

        return response()->json($warehouse->users, 200);
    }

    public function addFile(Request $request, $id)
    {
        $order = Order::find($id);
        if (empty($order)) {
            return redirect()->back()->with([
                'message' => __('orders.order_not_found'),
                'alert-type' => 'error'
            ]);
        }
        $file = $request->file('file');
        $extension = explode('.', $file->getClientOriginalName());
        $extension = end($extension);
        if (!in_array($extension, OrderBuilder::VALID_EXTENSIONS)) {
            return redirect()->back()->with([
                'message' => __('orders.files.wrong_type_error'),
                'alert-type' => 'error'
            ]);
        }
        $random = Str::random(40);
        Storage::disk('private')->put('files/' . $order->id . '/' . $random . '.' . $extension, $file->get());
        $order->files()->create([
            'file_name' => $file->getClientOriginalName(),
            'hash' => $random . '.' . $extension
        ]);
        return redirect()->back()->with([
            'message' => __('voyager.media.success_uploaded_file'),
            'alert-type' => 'success'
        ]);
    }

    public function getFile(int $id, string $file_id)
    {
        $type = ["Content-Type" => Storage::disk('private')->mimeType('files/' . $id . '/' . $file_id)];
        $file = Storage::disk('private')->get('files/' . $id . '/' . $file_id);
        return response($file, 200, $type);
    }

    public function deleteFile(int $id)
    {
        $file = OrderFiles::find($id);
        Storage::disk('private')->delete('files/' . $file->order_id . '/' . $file->hash);
        $file->delete();
        return \response('success');
    }

    public function getFiles(int $id)
    {
        return OrderFiles::where('order_id', $id)->get();
    }

    public function getUserInfo(int $id)
    {
        $user = $this->userRepository->find($id);

        return response()->json($user, 200);
    }

    public function confirmCustomerInformation($orderId, $invoiceId)
    {
        $order = $this->orderRepository->find($orderId);

        $subiektAddress = DB::table('gt_addresses_to_check')->where('gt_invoices_id', '=', $invoiceId)->first();

        return view('customers.confirmation.confirmation', compact('order', 'subiektAddress'));
    }

    public function confirmCustomerInformationWithoutData($orderId)
    {
        $order = $this->orderRepository->find($orderId);

        return view('customers.confirmation.confirmation-without-data', compact('order'));
    }

    public function confirmCustomer(Request $request)
    {
        $orderAddress = $this->orderAddressRepository->findWhere(['order_id' => $request->input('orderId'), 'type' => 'INVOICE_ADDRESS'])->first();

        $orderAddress->update([
            'firstname' => $request->input('crm-firstname'),
            'lastname' => $request->input('crm-lastname'),
            'firmname' => $request->input('crm-firmname'),
            'nip' => $request->input('crm-nip'),
            'phone' => $request->input('crm-phone'),
            'address' => $request->input('crm-address'),
            'flat_number' => $request->input('crm-flat-number'),
            'city' => $request->input('crm-city'),
            'postal_code' => $request->input('crm-postal-code'),
            'email' => $request->input('crm-email')
        ]);


        dispatch_now(new RemoveLabelJob($request->input('orderId'), [124]));
        dispatch_now(new AddLabelJob($request->input('orderId'), [136]));

        return view('customers.confirmation.confirmationThanks');
    }

    public function getCosts()
    {
        dispatch_now(new UpdatePackageRealCostJob());
        return redirect()->route('orders.index')->with([
            'message' => 'Rozpoczęto pobieranie realnych wartości zleceń',
            'alert-type' => 'success',
        ]);
    }

    public function selloImport()
    {
        dispatch_now(new ImportOrdersFromSelloJob());
        return redirect()->route('orders.index')->with([
            'message' => 'Rozpoczęto import z Sello',
            'alert-type' => 'success',
        ]);
    }

    public function findPage(Request $request, $id)
    {
        list($collection, $count) = $this->prepareCollection($request->all(), false, $id);
        return response($count / $request->all()['length']);
    }

    public function sendTrackingNumbers()
    {
        dispatch_now(new AllegroTrackingNumberUpdater());
        return redirect()->route('orders.index')->with([
            'message' => 'Rozpoczęto wysyłanie numerów naklejek do allegro',
            'alert-type' => 'success',
        ]);
    }

    public function goToBasket(Request $request)
    {

        try {
            $user = Auth::user();
            if (empty($user)) {
                throw new Exception('Wrong User');
            }
            $order = Order::findOrFail($request->id);


            $code = Str::random(60);
            Auth_code::where('customer_id', $order->customer->id)->delete();
            $authCode = new Auth_code();
            $authCode->token = $code;
            $authCode->customer_id = $order->customer->id;
            $authCode->save();
            $query = http_build_query([
                'cart_token' => $order->getToken(),
                'user_code' => $code
            ]);

            $frontUrl = env('FRONT_URL') . '/koszyk.html?' . $query;
            return redirect($frontUrl);
        } catch (Exception $exception) {
            Log::notice('Can not edit basket', ['message' => $exception->getMessage(), 'stack' => $exception->getTraceAsString()]);
        }
        return redirect()->back()->with([
            'message' => __('firms.message.send_request_to_update_data_error'),
            'alert-type' => 'error'
        ]);
    }

    public function invoiceRequest(Request $request)
    {
        $invoiceRequest = InvoiceRequest::create([
            'order_id' => $request->input('id'),
            'status' => 'MISSING'
        ]);

        return response()->json(['status' => 200]);
    }

    public function getInvoices($id)
    {
        $order = Order::find($id);

        return response()->json($order->buyInvoices);
    }

    public function deleteInvoice($id)
    {
        OrderInvoice::where('id', $id)->delete();

        return response()->json(['status' => 'success']);
    }

    public function createPayments(CreatePaymentsRequest $request)
    {
        $data = $request->validated();
        $arr = json_decode($data['payments_ids']);
        foreach ($arr as $payment) {
            $pay = json_decode($payment);
            $order = Order::where('return_payment_id', $pay->id)->count();
            if ($order) {
                continue;
            }
            $orderParams = [
                'want_contact' => true,
                'phone' => User::CONTACT_PHONE
            ];
            $orderBuilder = new OrderBuilder();
            $orderBuilder
                ->setPackageGenerator(new BackPackPackageDivider())
                ->setPriceCalculator(new OrderPriceCalculator())
                ->setTotalTransportSumCalculator(new TransportSumCalculator)
                ->setUserSelector(new GetCustomerForNewOrder());
            ['id' => $id, 'canPay' => $canPay] = $orderBuilder->newStore($orderParams);
            $order = Order::find($id);
            $order->return_payment_id = $pay->id;
            $order->to_refund = $pay->amount;
            $order->labels()->sync([Label::SHIPPING_MARK]);
            $order->save();
        }
        return back()->with([
            'message' => __('voyager.generic.successfully_added_new'),
            'alert-type' => 'success',
        ]);
    }

    /**
     * @param $order
     * @param $item
     */
    protected function recalculatePackages($order, $item): void
    {
        foreach ($order->packages as $pack) {
            foreach ($pack->packedProducts as $packedProduct) {
                if ($item->quantity === 0 || $item->id !== $packedProduct->id) {
                    continue;
                }
                $diff = min($item->quantity, $packedProduct->pivot->quantity);
                $item->quantity += $diff;
                $packedProduct->pivot->quantity -= $diff;
            }
        }

        foreach ($order->otherPackages as $pack) {
            foreach ($pack->products as $prod) {
                if ($item->quantity === 0 || $item->product_id !== $prod->id) {
                    continue;
                }
                $diff = min($item->quantity, $prod->pivot->quantity);
                $item->quantity -= $diff;
                $prod->pivot->quantity += $diff;
                $prod->pivot->save();
            }
        }
    }

    /**
     * @param $order
     * @param $item
     * @return mixed
     */
    private function calculateTotalAmoutForPackages($order, $item)
    {
        return $order->packages->reduce(function ($acu, $curr) use ($item) {
            $totalAmountForPack = $curr->packedProducts->reduce(function ($acumulator, $current) use ($item) {
                return $acumulator + ($current->id == $item->product_id ? $current->pivot->quantity : 0);
            }, 0);
            return $acu + $totalAmountForPack;
        }, 0);
    }

    private function calculateTotalAmoutForOtherPackages($order, $item)
    {
        return $order->otherPackages->reduce(function ($acu, $curr) use ($item) {
            $totalAmountForPack = $curr->products->reduce(function ($accumulator, $current) use ($item) {
                return $accumulator + ($current->id == $item->product_id ? $current->pivot->quantity : 0);
            }, 0);
            return $acu + $totalAmountForPack;
        }, 0);
    }

    /**
     * @param array $courierArray
     * @return mixed
     */
    private function getTaskQuery(array $courierArray)
    {
        return Task::where('user_id', Task::WAREHOUSE_USER_ID)
            ->with(['taskTime' => function ($query) {
                $query->orderBy('date_start', 'asc');
            }])
            ->whereHas('order', function ($query) use ($courierArray) {
                $query->whereHas('packages', function ($query) use ($courierArray) {
                    $query->whereIn('service_courier_name', $courierArray);
                })->whereHas('labels', function ($query) {
                    $query
                        ->where('labels.id', Label::BLUE_HAMMER_ID);
                })->whereDoesntHave('labels', function ($query) {
                    $query->where('labels.id', Label::RED_HAMMER_ID)
                        ->orWhere('labels.id', Label::GRAY_HAMMER_ID)
                        ->orWhere('labels.id', Label::PRODUCTION_STOP_ID);
                });
            });
    }
}


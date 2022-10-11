<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\DelivererPackageImport\PriceFormatter;
use App\Entities\ConfirmPackages;
use App\Entities\ContainerType;
use App\Entities\ContentType;
use App\Entities\Order;
use App\Entities\OrderOtherPackage;
use App\Entities\OrderPackage;
use App\Entities\PackageTemplate;
use App\Entities\PackingType;
use App\Entities\SelAddress;
use App\Entities\SelTransaction;
use App\Entities\WorkingEvents;
use App\Helpers\DateHelper;
use App\Helpers\OrderPackagesDataHelper;
use App\Helpers\PdfCharactersHelper;
use App\Http\Requests\GetProtocolRequest;
use App\Http\Requests\OrderPackageCostsUpdateRequest;
use App\Http\Requests\OrderPackageCreateRequest;
use App\Http\Requests\OrderPackageUpdateRequest;
use App\Integrations\GLS\GLSClient;
use App\Jobs\AddLabelJob;
use App\Jobs\OrdersCourierJobs;
use App\Jobs\SendRequestForCancelledPackageJob;
use App\Mail\SendDailyProtocolToDeliveryFirmMail;
use App\Repositories\FirmRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PackageTemplateRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use iio\libmergepdf\Merger;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class OrderTasksController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersPackagesController extends Controller
{
    protected $repository;

    protected $orderRepository;

    protected $orderPackagesDataHelper;

    protected $firmRepository;

    protected $packageTemplateRepository;


    public function __construct(
        OrderPackageRepository $repository,
        OrderRepository $orderRepository,
        OrderPackagesDataHelper $orderPackagesDataHelper,
        FirmRepository $firmRepository,
        PackageTemplateRepository $packageTemplateRepository
    )
    {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->orderPackagesDataHelper = $orderPackagesDataHelper;
        $this->firmRepository = $firmRepository;
        $this->packageTemplateRepository = $packageTemplateRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, $id, $multi = null)
    {
        if ($request['package_id']) {
            $cod = OrderPackage::find($request['package_id'])->cash_on_delivery;
        }

        $contentTypes = ContentType::all();
        $packingTypes = PackingType::all();
        $containerTypes = ContainerType::all();
        $templateData = $this->orderPackagesDataHelper->getData();
        $order = $this->orderRepository->find($id);
        $shipmentDate = $this->orderPackagesDataHelper->calculateShipmentDate();
        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        if ($order->master_order_id != null) {
            $mainId = $order->master_order_id;
            $order = $this->orderRepository->find($mainId);
            $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        }
        $promisedPayments = [];
        $payments = [];
        $isAllegro = !empty($order->sello_id);
        WorkingEvents::createEvent(WorkingEvents::ORDER_PACKAGES_CREATE_EVENT, $order->id);

        $cashOnDeliverySum = 0;

        foreach ($order->packages()->where('letter_number', '=', 'null')->get() as $package) {
            $cashOnDeliverySum += $package->cash_on_delivery;
        }

        $allOrdersSum = 0;
        $isAdditionalDKPExists = false;
        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        foreach ($connectedOrders as $connectedOrder) {
            if ($connectedOrder->additional_cash_on_delivery_cost == 50) {
                $isAdditionalDKPExists = true;
            }
            $allOrdersSum += $connectedOrder->getSumOfGrossValues();
        }
        $allOrdersSum += $order->getSumOfGrossValues();
        if ($order->additional_cash_on_delivery_cost == 50) {
            $isAdditionalDKPExists = true;
        }
        foreach ($connectedOrders as $connectedOrder) {
            foreach ($connectedOrder->packages()->where('status', '!=', 'CANCELLED')->get() as $package) {
                $cashOnDeliverySum += $package->cash_on_delivery;
            }
        }

        foreach ($order->payments as $payment) {
            if ($payment->promise == '') {
                $payments[] = [
                    'amount' => $payment->amount,
                    'promise' => $payment->promise
                ];
            } else {
                $promisedPayments[] = [
                    'amount' => $payment->amount,
                    'promise' => $payment->promise
                ];
            }

        }

        $orderData = [
            'shipment_date' => $shipmentDate,
            'delivery_date' => $this->orderPackagesDataHelper->calculateDeliveryDate($shipmentDate),
            'cash_on_delivery_amount' => $cod ?? $order->cash_on_delivery_amount,
            'customer_notices' => $order->customer_notices,
            'shipment_price_for_client' => $order->shipment_price_for_client,
            'shipment_price_for_us' => $order->shipment_price_for_us,
            'weight' => $order->weight,
        ];
        $multiData = null;
        if (!empty($multi)) {
            $sessionData = $request->session()->get('multi');
            if ($multi == $sessionData['token']) {
                $multiData = $sessionData['template'];
                $request->session()->forget('multi');
            } else {
                return redirect()->route('order_packages.create', ['order_id' => $id]);
            }
        }

        return view('orderPackages.create', compact('id', 'templateData', 'orderData', 'order', 'payments', 'promisedPayments', 'connectedOrders', 'cashOnDeliverySum', 'isAdditionalDKPExists', 'allOrdersSum', 'multiData'))
            ->withcontentTypes($contentTypes)
            ->withpackingTypes($packingTypes)
            ->withcontainerTypes($containerTypes)
            ->withisAllegro($isAllegro);
    }

    public function changeValue(Request $request)
    {
        $this->repository->update([
            'cash_on_delivery' => $request->input('modalPackageValue')
        ], $request->input('packageId'));
        return redirect()->back()->with('template-id', $request->input('template-id'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderPackage = OrderPackage::find($id);
        $order = Order::find($orderPackage->order_id);
        WorkingEvents::createEvent(WorkingEvents::ORDER_PACKAGES_EDIT_EVENT, $order->id);
        $isAllegro = !empty($order->sello_id);

        $contentTypes = ContentType::all();
        $packingTypes = PackingType::all();
        $containerTypes = ContainerType::all();

        return view('orderPackages.edit', compact('orderPackage', 'id'))
            ->withcontentTypes($contentTypes)
            ->withpackingTypes($packingTypes)
            ->withcontainerTypes($containerTypes)
            ->withisAllegro($isAllegro);
    }

    /**
     * @param OrderPackageUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OrderPackageUpdateRequest $request, $id)
    {
        $orderPackage = OrderPackage::find($id);

        if (empty($orderPackage)) {
            abort(404);
        }

        $orderId = $orderPackage->order_id;
        WorkingEvents::createEvent(WorkingEvents::ORDER_PACKAGES_UPDATE_EVENT, $orderId);
        $data = $request->validated();
        $data['packing_type'] = $request->input('packing_type');
        $data['delivery_date'] = new \DateTime($data['delivery_date']);
        $data['shipment_date'] = new \DateTime($data['shipment_date']);

        $this->saveOrderPackage($data, $id);

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_packages.message.update'),
            'alert-type' => 'success'
        ]);
    }

    private function saveOrderPackage($data, $id = null)
    {
        if (is_null($id)) {
            $orderPackage = new OrderPackage;
        } else {
            $orderPackage = OrderPackage::find($id);
        }
        $orderPackage->size_a = $data['size_a'];
        $orderPackage->size_b = $data['size_b'];
        $orderPackage->size_c = $data['size_c'];
        $orderPackage->shipment_date = $data['shipment_date'];
        $orderPackage->delivery_date = $data['delivery_date'];
        $orderPackage->delivery_courier_name = $data['delivery_courier_name'];
        $orderPackage->service_courier_name = $data['service_courier_name'];
        $orderPackage->weight = $data['weight'];
        $orderPackage->quantity = 1;
        $orderPackage->container_type = $data['container_type'];
        $orderPackage->notices = $data['notices'];
        $orderPackage->shape = $data['shape'];
        $orderPackage->sending_number = $data['sending_number'];
        $orderPackage->letter_number = $data['letter_number'];
        $orderPackage->cash_on_delivery = $data['cash_on_delivery'];
        $orderPackage->status = $data['status'];
        $orderPackage->cost_for_client = $data['cost_for_client'];
        $orderPackage->cost_for_company = $data['cost_for_company'];
        $orderPackage->content = $data['content'];
        $orderPackage->packing_type = $data['packing_type'];
        if (is_null($id)) {
            $orderPackage->order_id = $data['order_id'];
            $orderPackage->number = $data['number'];
            $orderPackage->symbol = $data['symbol'];
            $orderPackage->chosen_data_template = $data['chosen_data_template'];
        }
        $this->orderPackagesDataHelper->findFreeShipmentDate($orderPackage);
        $orderPackage->save();

        if (!empty($data['real_cost_for_company'])) {
            $orderPackage->realCostsForCompany()->create([
                'order_package_id' => $orderPackage->id,
                'cost' => PriceFormatter::asAbsolute(
                    PriceFormatter::fromString($data['real_cost_for_company'])
                ),
            ]);
        }

        return $orderPackage;
    }

    public function store(OrderPackageCreateRequest $request)
    {
        $order_id = $request->input('order_id');

        $data = $request->validated();
        $toCheck = (float)$request->input('toCheck');
        $data['delivery_date'] = new \DateTime($data['delivery_date']);
        $data['shipment_date'] = new \DateTime($data['shipment_date']);
        if (!empty($request->input('template_accept_hour')) || !empty($request->input('template_max_hour'))) {
            $today = new \DateTime;
            $daydate = $data['shipment_date'];
            $daytoday = $today;
            $daydate->setTime(0, 0, 0);
            $daytoday->setTime(0, 0, 0);
            if ($daydate->diff($daytoday)->days == 0 && empty($request->input('force_shipment'))) {
                $shipdate = $this->orderPackagesDataHelper->calculateShipmentDate($request->input('template_accept_hour'), $request->input('template_max_hour'));
                $delidate = $this->orderPackagesDataHelper->calculateDeliveryDate($shipdate);
                $data['shipment_date'] = new \DateTime($shipdate);
                $data['delivery_date'] = new \DateTime($delidate);
            }
        }

        $packageNumber = OrderPackage::where('order_id', $order_id)->max('number');
        $data['packing_type'] = $request->input('packing_type');
        $data['number'] = $packageNumber + 1;
        $data['symbol'] = $request->input('symbol');
        $notices = $data['notices'];
        $data['notices'] = $data['order_id'] . '/' . $data['number'] . ' ' . $notices;
        if ($data['delivery_courier_name'] === 'GIELDA' || $data['delivery_courier_name'] === 'ODBIOR_OSOBISTY') {
            $data = $this->generateSticker($data);
            $data['status'] = PackageTemplate::WAITING_FOR_SENDING;
        }

        $order = $this->orderRepository->find($order_id);
        WorkingEvents::createEvent(WorkingEvents::ORDER_PACKAGES_STORE_EVENT, $order->id);
        if (empty($packageNumber)) {
            $isAdditionalDKPExists = false;
            $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
            foreach ($connectedOrders as $connectedOrder) {
                if ($connectedOrder->additional_cash_on_delivery_cost == 50) {
                    $isAdditionalDKPExists = true;
                }
            }
            if ($order->toPay() > 5 && $isAdditionalDKPExists == false) {
                $this->orderRepository->update([
                    'additional_cash_on_delivery_cost' => $order->additional_cash_on_delivery_cost + 50,
                ], $order->id);
                $data['cash_on_delivery'] = $data['cash_on_delivery'] + 50;
            }
        }

        $this->saveOrderPackage($data);

        if ($toCheck != 0) {
            dispatch_now(new AddLabelJob($order->id, [134]));
        } else {
            dispatch_now(new AddLabelJob($order->id, [133]));
        }
        if (empty($request->input('quantity')) || $request->input('quantity') <= 1) {
            return redirect()->route('orders.edit', ['order_id' => $order_id])->with([
                'message' => __('order_packages.message.store'),
                'alert-type' => 'success'
            ]);
        }
        $token = md5(uniqid((string)rand(), true));
        $multi = [
            'token' => $token,
            'template' => [
                'quantity' => $request->input('quantity') - 1,
                'size_a' => $request->input('size_a'),
                'size_b' => $request->input('size_b'),
                'size_c' => $request->input('size_c'),
                'shipment_date' => $request->input('shipment_date'),
                'delivery_date' => $request->input('delivery_date'),
                'service_courier_name' => $request->input('service_courier_name'),
                'delivery_courier_name' => $request->input('delivery_courier_name'),
                'shape' => $request->input('shape'),
                'container_type' => $request->input('container_type'),
                'notices' => $request->input('notices'),
                'weight' => $request->input('weight'),
                'cost_for_client' => $request->input('cost_for_client'),
                'cost_for_us' => $request->input('cost_for_company'),
                'chosen_data_template' => $request->input('chosen_data_template'),
                'content' => $request->input('content'),
                'symbol' => $request->input('symbol'),
                'packing_type' => $request->input('packing_type')
            ]
        ];
        $request->session()->put('multi', $multi);
        return redirect()->route('order_packages.create', ['order_id' => $order_id, 'multi' => $token]);

    }

    public function generateSticker($data)
    {
        $order = $this->orderRepository->find($data['order_id']);
        $this->orderPackagesDataHelper->generateSticker($order, $data);

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $package = OrderPackage::find($id);
        if (empty($package)) {
            return redirect()->back()->with([
                'message' => __('orders.message.not_delete'),
                'alert-type' => 'error'
            ])->withInput(['tab' => 'orderPackages']);
        }
        $prods = $package->packedProducts;

        $order = $package->order;
        $container = $order->notCalculable->first();
        if (empty($container)) {
            $container = new OrderOtherPackage();
            $container->type = 'not_calculable';
            $container->order_id = $order->id;
            $container->save();
        }
        $prods->map(function ($product) use ($container) {
            $exist = $container->products()->where('product_id', $product->id)->first();
            if ($exist) {
                $exist->pivot->quantity += $product->pivot->quantity;
                $exist->pivot->save();
            } else {
                $container->products()->attach($product->id, ['quantity' => $product->pivot->quantity]);
            }
        });

        $deleted = $package->delete();
        if (empty($deleted)) {
            if (isset($request->redirect) && $request->redirect == 'false') {
                return response('failure', 400);
            }
            return redirect()->back()->with([
                'message' => __('orders.message.not_delete'),
                'alert-type' => 'error'
            ])->withInput(['tab' => 'orderPackages']);
        }
        if (isset($request->redirect) && $request->redirect == 'false') {
            return response('success');
        }
        return redirect()->back()->with([
            'message' => __('order_packages.message.delete'),
            'alert-type' => 'success'
        ])->withInput(['tab' => 'orderPackages']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable($id)
    {
        $collection = $this->prepareCollection($id);
        return DataTables::collection($collection)->make(true);
    }

    public function prepareCollection($id): Collection
    {
        return $this->repository->with(['realCostsForCompany'])->findByField('order_id', $id);
    }

    public function sendRequestForCancelled($id)
    {
        $this->repository->update(['status' => 'CANCELLED'], $id);

        return redirect()->back()->with([
            'message' => __('order_packages.message.request_for_cancelled_package'),
            'alert-type' => 'success'
        ]);
    }

    public function getProtocols(GetProtocolRequest $request)
    {
        $request->validated();
        $courierName = strtoupper($request->courier);

        if ($courierName !== 'WSZYSTKIE') {
            $packages = OrderPackage::where('delivery_courier_name', '=', $courierName)
                ->leftJoin('orders', 'order_packages.order_id', '=', 'orders.id')
                ->where('order_packages.shipment_date', '<=', Carbon::createFromFormat('d/m/Y',$request->date_to)->format('Y/m/d H:i:s'))
                ->where('order_packages.shipment_date', '>=', Carbon::createFromFormat('d/m/Y',$request->date_from)->subDays(1)->format('Y/m/d H:i:s'))
                ->where('order_packages.status', '!=', 'CANCELLED')
                ->where('order_packages.status', '!=', 'WAITING_FOR_CANCELLED')
                ->where('order_packages.status', '!=', 'REJECT_CANCELLED')
                ->where('order_packages.letter_number', '!=', null)
                ->select('order_packages.*')
                ->orderBy('order_packages.order_id')
                ->get();
        } else {
            $courierName = 'wszystkie';
                $packages = OrderPackage::leftJoin('orders', 'order_packages.order_id', '=', 'orders.id')
                ->where('order_packages.shipment_date', '<=', Carbon::createFromFormat('d/m/Y',$request->date_to)->format('Y/m/d H:i:s'))
                ->where('order_packages.shipment_date', '>=', Carbon::createFromFormat('d/m/Y',$request->date_from)->subDays(1)->format('Y/m/d H:i:s'))
                ->where('order_packages.status', '!=', 'CANCELLED')
                ->where('order_packages.status', '!=', 'WAITING_FOR_CANCELLED')
                ->where('order_packages.status', '!=', 'REJECT_CANCELLED')
                ->where('order_packages.letter_number', '!=', null)
                ->select('order_packages.*')
                ->orderBy('order_packages.order_id')
                ->get();
        }

        if ($packages->count() > 0){
            $packagesArray = [];
            foreach ($packages as $package) {
                if ($package->order->warehouse !== null) {
                    if ($package->order->warehouse->symbol !== $request->delivery_warehouse) {
                        continue;
                    }
                }
                $packagesArr = [
                    'order_id' => $package->order->id,
                    'number' => $package->number,
                    'warehouse' => $package->order->warehouse !== null ? $package->order->warehouse->symbol : null,
                    'size_a' => $package->size_a,
                    'size_b' => $package->size_b,
                    'size_c' => $package->size_c,
                    'delivery_courier_name' => $package->delivery_courier_name,
                    'weight' => $package->weight,
                    'quantity' => $package->quantity,
                    'container_type' => $package->container_type,
                    'letter_number' => $package->letter_number,
                    'phone' => $package->order->addresses->first->id->phone_full,
                    'postal_code' => $package->order->addresses->first->id->postal_code,
                    'city' => PdfCharactersHelper::changePolishCharactersToNonAccented($package->order->addresses->first->id->city ?? ''),
                ];
                array_push($packagesArray, $packagesArr);
            }
            $pdfFilename = 'protocol-' . $courierName . '-' . Carbon::today()->toDateString() . '.pdf';

            $pdf = PDF::loadView('pdf.protocol', [
                'packages' => $packagesArray,
                'date' => DateHelper::dateRangeOrDate($request->date_from, $request->date_to),
                'courierName' => strtoupper($courierName)
            ])->setPaper('a4', 'landscape');
            if (!file_exists(storage_path('app/public/protocols'))) {
                mkdir(storage_path('app/public/protocols'));
            }
            $path = storage_path('app/public/protocols/' . $pdfFilename);
            $pdf->save($path);
            try {
                $this->sendProtocolToDeliveryFirm(strtoupper($courierName), $path);
            } catch (\Exception $e) {
                \Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
            }
            return $pdf->download($pdfFilename);
        } else {
            return redirect()->back()->with([
                'message' => __('order_packages.message.protocol_error'),
                'alert-type' => 'error'
            ]);
        }
    }

    protected function sendProtocolToDeliveryFirm($courierName, $path)
    {
        switch ($courierName) {
            case 'INPOST':
                $firm = $this->firmRepository->findByField('symbol', $courierName)->first();
                $email = $firm->email;
                break;
            case 'DPD':
                $firm = $this->firmRepository->findByField('symbol', $courierName)->first();
                $email = $firm->email;
                break;
            case 'APACZKA':
                $firm = $this->firmRepository->findByField('symbol', $courierName)->first();
                $email = $firm->email;
                break;
            case 'POCZTEX':
                $firm = $this->firmRepository->findByField('symbol', 'POCZTAPOLSKA')->first();
                $email = $firm->email;
                break;
            case 'JAS':
                $firm = $this->firmRepository->findByField('symbol', 'JASBFG')->first();
                $email = $firm->email;
                break;
            default:
                return;
        }

        \Mailer::create()
            ->to($email)
            ->send(new SendDailyProtocolToDeliveryFirmMail("Protokół odbioru z dnia: " . Carbon::today()->toDateString(),
                $path));
    }

    public function getSticker(Request $request, $id)
    {
        $package = OrderPackage::find($id);
        if (empty($package)) {
            return redirect()->back()->with([
                'message' => __('order_packages.message.package_not_found_error'),
                'alert-type' => 'error'
            ]);
        }
        if (empty($package->sending_number)) {
            return redirect()->back()->with([
                'message' => __('order_packages.message.package_not_ordered_error'),
                'alert-type' => 'error'
            ]);
        }
        $this->getStickerForGls($package);
        return Storage::disk('private')->download('labels/gls/' . $package->sending_number . '.pdf');
    }

    public function letters($courier_name)
    {
        if (empty($courier_name)) {
            return redirect()->back()->with([
                'message' => __('order_packages.message.courier_error'),
                'alert-type' => 'error'
            ]);
        }
        $packages = $this->getPackagesToSent($courier_name);
        $merger = new Merger;
        $packages->map(function ($pack) use ($courier_name, $merger) {
            if ($courier_name == 'GLS') {
                $file = $this->getStickerForGls($pack);
                $merger->addRaw($file);
            } else {
                $file = $pack->getPathToSticker();
                if (File::exists(public_path($file))) {
                    $merger->addFile(public_path($file));
                }
            }
        });

        return response($merger->merge())->withHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "filename=listy-$courier_name.pdf"
        ]);
    }

    public function prepareGroupPackageToSend($courierName)
    {
        ini_set('max_execution_time', 600);
        if ($courierName == 'APACZKA' || $courierName == 'INPOST' || $courierName == 'DPD' || $courierName == 'POCZTEX' || $courierName == 'JAS' ||  $courierName == 'GLS' || $courierName == 'ALL') {
            if ($courierName !== 'ALL') {
                $packages = $this->repository->findWhere([
                    ['status', '=', 'NEW'],
                    ['delivery_courier_name', '=', $courierName],
                    ['shipment_date', '=', Carbon::today()]
                ]);
            } else {
                $packages = $this->repository->findWhere([
                    ['status', '=', 'NEW'],
                    ['delivery_courier_name', '!=', 'GIELDA'],
                    ['delivery_courier_name', '!=', 'ODBIOR_OSOBISTY'],
                    ['shipment_date', '=', Carbon::today()->addDays(2)]
                ]);
            }
            $messages = [];

            if (!$packages->isEmpty()) {
                foreach ($packages as $package) {
                    try {
                        list($message, $messages) = $this->sendPackage($package, $messages);
                    } catch (\Exception $e) {
                        \Log::error('błąd przy nadawaniu hurtowym paczki', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
                    }
                }
            }
            if (!empty($message)) {
                $alert = 'error';
            } else {
                $message = __('order_packages.message.courier_success');
                $alert = 'success';
            }
            return redirect()->back()->with([
                'message' => $message,
                'alert-type' => $alert
            ]);
        } else {
            Log::notice(
                'Wrong courier',
                ['courier' => $courierName, 'class' => get_class($this), 'line' => __LINE__]
            );

            return redirect()->back()->with([
                'message' => __('order_packages.message.courier_error'),
                'alert-type' => 'error'
            ]);

        }
    }

    public function preparePackageToSend($orderId, $packageId)
    {
        $order = Order::find($orderId);
        if (empty($order)) {
            abort(404);
        }
        $package = $this->repository->find($packageId);
        if (empty($package)) {
            abort(404);
        }
        $deliveryAddress = $order->addresses->where('type', '=', 'DELIVERY_ADDRESS');
        $deliveryAddress = $deliveryAddress->first();
        if (empty($deliveryAddress)) {
            abort(404);
        }
        if ($order->sello_id) {
            $transaction = SelTransaction::find($order->sello_id);
            $addressAllegro = SelAddress::where('adr_TransId', $order->sello_id)->where('adr_Type', 1)->first();
            $order->allegro_transaction_id = $transaction->tr_CheckoutFormId;
            $order->save();
        }
        $data = [
            'warehouse' => $order->warehouse->symbol,
            'order_id' => $order->id,
            'symbol' => $package->symbol,
            'courier_type' => $package->delivery_courier_name,
            'courier_name' => $package->service_courier_name,
            'weight' => $package->weight,
            'length' => $package->size_a,
            'width' => $package->size_b,
            'height' => $package->size_c,
            'notices' => $package->notices !== null ? $package->notices : 'Brak',
            'cash_on_delivery' => $package->cash_on_delivery !== null,
            'number_account_for_cash_on_delivery' => $package->cash_on_delivery !== null ? env('ACCOUNT_NUMBER') : null,
            'bank_name' => $package->cash_on_delivery !== null ? env('BANK_NAME') : null,
            'price_for_cash_on_delivery' => ($package->cash_on_delivery !== null) ? ($package->cash_on_delivery === 0 ? null : $package->cash_on_delivery) : null,
            'amount' => 1000,
            'content' => $package->content,
            'additional_data' => [
                'order_package_id' => $package->id,
                'forwarding_delivery' => $package->delivery_courier_name,
                'allegro_user_id' => $transaction->tr_RegId ?? null,
                'allegro_transaction_id' => $order->allegro_transaction_id,
                'package_type' => $package->container_type,
                'packing_type' => $package->packing_type,
                'allegro_mail' => $addressAllegro->adr_Email ?? $deliveryAddress->email,
            ],
            'delivery_address' => [
                'firstname' => $deliveryAddress->firstname,
                'lastname' => $deliveryAddress->lastname,
                'address' => $deliveryAddress->address,
                'flat_number' => $deliveryAddress->flat_number,
                'city' => $deliveryAddress->city,
                'email' => $deliveryAddress->email,
                'phone' => $deliveryAddress->phone,
                'firmname' => $deliveryAddress->firmname,
                'nip' => $deliveryAddress->nip,
                'postal_code' => $deliveryAddress->postal_code,
                'country' => $deliveryAddress->country !== null ? $deliveryAddress->country->name : 'Polska',
                'delivery_date' => $package->delivery_date !== null ? $package->delivery_date : null,
            ],
        ];

        $shipmentDate = Carbon::parse($package->shipment_date);
        if ($order->warehouse_id !== null) {
            $pickupAddress = [
                'pickup_address' => [
                    'firstname' => $order->warehouse->property->firstname,
                    'lastname' => $order->warehouse->property->lastname,
                    'address' => $order->warehouse->address->address,
                    'flat_number' => $order->warehouse->address->warehouse_number,
                    'city' => $order->warehouse->address->city,
                    'email' => $order->warehouse->firm->email,
                    'phone' => $order->warehouse->property->phone,
                    'firmname' => $order->warehouse->firm->name,
                    'nip' => $order->warehouse->firm->nip,
                    'postal_code' => $order->warehouse->address->postal_code,
                    'country' => 'Polska',
                ],
            ];

            if ($package->shipment_date !== null) {
                $pickupAddress['pickup_address']['parcel_date'] = $shipmentDate->toDateString();
            } else {
                $pickupAddress['pickup_address']['parcel_date'] = null;
            }

            $data = array_merge($data, $pickupAddress);
        }
        $validator = $this->validatePackage($data);
        if ($data['price_for_cash_on_delivery'] == '0.00') {
            $data['cash_on_delivery'] = false;
            unset($data['price_for_cash_on_delivery']);
        }
        if ($validator === true) {
            dispatch_now(new OrdersCourierJobs($data));
            $message = ['status' => 200, 'message' => null];
        } else {
            $message = ['status' => 200, 'message' => $validator->getData()];
        }
        return new JsonResponse($message, 200);
    }

    protected function validatePackage($data)
    {
        $validator = Validator::make($data, [
            'courier_name' => 'required|min:3|in:INPOST,APACZKA,DPD,POCZTEX,JAS,ALLEGRO-INPOST,GLS',
            'courier_type' => 'nullable',
            'weight' => 'required|numeric',
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'cash_on_delivery' => 'nullable',
            'content' => 'required|min:3|string',
            'amount' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'notices' => 'nullable',
            'number_account_for_cash_on_delivery' => 'nullable|string',
            'price_for_cash_on_delivery' => 'nullable|string|regex:/^\d*(\.\d{2})?$/',
            'additional_data.order_package_id' => 'integer',
            'delivery_address.firstname' => 'required|string',
            'delivery_address.lastname' => 'required|string',
            'delivery_address.address' => 'required|string',
            'delivery_address.flat_number' => 'required|string',
            'delivery_address.city' => 'required|string',
            'delivery_address.email' => 'nullable|email',
            'delivery_address.phone' => 'required',
            'delivery_address.firmname' => 'nullable|string',
            'delivery_address.nip' => 'nullable|numeric',
            'delivery_address.postal_code' => 'required|string',
            'delivery_address.country' => 'required|string',
            'pickup_address.firstname' => 'required|string',
            'pickup_address.lastname' => 'required|string',
            'pickup_address.address' => 'required|string',
            'pickup_address.flat_number' => 'required|string',
            'pickup_address.city' => 'required|string',
            'pickup_address.email' => 'required|email',
            'pickup_address.phone' => 'required|numeric',
            'pickup_address.firmname' => 'nullable|string',
            'pickup_address.nip' => 'nullable|numeric',
            'pickup_address.postal_code' => 'required|string',
            'pickup_address.country' => 'required|string',
            'pickup_address.parcel_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            $message = ['status' => 422, 'message' => $validator->errors()->all()];
            return response()->json($message);
        }
        return true;
    }

    public function duplicate(Request $request, $packageId)
    {
        try {
            $template = PackageTemplate::findOrFail($request->templateList);
            $package = OrderPackage::findOrFail($packageId);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => __('order_packages.message.package_error'),
                'alert-type' => 'error'
            ]);
        }

        $data = $template->attributesToArray();
        $data['size_a'] = $template->sizeA;
        $data['size_b'] = $template->sizeB;
        $data['size_c'] = $template->sizeC;
        $data['order_id'] = $package->order_id;
        $data['shipment_date'] = $package->shipment_date;
        $data['sending_number'] = null;
        $data['letter_number'] = null;
        $data['delivery_date'] = $package->delivery_date;
        $data['quantity'] = 1;
        $data['chosen_data_template'] = $template->id;
        $packageNumber = OrderPackage::where('order_id', $package->order_id)->max('number') + 1;
        $data['notices'] = $data['order_id'] . '/' . $packageNumber;
        $data['cash_on_delivery'] = $package->cash_on_delivery;
        $data['status'] = PackageTemplate::STATUS_NEW;
        $data['cost_for_client'] = $package->cost_for_client;
        $data['cost_for_company'] = $package->cost_for_company;
        $data['content'] = $package->content;
        $data['packing_type'] = $package->packing_type;
        $data['number'] = $packageNumber;
        $newPackage = $this->saveOrderPackage($data);

        $toCancel = [
            PackageTemplate::WAITING_FOR_SENDING,
            PackageTemplate::SENDING
        ];

        $prods = $package->packedProducts;
        $products = $prods->reduce(function ($prev, $next) {
            $prev[$next->pivot->product_id] = ['quantity' => $next->pivot->quantity];
            return $prev;
        }, []);

        $newPackage->packedProducts()->sync($products);
        $package->packedProducts()->sync([]);
        $newPackage->save();

        if (in_array($package->status, $toCancel)) {
            dispatch_now(new SendRequestForCancelledPackageJob($packageId));
            $package->status = PackageTemplate::WAITING_FOR_CANCELLED;
            $package->save();
        } else if ($package->status == PackageTemplate::STATUS_NEW) {
            $package->delete();
        }
        return redirect()->back()->with([
            'message' => __('order_packages.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $package
     * @param array $messages
     * @return array
     */
    public function sendPackage($package, array $messages): array
    {
        $result = $this->preparePackageToSend($package->order->id, $package->id);
        $resArr = $result->getData();
        $itemMessage = 'ID Zamówienia ' . $package->order->id . ' | Numer paczki: ' . $package->id;
        if ($resArr->message != null) {
            foreach ($resArr->message as $msg) {
                $itemMessage .= ' ' . $msg;
            }
            $message = [
                'message' => $itemMessage
            ];
            array_push($messages, $message);
        }
        return array($message, $messages);
    }

    /**
     * @param $courier_name
     * @return mixed
     */
    protected function getPackagesToSent($courier_name)
    {
        return OrderPackage::where('delivery_courier_name', 'like', $courier_name)
            ->whereNotNull('letter_number')
            ->whereNotIn('status', [
                PackageTemplate::WAITING_FOR_CANCELLED,
                PackageTemplate::SENDING,
                PackageTemplate::DELIVERED,
                PackageTemplate::CANCELLED])
            ->whereHas('order', function ($query) {
                $query->where('status_id', '<>', Order::STATUS_WITHOUT_REALIZATION);
            })
            ->get();
    }

    /**
     * @param $package
     * @throws FileNotFoundException
     */
    protected function getStickerForGls($package)
    {
        try {
            $file = Storage::disk('private')->get('labels/gls/' . $package->sending_number . '.pdf');
        } catch (FileNotFoundException $e) {
            $gls = new GLSClient();
            $gls->auth();
            $gls->getLetterForPackage($package->sending_number);
            $number = $gls->getPackageNumer($package->sending_number);
            $package->letter_number = $number;
            $package->save();
            $gls->logout();
            ConfirmPackages::create(['package_id' => $package->id]);
            $file = Storage::disk('private')->get('labels/gls/' . $package->sending_number . '.pdf');
        }
        return $file;
    }

    public function changePackageCost(OrderPackageCostsUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $packageTemplate = $this->packageTemplateRepository->find($validated['templateList']);

        $this->repository->update([
            'cost_for_client' => $validated['cost_for_client'],
            'cost_for_company' => $validated['cost_for_company'],
            'chosen_data_template' => $packageTemplate->name,
            'symbol' => $packageTemplate->symbol,
        ], $validated['changePackageCostId']);

        return redirect()->back();
    }

    public function closeDay(Request $request)
    {
        $today = new Carbon();
        $courierName = $request->get('courier_name');
        $query = OrderPackage::query()
            ->whereNotIn('status',
                [
                    PackageTemplate::WAITING_FOR_CANCELLED,
                    PackageTemplate::SENDING,
                    PackageTemplate::DELIVERED,
                    PackageTemplate::CANCELLED
                ]
            )
            ->whereNull('letter_number')
            ->whereDate('shipment_date', '<=', $today);
        if ($courierName !== 'all') {
            $query->where('delivery_courier_name', 'like', $courierName);
        }
        $packages = $query->get();

        try {
            foreach ($packages as $package) {
                $package->update(
                    [
                        'shipment_date' => $today->copy()->addWeekday(),
                        'delivery_date' => (empty($package->delivery_date)) ? null : $today->copy()->addWeekday(2)
                    ]
                );
            }

            $pdfFilename = 'day-close-protocol-' . $courierName . '-' . Carbon::today()->toDateString() . '.pdf';

            $pdf = PDF::loadView('pdf.close-day-protocol', [
                'packages' => $packages,
                'date' => $today,
                'courierName' => strtoupper($courierName),
                'mode' => 'utf-8'
            ])->setPaper('a4', 'landscape');
            if (!file_exists(storage_path('app/public/protocols'))) {
                mkdir(storage_path('app/public/protocols'));
            }
            $path = storage_path('app/public/protocols/' . $pdfFilename);
            $pdf->save($path);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => __('order_packages.message.close_day_protocol_error'),
                'alert-type' => 'error'
            ]);
        }
        return $pdf->download($pdfFilename);
    }
}

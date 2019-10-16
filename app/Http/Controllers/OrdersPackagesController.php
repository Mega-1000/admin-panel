<?php

namespace App\Http\Controllers;

use App\Helpers\OrderPackagesDataHelper;
use App\Http\Requests\OrderPackageCreateRequest;
use App\Http\Requests\OrderPackageUpdateRequest;
use App\Jobs\AddLabelJob;
use App\Jobs\SendRequestForCancelledPackageJob;
use App\Repositories\FirmRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mail\SendDailyProtocolToDeliveryFirmMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Jobs\OrdersCourierJobs;

/**
 * Class OrderTasksController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersPackagesController extends Controller
{
    /**
     * @var OrderPackageRepository
     */
    protected $repository;

    /** @var OrderRepository */
    protected $orderRepository;

    /** @var OrderPackagesDataHelper */
    protected $orderPackagesDataHelper;

    /**
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     * OrdersPackagesController constructor.
     * @param OrderPackageRepository $repository
     * @param OrderRepository $orderRepository
     * @param OrderPackagesDataHelper $orderPackagesDataHelper
     */
    public function __construct(
        OrderPackageRepository $repository,
        OrderRepository $orderRepository,
        OrderPackagesDataHelper $orderPackagesDataHelper,
        FirmRepository $firmRepository
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->orderPackagesDataHelper = $orderPackagesDataHelper;
        $this->firmRepository = $firmRepository;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        $templateData = $this->orderPackagesDataHelper->getData();
        $order = $this->orderRepository->find($id);
        $shipmentDate = $this->orderPackagesDataHelper->calculateShipmentDate();
        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        if($order->master_order_id != null) {
            $mainId = $order->master_order_id;
            $order = $this->orderRepository->find($mainId);
            $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        }
        $promisedPayments = [];
        $payments = [];


        $cashOnDeliverySum = 0;

        foreach($order->packages()->where('letter_number', '=', 'null')->get() as $package) {
            $cashOnDeliverySum += $package->cash_on_delivery;
        }

        $allOrdersSum = 0;
        $isAdditionalDKPExists = false;
        $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
        foreach($connectedOrders as $connectedOrder)
        {
            if($connectedOrder->additional_cash_on_delivery_cost == 50) {
                $isAdditionalDKPExists = true;
            }
            $allOrdersSum += $connectedOrder->getSumOfGrossValues();
        }
        $allOrdersSum += $order->getSumOfGrossValues();
        if($order->additional_cash_on_delivery_cost == 50) {
            $isAdditionalDKPExists = true;
        }
        foreach($connectedOrders as $connectedOrder) {
            foreach($connectedOrder->packages()->where('status', '!=', 'CANCELLED')->get() as $package) {
                $cashOnDeliverySum += $package->cash_on_delivery;
            }
        }

        foreach($order->payments as $payment){
            if($payment->promise == '') {
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
            'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
            'customer_notices' => $order->customer_notices,
            'shipment_price_for_client' => $order->shipment_price_for_client,
            'shipment_price_for_us' => $order->shipment_price_for_us,
            'weight' => $order->weight,
        ];
        return view('orderPackages.create', compact('id', 'templateData', 'orderData', 'order', 'payments', 'promisedPayments', 'connectedOrders', 'cashOnDeliverySum', 'isAdditionalDKPExists', 'allOrdersSum'));
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
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderPackage = $this->repository->find($id);

        return view('orderPackages.edit', compact('orderPackage', 'id'));
    }

    /**
     * @param OrderPackageUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OrderPackageUpdateRequest $request, $id)
    {
        $orderPackage = $this->repository->find($id);

        if (empty($orderPackage)) {
            abort(404);
        }

        $orderId = $orderPackage->order_id;
        $data = $request->validated();

        $data['delivery_date'] = new \DateTime($data['delivery_date']);
        $data['shipment_date'] = new \DateTime($data['shipment_date']);


        $this->repository->update($data, $id);

        return redirect()->route('orders.edit', ['order_id' => $orderId])->with([
            'message' => __('order_packages.message.update'),
            'alert-type' => 'success'
        ]);
    }

    public function store(OrderPackageCreateRequest $request)
    {
        $order_id = $request->input('order_id');
        $data = $request->validated();
        $toCheck = (float)$request->input('toCheck');

        $data['delivery_date'] = new \DateTime($data['delivery_date']);
        $data['shipment_date'] = new \DateTime($data['shipment_date']);
        $packagesNumber = 0;
        $package = $this->repository->orderBy("created_at", "desc")->findWhere(["order_id" => $order_id],
            ["number"])->first();

        if (!empty($package)) {
            $packagesNumber = $package->number;
        }

        $data['number'] = $packagesNumber + 1;
        $notices = $data['notices'];
        $data['notices'] = $data['order_id'] . '/' . $data['number'] . ' ' . $notices;
        if ($data['delivery_courier_name'] === 'GIELDA' || $data['delivery_courier_name'] === 'ODBIOR_OSOBISTY') {
            $data = $this->generateSticker($data);
            $data['status'] = 'WAITING_FOR_SENDING';
        }

        $order = $this->orderRepository->find($order_id);
        if(empty($package)) {
            $isAdditionalDKPExists = false;
            $connectedOrders = $this->orderRepository->findWhere(['master_order_id' => $order->id]);
            foreach($connectedOrders as $connectedOrder)
            {
                if($connectedOrder->additional_cash_on_delivery_cost == 50) {
                    $isAdditionalDKPExists = true;
                }
            }
            if($order->toPay() > 5 && $isAdditionalDKPExists == false) {
                $this->orderRepository->update([
                    'additional_cash_on_delivery_cost' => $order->additional_cash_on_delivery_cost + 50,
                ], $order->id);
                $data['cash_on_delivery'] = $data['cash_on_delivery'] + 50;
            }
        }



        $this->repository->create($data);
        if($toCheck != 0) {
            dispatch_now(new AddLabelJob($order->id, [134]));
        } else {
            dispatch_now(new AddLabelJob($order->id, [133]));
        }
        return redirect()->route('orders.edit', ['order_id' => $order_id])->with([
            'message' => __('order_packages.message.store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('orders.message.not_delete'),
                'alert-type' => 'error'
            ])->withInput(['tab' => 'orderPackages']);
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


    /**
     * @return mixed
     */
    public function prepareCollection($id)
    {
        $collection = $this->repository->findByField('order_id', $id);

        return $collection;
    }

    public function preparePackageToSend($orderId, $packageId)
    {
        $order = $this->orderRepository->find($orderId);
        if (empty($order)) {
            abort(404);
        }
        $package = $this->repository->find($packageId);
        if (empty($package)) {
            abort(404);
        }
        $deliveryAddress = $order->addresses->where('type', '=', 'DELIVERY_ADDRESS');
        $deliveryAddress = $deliveryAddress->first->id;
        if (empty($deliveryAddress)) {
            abort(404);
        }
        $data = [
            'order_id' => $order->id,
            'courier_type' => $package->delivery_courier_name,
            'courier_name' => $package->service_courier_name,
            'weight' => $package->weight,
            'length' => $package->size_a,
            'width' => $package->size_b,
            'height' => $package->size_c,
            'notices' => $package->notices !== null ? $package->notices : 'Brak',
            'cash_on_delivery' => $package->cash_on_delivery !== null ? true : false,
            'number_account_for_cash_on_delivery' => $package->cash_on_delivery !== null ? env('ACCOUNT_NUMBER') : null,
            'bank_name' => $package->cash_on_delivery !== null ? env('BANK_NAME') : null,
            'price_for_cash_on_delivery' => $package->cash_on_delivery !== null ? $package->cash_on_delivery === 0 ? null : $package->cash_on_delivery : null,
            'amount' => 1000,
            'content' => $package->content,
            'additional_data' => [
                'order_package_id' => $package->id,
                'forwarding_delivery' => $package->service_courier_name,
                'allegro_id' => $order->customer->nick_allegro,
                'allegro_transaction_id' => $order->allegro_transaction_id,
                'package_type' => $package->container_type
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
                'country' => $deliveryAddress->country !== null ? $deliveryAddress->country : 'Polska',
                'delivery_date' => $package->delivery_date !== null ? $package->delivery_date : null,
            ],
        ];


        if ($order->warehouse_id !== null) {
            $pickupAddress = [
                'pickup_address' => [
                    'firstname' => $order->warehouse->property->firstname !== null ? $order->warehouse->property->firstname : null,
                    'lastname' => $order->warehouse->property->lastname !== null ? $order->warehouse->property->lastname : null,
                    'address' => $order->warehouse->address->address !== null ? $order->warehouse->address->address : null,
                    'flat_number' => $order->warehouse->address->warehouse_number !== null ? $order->warehouse->address->warehouse_number : null,
                    'city' => $order->warehouse->address->city !== null ? $order->warehouse->address->city : null,
                    'email' => $order->warehouse->firm->email !== null ? $order->warehouse->firm->email : null,
                    'phone' => $order->warehouse->property->phone !== null ? $order->warehouse->property->phone : null,
                    'firmname' => $order->warehouse->firm->name !== null ? $order->warehouse->firm->name : null,
                    'nip' => $order->warehouse->firm->nip !== null ? $order->warehouse->firm->nip : null,
                    'postal_code' => $order->warehouse->address->postal_code !== null ? $order->warehouse->address->postal_code : null,
                    'country' => 'Polska',
                    'parcel_date' => $package->shipment_date !== null ? $package->shipment_date : null,
                ],
            ];
            $data = array_merge($data, $pickupAddress);

        }
        $validator = $this->validatePackage($data);
        if($data['price_for_cash_on_delivery'] == '0.00') {
            $data['cash_on_delivery'] = false;
            unset($data['price_for_cash_on_delivery']);
        }
        if($validator === true) {
            dispatch_now(new OrdersCourierJobs($data));
            $message = ['status' => 200, 'message' => null];
        } else {
            $message = ['status' => 200, 'message' => $validator->getData()];
        }
        return new JsonResponse($message, 200);
    }

    public function sendRequestForCancelled($id)
    {
        dispatch_now(new SendRequestForCancelledPackageJob($id));

        $this->repository->update(['status' => 'WAITING_FOR_CANCELLED'], $id);

        return redirect()->back()->with([
            'message' => __('order_packages.message.request_for_cancelled_package'),
            'alert-type' => 'success'
        ]);
    }

    public function getProtocols($courierName)
    {
        if ($courierName !== 'all') {
            $packages = $this->repository->findWhere([
                ['delivery_courier_name', '=', $courierName],
                ['shipment_date', '=', Carbon::today()],
                ['status', '!=', 'CANCELLED'],
                ['status', '!=', 'WAITING_FOR_CANCELLED'],
                ['status', '!=', 'REJECT_CANCELLED'],
            ]);
        } else {
            $courierName = 'wszystkie';
            $packages = $this->repository->findWhere([
                ['shipment_date', '=', Carbon::today()],
                ['status', '!=', 'CANCELLED'],
                ['status', '!=', 'WAITING_FOR_CANCELLED'],
                ['status', '!=', 'REJECT_CANCELLED'],
            ]);
        }

        if (!$packages->isEmpty()) {
            $packagesArray = [];
            foreach ($packages as $package) {
                if($package->order->warehouse !== null){
                    if($package->order->warehouse->symbol !== 'MEGA-OLAWA') {
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
                    'phone' => $package->order->addresses->first->id->phone,
                    'postal_code' => $package->order->addresses->first->id->postal_code,
                    'city' => $package->order->addresses->first->id->city,
                ];
                array_push($packagesArray, $packagesArr);
            }
            $pdf = PDF::loadView('pdf.protocol', [
                'packages' => $packagesArray,
                'date' => Carbon::today()->toDateString(),
                'courierName' => strtoupper($courierName)
            ])->setPaper('a4', 'landscape');
            if (!file_exists(storage_path('app/public/protocols'))) {
                mkdir(storage_path('app/public/protocols'));
            }
            $path = storage_path('app/public/protocols/protocol-' . $courierName . '-' . Carbon::today()->toDateString() . '.pdf');
            $pdf->save($path);
            $this->sendProtocolToDeliveryFirm(strtoupper($courierName), $path);
            return $pdf->download('protocol-' . $courierName . '-' . Carbon::today()->toDateString() . '.pdf');
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

    public function prepareGroupPackageToSend($courierName)
    {
        ini_set('max_execution_time', 600);
        if ($courierName == 'APACZKA' || $courierName == 'INPOST' || $courierName == 'DPD' || $courierName == 'POCZTEX' || $courierName == 'JAS' || $courierName == 'ALL') {
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
                }
            }
            if (!empty($message)) {
                $alert = 'error';
            } else {
                $message = __('order_packages.message.courier_success');
                $alert = 'success';
            }
            return redirect()->back()->with([
                'message' => __('order_packages.message.courier_success'),
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

    public function generateSticker($data)
    {
        $order = $this->orderRepository->find($data['order_id']);
        $data['letter_number'] = $data['order_id'] . rand(1000000, 9999999);
        $data['sending_number'] = $data['order_id'] . rand(1000000, 9999999);
        $data['shipment_date'] = $data['shipment_date']->format('Y-m-d');
        $data['delivery_date'] = $data['delivery_date']->format('Y-m-d');
        $pdf = PDF::loadView('pdf.sticker', [
            'order' => $order,
            'package' => $data
        ])->setPaper('a5');

        if (!file_exists(storage_path('app/public/' . strtolower($data['delivery_courier_name']) . '/stickers/'))) {
            mkdir(storage_path('app/public/' . strtolower($data['delivery_courier_name'])));
            mkdir(storage_path('app/public/' . strtolower($data['delivery_courier_name']) . '/stickers/'));
        }
        $path = storage_path('app/public/' . strtolower($data['delivery_courier_name']) . '/stickers/sticker'. $data['letter_number'] . '.pdf');
        $pdf->save($path);

        return $data;
    }

    protected function validatePackage($data){
        $validator = Validator::make($data, [
            'courier_name' => 'required|min:3|in:INPOST,APACZKA,DPD,POCZTEX,JAS',
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
            'delivery_address.phone' => 'required|numeric',
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
}

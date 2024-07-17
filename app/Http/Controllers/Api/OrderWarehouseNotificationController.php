<?php

namespace App\Http\Controllers\Api;

use App\Entities\Order;
use App\Entities\OrderWarehouseNotification;
use App\Entities\Warehouse;
use App\Entities\WorkingEvents;
use App\Enums\OrderPaymentLogTypeEnum;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderLabelHelper;
use App\Helpers\PriceHelper;
use App\Helpers\RecalculateBuyingLabels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderWarehouseNotification\AcceptShipmentRequest;
use App\Http\Requests\Api\OrderWarehouseNotification\DenyShipmentRequest;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\OrderStatusChangedToDispatchNotificationJob;
use App\Repositories\OrderInvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarehouseNotificationRepository;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLogService;
use App\Services\OrderPaymentService;
use App\Services\WorkingEventsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderWarehouseNotificationController extends Controller
{
    use ApiResponsesTrait;

    /**
     * OrderWarehouseNotificationController constructor.
     * @param OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository
     * @param OrderRepository $orderRepository
     * @param OrderInvoiceRepository $orderInvoiceRepository
     */
    public function __construct(
        protected readonly OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository,
        protected readonly OrderRepository                      $orderRepository,
        protected readonly OrderInvoiceRepository               $orderInvoiceRepository
    ) {}

    public function getNotification(int $notificationId): ?OrderWarehouseNotification
    {
        return OrderWarehouseNotification::find($notificationId);
    }

    public function deny(DenyShipmentRequest $request, int $notificationId, MessagesHelper $messagesHelper): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;
            $notification = $this->orderWarehouseNotificationRepository->update($data, $notificationId);

            $messagesHelper->sendAvisationDeny($notification->order->chat, $request->get('customer_notices'));

            /** @var Order $order */
            $order = Order::query()->findOrFail($data['order_id']);
            dispatch(new DispatchLabelEventByNameJob($order, "warehouse-notification-denied"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with deny.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    /**
     * @param $data
     * @throws ChatException
     */
    private function sendMessage($data): void
    {
        $warehouse = Warehouse::findOrFail($data['warehouse_id']);
        $role_id = config('employees_roles')['zamawianie-towaru'];

        $employees = $warehouse->firm->employees->filter(function ($employee) use ($role_id) {
            return $employee->employeeRoles->find($role_id);
        });
        if (empty($employees)) {
            $employees = $warehouse->firm->employees;
        }
        Log::notice('twoja stara 1' . $employees->toArray(), $warehouse->toArray());

        $helper = new MessagesHelper();
        $helper->orderId = $data['order_id'];
        $helper->currentUserId = $employees->first()->id;
        $helper->currentUserType = MessagesHelper::TYPE_EMPLOYEE;
        $helper->createNewChat();
        $helper->addMessage($data['customer_notices']);
        OrderLabelHelper::setYellowLabel($helper->getChat());
    }

    public function accept(AcceptShipmentRequest $request, $notificationId, MessagesHelper $messagesHelper): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;

            $data['realization_date'] = $data['realization_date_from'];
            $data['possible_delay_days'] = Carbon::parse($data['realization_date_from'])->diffInDays(Carbon::parse($data['realization_date_to']));
            $notification = $this->orderWarehouseNotificationRepository->update($data, $notificationId);

            if (!empty($data['customer_notices'])) {
                $this->sendMessage($data);
            }

            $notification->order->shipment_date = $notification->realization_date;
            $notification->order->shipment_start_days_variation = $notification->possible_delay_days;
            $notification->order->save();
            $notification->order->dates->update([
                'warehouse_shipment_date_from' => Carbon::parse($data['realization_date_from']),
                'warehouse_shipment_date_to' => Carbon::parse($data['realization_date_to']),
                'warehouse_acceptance' => true,
                'message' => 'Magazyn <strong>wprowadził</strong> daty dotyczące przesyłki.'
            ]);
            /** @var Order $order */
            $order = Order::findOrFail($data['order_id']);
            dispatch(new DispatchLabelEventByNameJob($order, "warehouse-notification-accepted"));

            $messagesHelper->sendAvizationAcceptation($order->chat);

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with create new order.',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]
            );
            die();
        }
    }

    public function sendInvoice(Request $request): JsonResponse
    {
        try {
            $order = Order::findOrFail($request->orderId);

            $file = $request->file('file');
            if ($file !== null) {
                $filename = $file?->getClientOriginalName();

                Storage::disk('local')->put('public/invoices/' . $filename, file_get_contents($file));
                $order->invoices()->create([
                    'invoice_type' => 'buy',
                    'invoice_name' => $filename,
                    'is_visible_for_client' => (boolean)$request->isVisibleForClient,
                ]);

                RecalculateBuyingLabels::recalculate($order);


                $orders = Order::whereHas('invoices')->where('id', '>', '20000')->get()->count();

                $invoiceRequest = $order->invoiceRequests()->first();

                if (!empty($invoiceRequest) && $invoiceRequest->status === 'MISSING') {
                    $invoiceRequest->update(['status' => 'SENT']);
                }

                dispatch(new DispatchLabelEventByNameJob($order, "new-file-added-to-order"));

                return $this->okResponse();
            }
            Log::error('Problem with send invoice.',
                ['exception' => 'No file in request', 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        } catch (Exception $e) {
            Log::error('Problem with send invoice.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function changeStatus(Request $request): JsonResponse
    {
        try {
            $orderId = $request->orderId;
            $order = Order::findOrFail($orderId);
//            $packages = $order->packages()->where('statsu', 'NEW')->get();
//
//            foreach ($packages as $package) {
//                $package->update(['status' => 'SENDING']);
//            }
            $order->labels()->detach([244, 245, 74, 243, 256, 270]);

            dispatch(new DispatchLabelEventByNameJob($order, "all-shipments-went-out"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with change order status.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function createAvisation(Order $order): View
    {
        return view('create-avisation-fast', compact('order'));
    }

    public function storeAvisation(Order $order, Request $request): RedirectResponse
    {
        $shipmentDateTo = Carbon::create($order->dates->warehouse_shipment_date_to ?? $order->dates->customer_shipment_date_to);

        if ($shipmentDateTo->isPast()) {
            $order->dates()->update([
                'customer_shipment_date_from' => Carbon::now(),
                'customer_shipment_date_to' => Carbon::now()->addBusinessDays(7),
            ]);

            $shipmentDateTo = Carbon::now()->addBusinessDays(7);
        }

        WorkingEventsService::createEvent(WorkingEvents::ORDER_PAYMENT_STORE_EVENT, $order->id);
        $type = $request->input('payment-type');
        $promiseDate = Carbon::create($request->get('declared_date'));
        $payer = $order->customer->login;


        if (
            $order->getValue() > ($order->payments()->sum('amount') + $order->payments()->sum('declared_sum'))
        ) {
            if ($order->payments->sum('declared_sum') !== 0 && !empty($request->input('declared_sum', '0'))) {
                $orderPayment = app(OrderPaymentService::class)->payOrder($order->id, $request->input('declared_sum', '0'), $payer,
                    null, true,
                    false, $promiseDate,
                    $type, false,
                );
                $orderPayment->deletable = true;
                $orderPayment->save();

                $orderPaymentAmount = (float)PriceHelper::modifyPriceToValidFormat($request->input('declared_sum'));
                $orderPaymentsSum = $orderPayment->order->payments->sum('declared_sum') - $orderPaymentAmount;

                app(OrderPaymentLogService::class)->create(
                    $order->id,
                    $orderPayment->id,
                    $orderPayment->order->customer_id,
                    $orderPaymentsSum,
                    $orderPaymentAmount,
                    $request->input('created_at') ?: Carbon::now(),
                    $request->input('notices') ?: '',
                    $request->input('declared_sum', '0') ?? '0',
                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                    true,
                );
            }

            if ($order->getValue() > ($order->payments()->sum('amount') + $order->payments()->sum('declared_sum'))) {
                WorkingEventsService::createEvent(WorkingEvents::ORDER_PAYMENT_STORE_EVENT, $order->id);
                $type = $request->input('payment-type');
                $promiseDate = Carbon::create($shipmentDateTo);

                $orderPayment = app(OrderPaymentService::class)->payOrder(
                    $order->id, $order->getValue() - ($order->payments()->sum('amount') + $order->payments()->sum('declared_sum')),
                    $payer,
                    null, true,
                    false, $promiseDate,
                    $type, false,
                );
                $orderPayment->deletable = true;
                $orderPayment->save();

                $orderPaymentAmount = (float)PriceHelper::modifyPriceToValidFormat($request->input('declared_sum'));
                $orderPaymentsSum = $orderPayment->order->payments->sum('declared_sum') - $orderPaymentAmount;

                app(OrderPaymentLogService::class)->create(
                    $order->id,
                    $orderPayment->id,
                    $orderPayment->order->customer_id,
                    $orderPaymentsSum,
                    $orderPaymentAmount,
                    $request->input('created_at') ?: Carbon::now(),
                    $request->input('notices') ?: '',
                    $request->input('declared_sum', '0') ?? '0',
                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                    true,
                );
            }
        }


        $order->warehouse_id = Warehouse::where('symbol', $request->input('warehouse-symbol'))->first()->id;
        $order->save();

//        OrderWarehouseNotification::create([
//            'order_id' => $order->id,
//            'warehouse_id' => $order->warehouse_id,
//            'employee_id' => $request->get('employee'),
//            'waiting_for_response' => true,
//        ]);

//        $prev = [];
//        AddLabelService::addLabels($order, [52], $prev, [], Auth::user()->id);
//        AddLabelService::addLabels($order, [73], $prev, [], Auth::user()->id);
//
//        $order->labels()->detach([44, 224, 68]);

        return redirect()->route('orders.index');
    }

    protected function shouldNotifyWithEmail($orderWarehouseNotification, $now): bool
    {
        return $orderWarehouseNotification->created_at->diff($now)->h >= 2; //only schedules that wait longer then 2h
    }

    protected function canNotifyNow($now): bool
    {
        if (!($now->dayOfWeek == 6 || $now->dayOfWeek == 0)) {  //not Saturday nor Sunday
            if ($now->hour >= 7 && $now->hour < 17) {           //only between 9AM and 5PM
                return true;
            }
        }

        return false;
    }
}

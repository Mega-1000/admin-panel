<?php

namespace App\Http\Controllers\Api;

use App\Entities\Order;
use App\Entities\OrderWarehouseNotification;
use App\Entities\Warehouse;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderLabelHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderWarehouseNotification\AcceptShipmentRequest;
use App\Http\Requests\Api\OrderWarehouseNotification\DenyShipmentRequest;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Repositories\OrderInvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarehouseNotificationRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function getNotification(int $notificationId): OrderWarehouseNotification
    {
        return OrderWarehouseNotification::find($notificationId);
    }

    public function deny(DenyShipmentRequest $request, int $notificationId): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;
            $notification = $this->orderWarehouseNotificationRepository->update($data, $notificationId);

            $this->sendMessage($data, $notification);
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

        $helper = new MessagesHelper();
        $helper->orderId = $data['order_id'];
        $helper->currentUserId = $employees->first()->id;
        $helper->currentUserType = MessagesHelper::TYPE_EMPLOYEE;
        $helper->createNewChat();
        $helper->addMessage($data['customer_notices']);
        OrderLabelHelper::setYellowLabel($helper->getChat());
    }

    public function accept(AcceptShipmentRequest $request, $notificationId): JsonResponse
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
            $order = Order::query()->findOrFail($data['order_id']);
            dispatch(new DispatchLabelEventByNameJob($order, "warehouse-notification-accepted"));

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
            $packages = $order->packages()->where('statsu', 'NEW')->get();

            foreach ($packages as $package) {
                $package->update(['status' => 'SENDING']);
            }

            dispatch(new DispatchLabelEventByNameJob($order, "all-shipments-went-out"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with change order status.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }
}

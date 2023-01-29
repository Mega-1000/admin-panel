<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderWarehouseNotificationController extends Controller
{
    use ApiResponsesTrait;

    /** @var OrderWarehouseNotificationRepository */
    protected $orderWarehouseNotificationRepository;

    protected $orderRepository;

    protected $orderInvoiceRepository;

    /**
     * OrderWarehouseNotificationController constructor.
     * @param OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository
     */
    public function __construct(
        OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository,
        OrderRepository                      $orderRepository,
        OrderInvoiceRepository               $orderInvoiceRepository
    )
    {
        $this->orderWarehouseNotificationRepository = $orderWarehouseNotificationRepository;
        $this->orderRepository = $orderRepository;
        $this->orderInvoiceRepository = $orderInvoiceRepository;
    }

    public function getNotification($notificationId)
    {
        return $this->orderWarehouseNotificationRepository->find($notificationId);
    }

    public function deny(DenyShipmentRequest $request, $notificationId)
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;
            $notification = $this->orderWarehouseNotificationRepository->update($data, $notificationId);

            $this->sendMessage($data, $notification);

            dispatch_now(new DispatchLabelEventByNameJob($data['order_id'], "warehouse-notification-denied"));

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
     * @param $notification
     * @throws ChatException
     */
    private function sendMessage($data, $notification): void
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

    public function accept(AcceptShipmentRequest $request, $notificationId)
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;

            $data['realization_date'] = $data['realization_date_from'];
            $data['possible_delay_days'] = Carbon::parse($data['realization_date_from'])->diffInDays(Carbon::parse($data['realization_date_to']));
            $notification = $this->orderWarehouseNotificationRepository->update($data, $notificationId);

            if (!empty($data['customer_notices'])) {
                $this->sendMessage($data, $notification);
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

            dispatch_now(new DispatchLabelEventByNameJob($data['order_id'], "warehouse-notification-accepted"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with create new order.',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]
            );
            die();
        }
    }

    public function sendInvoice(Request $request)
    {
        try {
            $orderId = $request->orderId;
            $order = $this->orderRepository->find($orderId);

            if (empty($order)) {
                abort(404);
            }

            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            Storage::disk('local')->put('public/invoices/' . $filename, file_get_contents($file));
            $invoice = $order->invoices()->create([
                'invoice_type' => 'buy',
                'invoice_name' => $filename,
                'is_visible_for_client' => (boolean)$request->isVisibleForClient,
            ]);
            $invoiceRequest = $order->invoiceRequests()->first();
            if (!empty($invoiceRequest) && $invoiceRequest->status === 'MISSING') {
                $invoiceRequest->update([
                    'status' => 'SENT'
                ]);
            }
            dispatch_now(new DispatchLabelEventByNameJob($order, "new-file-added-to-order"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with send invoice.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $orderId = $request->orderId;
            $order = $this->orderRepository->find($orderId);

            if (empty($order)) {
                abort(404);
            }

            foreach ($order->packages as $package) {
                if ($package->status === 'NEW') {
                    $package->status = 'SENDING';
                    $package->save();
                }
            }
            dispatch_now(new DispatchLabelEventByNameJob($order, "all-shipments-went-out"));
            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with change order status.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }
}

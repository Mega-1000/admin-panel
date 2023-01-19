<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Facades\Mailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Entities\OrderWarehouseNotification;
use App\Mail\OrderStatusChangedToDispatchMail;

/**
 * Class OrderStatusChangedToDispatchNotificationJob
 * @package App\Jobs
 */
class OrderStatusChangedToDispatchNotificationJob extends Job implements ShouldQueue
{
    /**
     * @var
     */
    protected $orderId;
    /**
     * @var null
     */
    protected $self;
    /**
     * @var null
     */
    protected $path;
    /**
     * @var null
     */
    protected $pathSecond;
    /**
     * @var null
     */
    protected $packageNumber;

    /**
     * OrderStatusChangedToDispatchNotificationJob constructor.
     * @param $orderId
     * @param $self
     */
    public function __construct($orderId, $self = null, $path = null, $packageNumber = null, $pathSecond = null)
    {
        $this->orderId = $orderId;
        $this->self = $self;
        $this->path = $path;
        $this->pathSecond = $pathSecond;
        $this->packageNumber = $packageNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            $order = Order::findOrFail($this->orderId);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), ['line' => $exception->getLine(), 'file' => $exception->getFile(), 'comment' => 'Nie znaleziono zamówienia o numerze: ' . $this->orderId . ' podczas wysyłania awizacji.']);
            return;
        }
        $warehouse = $order->warehouse;
        if ($warehouse && $warehouse->firm) {
            $warehouseMail = $warehouse->firm->email;
        }
        if (empty($warehouseMail)) {
            Log::notice('Brak adresu mailowego w firmie, lub magazyn nie istnieje', ['line' => __LINE__, 'file' => __FILE__, 'order' => $order->id]);
            return;
        }

        $subject = "Przypomnienie o potwierdzenie awizacji dla zamówienia nr. " . $this->orderId;

        $dataArray = [
            'order_id' => $this->orderId,
            'warehouse_id' => $order->warehouse_id,
            'waiting_for_response' => true,
        ];

        $notification = OrderWarehouseNotification::where($dataArray)->first();
        if(!empty($notification) && (!$order->isOrderHasLabel(Label::PACKAGE_NOTIFICATION_SENT_LABEL) || $order->isOrderHasLabel(Label::PACKAGE_NOTIFICATION_LABEL))) {
            $notification->update([
                'order_id' => $this->orderId,
                'warehouse_id' => $order->warehouse_id,
                'waiting_for_response' => false,
            ]);
            Log::notice('Znaleziono etykietę Awizacja przyjęta w zamówieniu. Status wysyłania notyfikacji został zmieniony na przyjęty.', ['line' => __LINE__, 'file' => __FILE__, 'order' => $order->id]);
            return;
        }
        if (!$notification && !$order->isOrderHasLabel(Label::WAREHOUSE_REMINDER)) {
            $subject = "Prośba o potwierdzenie awizacji dla zamówienia nr. " . $this->orderId;
            $notification = OrderWarehouseNotification::create($dataArray);
        }

        $acceptanceFormLink = rtrim(env('FRONT_NUXT_URL'),"/") . "/magazyn/awizacja/{$notification->id}/{$order->warehouse_id}/{$this->orderId}";
        $sendFormInvoice = rtrim(env('FRONT_NUXT_URL'),"/") . "/magazyn/awizacja/{$notification->id}/{$order->warehouse_id}/{$this->orderId}/wyslij-fakture";

        if(!!filter_var($warehouseMail, FILTER_VALIDATE_EMAIL)) {
            if ($this->path === null) {
                $email = new OrderStatusChangedToDispatchMail($subject, $acceptanceFormLink, $sendFormInvoice, $order, $this->self);
                Mailer::notification()->to($warehouseMail)->send($email);
                Log::notice('Wysłano email awizacyjny na mail: ' . $warehouseMail . ' dla zamówienia: ' . $order->id, ['line' => __LINE__, 'file' => __FILE__]);
            } else {
                $email = new OrderStatusChangedToDispatchMail($subject, $acceptanceFormLink, $sendFormInvoice, $order, $this->self, $this->path, $this->packageNumber, $this->pathSecond);
                Mailer::notification()->to($warehouseMail)->send($email);
            }
        }
    }
}

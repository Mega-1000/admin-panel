<?php

namespace App\Jobs;

use App\Mail\OrderStatusChangedToDispatchMail;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarehouseNotificationRepository;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderStatusChangedToDispatchNotificationJob
 * @package App\Jobs
 */
class OrderStatusChangedToDispatchNotificationJob extends Job
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
    public function handle(
        OrderRepository $orderRepository,
        OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository
    ) {
        try {
            $order = $orderRepository->find($this->orderId);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), ['line' => $exception->getLine(), 'file' => $exception->getFile()]);
            return;
        }

        $warehouse = $order->warehouse;
        if ($warehouse && $warehouse->firm()->first()) {
            $warehouseMail = $warehouse->firm->email;
        }
        if (empty($warehouseMail)) {
            Log::notice('Brak adresu mailowego w firmie, lub magazyn nie istnieje', ['line' => __LINE__, 'file' => __FILE__]);
            return;
        }
        $subject = "Przypomnienie o potwierdzenie awizacji dla zamówienia nr. " . $this->orderId;

        $dataArray = [
            'order_id' => $this->orderId,
            'warehouse_id' => $order->warehouse_id,
            'waiting_for_response' => true,
        ];

        $notification = $orderWarehouseNotificationRepository->findWhere($dataArray)->first();

        if (!$notification) {
            $subject = "Prośba o potwierdzenie awizacji dla zamówienia nr. " . $this->orderId;
            $notification = $orderWarehouseNotificationRepository->create($dataArray);
        }

        $acceptanceFormLink = env('FRONT_NUXT_URL') . "/magazyn/awizacja/{$notification->id}/{$order->warehouse_id}/{$this->orderId}";
        $sendFormInvoice = env('FRONT_NUXT_URL') . "/magazyn/awizacja/{$notification->id}/{$order->warehouse_id}/{$this->orderId}/wyslij-fakture";
        if(!!filter_var($warehouseMail, FILTER_VALIDATE_EMAIL)) {
            if ($this->path === null) {
                \Mailer::create()
                    ->to($warehouseMail)
                    ->send(new OrderStatusChangedToDispatchMail($subject, $acceptanceFormLink,
                        $sendFormInvoice, $order, $this->self));
            } else {
                \Mailer::create()
                    ->to($warehouseMail)
                    ->send(new OrderStatusChangedToDispatchMail($subject,
                        $acceptanceFormLink,
                        $sendFormInvoice, $order, $this->self, $this->path, $this->packageNumber, $this->pathSecond));
            }
        }
    }
}

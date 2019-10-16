<?php

namespace App\Jobs;

use App\Mail\OrderStatusChangedToDispatchMail;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarehouseNotificationRepository;

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
    protected $packageNumber;

    /**
     * OrderStatusChangedToDispatchNotificationJob constructor.
     * @param $orderId
     * @param $self
     */
    public function __construct($orderId, $self = null, $path = null, $packageNumber = null)
    {
        $this->orderId = $orderId;
        $this->self = $self;
        $this->path = $path;
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
        $order = $orderRepository->find($this->orderId);
        $warehouseMail = $order->warehouse()->first()->firm()->first()->email;   //TODO find out which email should be taken
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
                        $sendFormInvoice, $order, $this->self, $this->path, $this->packageNumber));
            }
        }
    }
}

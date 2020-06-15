<?php

namespace App\Jobs;

use App\Helpers\EmailTagHandlerHelper;
use App\Mail\WarehouseOrder;
use App\Repositories\OrderRepository;
use App\Repositories\TagRepository;

/**
 * Class OrderStatusChangedNotificationJob
 * @package App\Jobs
 */
class SendWarehouseOrderEmailJob extends Job
{

    /**
     * @var
     */
    protected $warehouseOrderId;

    /**
     * @var null
     */
    protected $message;

    protected $email;

    /**
     * Requires to pass id of Order that's status changed to ::dispatch()
     *
     * @param $warehouseOrderId
     * @param $email
     * @param $message
     */
    public function __construct($warehouseOrderId, $email, $message = null)
    {
        $this->warehouseOrderId = $warehouseOrderId;
        $this->message = $message;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailTagHandlerHelper $emailTagHandler, OrderRepository $orderRepository, TagRepository $tagRepository)
    {
        if (strpos($this->email,'allegromail.pl')) {
            return;
        }
        $subject = "Zamówienie Produktów. Zlecenie nr: " . $this->warehouseOrderId;
        \Mailer::create()
            ->to($this->email)
            ->send(new WarehouseOrder($subject, $this->message));
    }
}

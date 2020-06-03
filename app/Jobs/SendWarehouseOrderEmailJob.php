<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Repositories\OrderRepository;
use App\Repositories\TagRepository;
use App\Helpers\EmailTagHandlerHelper;
use App\Mail\WarehouseOrder;


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



        $subject = "Zamówienie Produktów. Zlecenie nr: ". $this->warehouseOrderId;
            \Mailer::create()
                ->to($this->email)
                ->send(new WarehouseOrder($subject, $this->message));
    }
}

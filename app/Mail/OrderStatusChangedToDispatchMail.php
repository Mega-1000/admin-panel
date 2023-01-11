<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class OrderStatusChangedToDispatchMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formLink;

    public $sendFormInvoice;

    public $order;

    public $self;

    public $path;

    public $packageNumber;

    public $pathSecond;

    public $customerShipmentDateFrom;

    public $customerShipmentDateTo;

    /**
     * OrderStatusChangedToDispatchMail constructor.
     * @param $subject
     * @param $formLink
     * @param $sendFormInvoice
     * @param $order
     * @param $self
     */
    public function __construct($subject, $formLink, $sendFormInvoice, $order, $self = null, $path = null, $packageNumber = null, $pathSecond = null)
    {
        ini_set('max_execution_time', 60);
        $this->formLink = $formLink;
        $this->sendFormInvoice = $sendFormInvoice;
        $this->subject = $subject;
        $this->order = $order;
        $this->self = $self;
        $this->path = $path;
        $this->packageNumber = $packageNumber;
        $this->pathSecond = $pathSecond;
        $this->customerShipmentDateFrom = $order->dates->getDateAttribute('customer_shipment_date_from');
        $this->customerShipmentDateTo = $order->dates->getDateAttribute('customer_shipment_date_to');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->self == true) {
            return $this->view('emails.order-status-changed-to-dispatch-self');
        } else {
            if ($this->path == null) {
                return $this->view('emails.order-status-changed-to-dispatch');
            } else {
                return $this->view('emails.reminder-order-status-changed-to-dispatch')->attach($this->path)->attach($this->pathSecond);
            }
        }

    }
}

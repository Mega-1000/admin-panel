<?php

namespace App\Mail;

use App\Helpers\MessagesHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

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

    public $chatLink;

    /**
     * OrderStatusChangedToDispatchMail constructor.
     * @param $subject
     * @param $formLink
     * @param $sendFormInvoice
     * @param $order
     * @param null $self
     * @param null $path
     * @param null $packageNumber
     * @param null $pathSecond
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
        $this->customerShipmentDateFrom = $order->dates === null ? '' : $order->dates->getDateAttribute('customer_shipment_date_from');
        $this->customerShipmentDateTo = $order->dates === null ? '' : $order->dates->getDateAttribute('customer_shipment_date_to');

        // create new Chat
        $helper = new MessagesHelper();
        $helper->orderId = $order->id;
        $helper->currentUserId = Auth::user()->id;
        $helper->currentUserType = MessagesHelper::TYPE_USER;
        $userToken = $helper->encrypt();
        // $this->chatLink = 'https://'.$_SERVER['HTTP_HOST'].'/chat/'.$userToken;
        $this->chatLink = '';
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        if ($this->self) {
            return new Content('emails.order-status-changed-to-dispatch-self');
        }
        if ($this->path == null) {
            return new Content('emails.order-status-changed-to-dispatch');
        }

        return new Content('emails.reminder-order-status-changed-to-dispatch');
        //->attach($this->path)->attach($this->pathSecond);
    }

    public function attachments(): array
    {
        $attachments = [];
        if ($this->path !== '') {
            $attachments[] = Attachment::fromPath($this->path);
        }
        if ($this->pathSecond !== '') {
            $attachments[] = Attachment::fromPath($this->pathSecond);
        }

        return $attachments;
    }

}

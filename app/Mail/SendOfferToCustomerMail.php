<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendOfferToCustomerMail
 * @package App\Mail
 */
class SendOfferToCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $order;

    /**
     * @var
     */
    public $productsVariation;

    /**
     * @var
     */
    public $allProductsFromSupplier;

    /**
     * @var
     */
    public $productPacking;


    /**
     * SendOfferToCustomerMail constructor.
     * @param $subject
     * @param $order
     * @param $productsVariation
     * @param $allProductsFromSupplier
     * @param $productPacking
     */
    public function __construct($subject, $order, $productsVariation = null, $allProductsFromSupplier = null, $productPacking)
    {
        $this->subject = $subject;
        $this->order = $order;
        $this->productsVariation = $productsVariation;
        $this->allProductsFromSupplier = $allProductsFromSupplier;
        $this->productPacking = $productPacking;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.send-offer-to-customer');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath(storage_path('app/public/products/320-01.pdf')),
        ];
    }
}

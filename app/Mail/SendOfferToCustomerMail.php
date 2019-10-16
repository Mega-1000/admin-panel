<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-offer-to-customer');
    }
}

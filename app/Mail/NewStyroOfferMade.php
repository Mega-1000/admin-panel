<?php

namespace App\Mail;

use App\Entities\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewStyroOfferMade extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Stworzono zapytanie na styropian o numerze: ' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        $products = Product::where('variation_group', 'styropiany')->whereHas('children')->get();

        $result = '';

        foreach ($products as $product) {
            $result .= 'Name: ' . $product->name . ', Price: ' . $product->price->net_purchase_price_commercial_unit . '; ';
        }

        $result = rtrim($result, '; ');
        return new Content(
            view: 'emails.new-styro-offer',
            with: [
                'order' => $this->order
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}

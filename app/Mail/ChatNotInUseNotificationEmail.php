<?php

namespace App\Mail;

use App\Entities\Chat;
use App\Entities\Order;
use App\Helpers\MessagesHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChatNotInUseNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Chat $chat;
    public string $chatUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
        $messagesHelper = new MessagesHelper();
        $orderId = $chat->order->id;
        $messagesHelper->chatId = Order::find($orderId)?->chat?->id;
        $token = $messagesHelper->getChatToken($orderId, $chat->order->customer->id, 'c');
        $this->chatUrl = 'https://admin.mega1000.pl/chat/' . $token;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Czy dialog do zapyania:' . $this->chat->order->id . ' został zakończony? Brak odpowiedzi na wiadomość na chacie od 2 dni',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'chat-not-in-use',
            with: [
                'chat' => $this->chat,
                'chatUrl' => $this->chatUrl
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}

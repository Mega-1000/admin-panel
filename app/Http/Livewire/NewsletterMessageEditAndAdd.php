<?php

namespace App\Http\Livewire;

use App\Entities\Message;
use App\Entities\NewsletterMessage;
use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\MailSending;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NewsletterMessageEditAndAdd extends Component
{
    public ?int $messageId;
    public mixed $message;
    public string $title = '';
    public string $content = '';
    public mixed $messages;
    public string $labelIds = '';
    public bool $sendToAll = false;

    public function render(): View
    {
        $this->message = $this->message ?? NewsletterMessage::find($this->messageId ?? 0) ?? new Message();
        $this->messages = NewsletterMessage::all();

        $this->title = $this->title === '' ? $this->message->title : $this->title;
        $this->content = $this->content === '' ? $this->message->content : $this->content;

        return view('livewire.newsletter-message-edit-and-add');
    }

    public function submitForm(): void
    {
        $data = [
            'title' => $this->title,
            'content' => $this->content,
        ];

        if ($this->message->id) {
            $this->message->update($data);

            return;
        }

        $labelIds = explode(',', $this->labelIds);

        if (!$this->sendToAll) {
            $labelIds = [];
        }

        $orders = Order::whereHas('labels', function ($q) use ($labelIds) {
            $q->whereIn('label_id', $labelIds);
        })
        ->with('customer')
        ->get();

        $emails = [];

        foreach ($orders as $order) {
            $user = $order->customer;
            if (in_array($user->login, $emails)) {
                continue;
            }

            $emails[] = $user->login;

            Mailer::create()
                ->to($user->login)
            ->send(new MailSending(
                $this->title,
                $this->content
            ));
        }

        NewsletterMessage::create($data);
    }

    public function deleteMessage(int $messageId): void
    {
        $message = NewsletterMessage::find($messageId);

        if ($message) {
            $message->delete();
        }
    }
}

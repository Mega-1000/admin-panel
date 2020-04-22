<?php

namespace App\Jobs;

use App\Mail\StartCommunication;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartCommunicationMailSenderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $orderId;

    protected $email;

    /**
     * Requires to pass id of Order that's status changed to ::dispatch()
     *
     * @param $orderId
     * @param $email
     */
    public function __construct($orderId, $email)
    {
        $this->orderId = $orderId;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            \Log::error('Error while sending e-mail in StartCommunicationMailSenderJob: invalid e-mail address');
            return;
        }
        try {
            \Mailer::create()
                ->to($this->email)
                ->send(new StartCommunication($this->orderId));
        } catch (\Exception $e) {
            \Log::error('Error while sending e-mail in StartCommunicationMailSenderJob: '.$e->getMessage());
        }
    }
}

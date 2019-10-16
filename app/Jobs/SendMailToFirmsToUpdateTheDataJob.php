<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\SendRequestToUpdateDataMail;

class SendMailToFirmsToUpdateTheDataJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = env('FRONT_NUXT_URL') . "/firmy/aktualizacja/{$this->id}/";
        \Mailer::create()
            ->to($this->email)
            ->send(new SendRequestToUpdateDataMail("Prośba o aktualizację danych firmy", $url));
    }
}

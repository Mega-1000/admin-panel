<?php

namespace App\Jobs;

use App\Facades\Mailer;
use App\Mail\SendRequestToUpdateDataMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendMailToFirmsToUpdateTheDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
        $url = rtrim(config('app.front_nuxt_url'), "/") . "/firmy/aktualizacja/{$this->id}/";
        Mailer::create()
            ->to($this->email)
            ->send(new SendRequestToUpdateDataMail("Prośba o aktualizację danych firmy", $url));
    }
}

<?php

namespace App\Jobs;

use App\Services\ShippingPayInService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportShippingPayIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected readonly UploadedFile $file
    ) {}

    /**
     * @throws Exception
     */
    public function handle(ShippingPayInService $shippingPayInService): void
    {
        $shippingPayInService->processPayIn(
            $this->file
        );
    }
}

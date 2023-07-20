<?php

namespace App\Jobs;

use App\DTO\ImportPayIn\AllegroPayInDTO;
use App\Enums\AllegroImportPayInDataEnum;
use App\Facades\Mailer;
use App\Mail\AllegroPayInMail;
use App\Services\AllegroImportPayInService;
use App\Services\AllegroPaymentService;
use App\Services\FindOrCreatePaymentForPackageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportPayInFromAllegroJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AllegroPaymentService $allegroPaymentService, AllegroImportPayInService $allegroImportPayInService, FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService)
    {
        $files = Storage::disk('allegroPayInDisk')->files();
        foreach ($files as $file) {
            Storage::disk('allegroPayInDisk')->delete($file);
        }

        $payments = $allegroPaymentService->getPaymentsFromPastDay();

        $filename = "transactionWithoutOrder.csv";
        $file = fopen($filename, 'w');
        
        fputcsv($file, AllegroPayInDTO::$headers);

        $allegroImportPayInService->writeToFile($payments, AllegroImportPayInDataEnum::API, $file, $findOrCreatePaymentForPackageService);

        fclose($file);

        $newFilePath = 'public/transaction/TransactionWithoutOrdersFromAllegro' . date('Y-m-d') . '.csv';

        Storage::disk('allegroPayInDisk')->put($newFilePath, file_get_contents($filename));

        Mailer::create()
            ->to('ksiegowosc@ephpolska.pl')->send(new AllegroPayInMail($newFilePath));
    }
}

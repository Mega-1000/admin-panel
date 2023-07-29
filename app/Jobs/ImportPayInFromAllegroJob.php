<?php

namespace App\Jobs;

use App\DTO\ImportPayIn\AllegroPayInDTO;
use App\Enums\AllegroImportPayInDataEnum;
use App\Facades\Mailer;
use App\Mail\AllegroPayInMail;
use App\Mail\TestMail;
use App\Services\AllegroImportPayInService;
use App\Services\AllegroPaymentService;
use App\Services\FindOrCreatePaymentForPackageService;
use Carbon\Carbon;
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
     * Execute the job.
     *
     * @param AllegroPaymentService $allegroPaymentService
     * @param AllegroImportPayInService $allegroImportPayInService
     * @param FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService
     * @return void
     */
    public function handle(
        AllegroPaymentService $allegroPaymentService,
        AllegroImportPayInService $allegroImportPayInService,
        FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService
    ): void
    {
        $files = Storage::disk('allegroPayInDisk')->files();
        foreach ($files as $file) {
            Storage::disk('allegroPayInDisk')->delete($file);
        }

        $payments = $allegroPaymentService->getPaymentsFromLastDay();

        $filename = "transactionWithoutOrder.csv";
        $file = fopen($filename, 'w');

        fputcsv($file, AllegroPayInDTO::$headers);

        $allegroImportPayInService->writeToFile($payments, AllegroImportPayInDataEnum::API, $file, $findOrCreatePaymentForPackageService);

        fclose($file);

        $yesterdayDate = Carbon::yesterday()->format('Y-m-d');

        $newFilePath = 'public/transaction/TransactionWithoutOrdersFromAllegro' . $yesterdayDate . '.csv';

        Storage::disk('allegroPayInDisk')->put($newFilePath, file_get_contents($filename));

//        Mailer::create()
//            ->to('ksiegowosc@ephpolska.pl')->send(new AllegroPayInMail($newFilePath));
    }
}

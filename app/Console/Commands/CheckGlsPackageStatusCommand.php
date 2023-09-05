<?php

namespace App\Console\Commands;

use App\Entities\OrderPackage;
use App\Helpers\GlsCourier;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class CheckGlsPackageStatusCommand extends Command
{
    protected $signature = 'check:gls-package-status {letter-number}';

    protected $description = 'Command description';

    /**
     * @throws GuzzleException
     */
    public function handle(): void
    {

        $packageNumber = $this->argument('letter-number');

        $this->comment('Szukanie paczki o numerze: ' . $packageNumber);

        $orderPackage = OrderPackage::where('letter_number', $packageNumber)->first();
        if ($orderPackage !== null) {

            $this->comment('Sprawdzanie paczki');
            $glsClient = new GlsCourier();
            $glsClient->checkStatus($orderPackage, true);
            $this->comment('Koniec sprawdzania paczki');
            return;
        }


        $this->comment('Brak paczki o takim numerze');
    }
}

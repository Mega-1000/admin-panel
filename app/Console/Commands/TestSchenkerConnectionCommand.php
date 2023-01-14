<?php

namespace App\Console\Commands;

use App\Exceptions\SapException;
use App\Exceptions\SoapParamsException;
use App\Services\SoapClientService;
use App\Utils\SoapParams;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class TestSchenkerConnectionCommand extends Command
{
    protected $signature = 'schenker:test';

    protected $description = 'Checking connection with the Schenker';

    /**
     * @throws FileNotFoundException
     * @throws SapException
     * @throws SoapParamsException
     */
    public function handle()
    {
        $soapParams = new SoapParams();
        $soapParams->setParam('getPackageDictionaryRequest', '');
        $response = SoapClientService::sendRequest(
            Storage::disk('wsdl')->path('schenker.wsdl'),
            'getPackageDictionary',
            $soapParams
        );
        if (array_key_exists('packageDictionary', $response) && count($response['packageDictionary'])) {
            $this->info('POŁĄCZENIE DZIAŁA POPRAWNIE');
            return true;
        }
        $this->alert('POŁĄCZENIE NIE DZIAŁA POPRAWNIE');
        return false;
    }
}

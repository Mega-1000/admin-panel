<?php

namespace App\Console\Commands;

use App\Exceptions\SapException;
use App\Exceptions\SoapParamsException;
use App\Services\SchenkerService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TestSchenkerConnectionCommand extends Command
{
    protected $signature = 'schenker:test_connection';

    protected $description = 'Checking connection with the Schenker';

    /**
     * @throws FileNotFoundException
     * @throws SapException
     * @throws SoapParamsException
     */
    public function handle()
    {
        $response = SchenkerService::getPackageDictionary();
        if (array_key_exists('packageDictionary', $response) && count($response['packageDictionary'])) {
            $this->info('POŁĄCZENIE DZIAŁA POPRAWNIE');
            return true;
        }
        $this->alert('POŁĄCZENIE NIE DZIAŁA POPRAWNIE');
        return false;
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport;

use App\Domains\DelivererPackageImport\Exceptions\FileNotFoundException;
use App\Domains\DelivererPackageImport\Exceptions\OrderNotFoundException;
use App\Domains\DelivererPackageImport\Exceptions\OrderPackageWasNotFoundException;
use App\Domains\DelivererPackageImport\Exceptions\TooManyOrdersInDBException;
use App\Domains\DelivererPackageImport\Factories\DelivererImportRulesManagerFactory;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRulesManager;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Domains\DelivererPackageImport\Services\DelivererImportLogService;
use App\Entities\Deliverer;
use Illuminate\Support\Facades\Storage;
use \Symfony\Component\HttpFoundation\File\File;

class TransportPaymentImporter
{
    /* @var $file File */
    private $file;

    private $delivererImportRuleRepository;

    private $delivererImportRulesManagerFactory;

    /* @var $delivererImportRulesManager DelivererImportRulesManager */
    private $delivererImportRulesManager;

    private $delivererImportLogService;

    public function __construct(
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepository,
        DelivererImportRulesManagerFactory $delivererImportRulesManagerFactory,
        DelivererImportLogService $delivererImportLogService
    ) {
        $this->delivererImportRuleRepository = $delivererImportRuleRepository;
        $this->delivererImportRulesManagerFactory = $delivererImportRulesManagerFactory;
        $this->delivererImportLogService = $delivererImportLogService;
    }

    public function import(Deliverer $deliverer, array $fileData): string
    {
        $this->file = $fileData['file'];

        $logFileName = $this->delivererImportLogService->createLog(
            $deliverer->id,
            $fileData['oldFileName'],
            $fileData['uniqueLogFileName']
        );

        $this->delivererImportRulesManager = $this->delivererImportRulesManagerFactory->create(
            $deliverer,
            $logFileName
        );

        $this->delivererImportRulesManager->importLogger->logInfo('Import został rozpoczęty');

        $this->run();

        $this->delivererImportRulesManager->importLogger->logInfo('Import został zakończony');

        return $fileData['uniqueLogFileName'];
    }

    private function run(): void
    {
        if (($handle = fopen($this->file->getRealPath(), "r")) === FALSE) {
            throw new FileNotFoundException('Nie można otworzyć pliku');
        }

        $firstLineWasRead = false;
        $counter = 1;
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            if (!$firstLineWasRead) {
                $firstLineWasRead = true;
                $counter++;

                continue;
            }

            try {
                $this->delivererImportRulesManager->runRules($line);
            } catch (OrderNotFoundException $exception) {
                $this->delivererImportRulesManager->importLogger->logWarning(
                    "Nie znaleziono zamówienia dla LP: {$exception->getMessage()}"
                );
            } catch (OrderPackageWasNotFoundException $exception) {
                $this->delivererImportRulesManager->importLogger->logWarning(
                    "Nie znaleziono LP dla zamówienia nr {$exception->getMessage()}"
                );
            } catch (TooManyOrdersInDBException | \Exception $exception) {
                $this->delivererImportRulesManager->importLogger->logError(
                    $exception->getMessage()
                );
            }

            $counter++;
        }

        fclose($handle);

        Storage::disk('private')->delete('transport/' . $this->file->getFilename());
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport;

use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRulesManager;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;
use Illuminate\Support\Facades\Storage;
use \Symfony\Component\HttpFoundation\File\File;

class TransportPaymentImporter
{
    /* @var $file File */
    private $file;

    private $delivererImportRuleRepository;

    private $delivererImportRulesManager;

    public function __construct(
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepository,
        DelivererImportRulesManager $delivererImportRulesManager
    ) {
        $this->delivererImportRulesManager = $delivererImportRulesManager;
        $this->delivererImportRuleRepository = $delivererImportRuleRepository;
    }

    public function import(Deliverer $deliverer, File $file): void
    {
        $this->file = $file;

        $this->delivererImportRulesManager->setDeliverer($deliverer);

        if (!$this->delivererImportRulesManager->prepareRules()) {
            throw new \Exception('No import rules for the ' . $deliverer->name . ' deliverer');
        }

        $this->run();
    }

    private function run(): void
    {
        if (($handle = fopen($this->file->getRealPath(), "r")) === FALSE) {
            throw new \Exception('Nie można otworzyć pliku');
        }

        $firstLineWasRead = false;
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            if (!$firstLineWasRead) {
                $firstLineWasRead = true;

                continue;
            }

            $this->delivererImportRulesManager->runRules($line);
        }

        fclose($handle);

        Storage::disk('private')->delete('transport/' . $this->file->getFilename());
    }
}

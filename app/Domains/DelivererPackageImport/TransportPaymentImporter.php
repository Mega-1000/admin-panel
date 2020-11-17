<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport;

use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRulesManager;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;
use Illuminate\Support\Facades\Storage;
use \Symfony\Component\HttpFoundation\File\File;

class TransportPaymentImporter
{
    private $deliverer;

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

    public function import(Deliverer $deliverer, File $file) {
        $this->deliverer = $deliverer;
        $this->file = $file;

        $this->delivererImportRulesManager->setDeliverer($this->deliverer);

        if (!$this->delivererImportRulesManager->prepareRules()) {
            throw new \Exception('No import rules for the ' . $this->deliverer->name . ' deliverer');
        }

        $this->run();
    }

    private function run()
    {
        if (($handle = fopen($this->file->getRealPath(), "r")) === FALSE) {
            throw new \Exception('Nie można otworzyć pliku');
        }

        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            try {
                $this->processLine($line);
                dd('continue import lines');
            } catch (\Exception $e) {
                dd('catch', $e->getMessage());
            }
        }
        fclose($handle);

        Storage::disk('private')->delete('transport/' . $this->file->getFilename());
    }

    private function processLine($line)
    {
        $this->delivererImportRulesManager->runRules($line);

        dd('process line');
    }
}

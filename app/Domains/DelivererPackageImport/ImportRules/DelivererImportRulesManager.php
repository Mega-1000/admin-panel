<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;

class DelivererImportRulesManager
{
    private $delivererImportRulesRepository;

    private $deliverer;

    private $importRules;

    public function __construct(DelivererImportRuleRepositoryEloquent $delivererImportRuleRepositoryEloquent)
    {
        $this->delivererImportRulesRepository = $delivererImportRuleRepositoryEloquent;
    }

    public function setDeliverer(Deliverer $deliverer): void
    {
        $this->deliverer = $deliverer;
    }

    public function prepareRules(): bool
    {
        $this->importRules = $this->delivererImportRulesRepository->getDelivererImportRules(
            $this->deliverer
        );

        return !empty($this->importRules);
    }

    public function search()
    {
        dd($this->importRules);
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRulesManager;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;

class DelivererImportRulesManagerFactory
{
    private $delivererImportRuleRepositoryEloquent;

    private $delivererImportRuleFromEntityFactory;

    public function __construct(
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepositoryEloquent,
        DelivererImportRuleFromEntityFactory $delivererImportRuleFromEntityFactory
    ) {
        $this->delivererImportRuleRepositoryEloquent = $delivererImportRuleRepositoryEloquent;
        $this->delivererImportRuleFromEntityFactory = $delivererImportRuleFromEntityFactory;
    }

    public function create(Deliverer $deliverer): DelivererImportRulesManager
    {
        return new DelivererImportRulesManager(
            $this->delivererImportRuleRepositoryEloquent,
            $this->delivererImportRuleFromEntityFactory,
            $deliverer
        );
    }
}

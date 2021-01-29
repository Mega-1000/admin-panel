<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\DelivererImportLogger;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRulesManager;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;

class DelivererImportRulesManagerFactory
{
    private $delivererImportRuleRepositoryEloquent;

    private $delivererImportRuleFromEntityFactory;

    private $delivererImportLogger;

    public function __construct(
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepositoryEloquent,
        DelivererImportRuleFromEntityFactory $delivererImportRuleFromEntityFactory,
        DelivererImportLogger $delivererImportLogger
    ) {
        $this->delivererImportRuleRepositoryEloquent = $delivererImportRuleRepositoryEloquent;
        $this->delivererImportRuleFromEntityFactory = $delivererImportRuleFromEntityFactory;
        $this->delivererImportLogger = $delivererImportLogger;
    }

    public function create(Deliverer $deliverer, string $logFileName): DelivererImportRulesManager
    {
        return new DelivererImportRulesManager(
            $this->delivererImportRuleRepositoryEloquent,
            $this->delivererImportRuleFromEntityFactory,
            $this->delivererImportLogger,
            $deliverer,
            $logFileName
        );
    }
}

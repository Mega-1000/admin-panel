<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesColumnNumberVO;
use App\Entities\DelivererImportRule;
use App\Repositories\OrderRepositoryEloquent;

abstract class DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    public $action;

    protected $importRuleEntity;

    protected $orderRepository;

    public function __construct(
        OrderRepositoryEloquent $orderRepository,
        DelivererImportRule $delivererImportRule
    ) {
        $this->orderRepository = $orderRepository;
        $this->importRuleEntity = $delivererImportRule;

        $this->action = $delivererImportRule->getAction()->value;
    }

    public function getImportRuleEntity(): DelivererImportRule
    {
        return $this->importRuleEntity;
    }

    abstract public function run(array $line);

    protected function getDbColumnName(): ?DelivererRulesColumnNameEnum
    {
        return new DelivererRulesColumnNameEnum($this->importRuleEntity->db_column_name);
    }

    protected function getImportColumnNumber(): ?DelivererImportRulesColumnNumberVO
    {
        return new DelivererImportRulesColumnNumberVO($this->importRuleEntity->import_column_number);
    }
}

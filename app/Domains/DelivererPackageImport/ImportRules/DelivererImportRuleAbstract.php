<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesColumnNumberVO;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;

abstract class DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    public $action;

    protected $importRuleEntity;

    protected $orderRepository;

    /* @var array */
    protected $line;

    protected $order;

    public function __construct(
        OrderRepositoryEloquent $orderRepository,
        DelivererImportRule $delivererImportRule
    ) {
        $this->orderRepository = $orderRepository;
        $this->importRuleEntity = $delivererImportRule;

        $this->action = $delivererImportRule->getAction()->value;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getImportRuleEntity(): DelivererImportRule
    {
        return $this->importRuleEntity;
    }

    abstract public function run(array $line): ?Order;

    protected function getDbColumnName(): ?DelivererRulesColumnNameEnum
    {
        return new DelivererRulesColumnNameEnum($this->importRuleEntity->db_column_name);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getDataToImport()
    {
        $columnNumber = $this->getImportColumnNumber()->get();

        if (isset($this->line[$columnNumber-1])) {
            return $this->line[$columnNumber-1];
        }

        throw new \Exception('No correct column number for ' . $this->getDbColumnName() . ' column');
    }

    /**
     * @return mixed
     */
    protected function getValue()
    {
        return $this->importRuleEntity->value;
    }

    /**
     * @return mixed
     */
    protected function getChangeTo()
    {
        return $this->importRuleEntity->change_to;
    }

    private function getImportColumnNumber(): ?DelivererImportRulesColumnNumberVO
    {
        return new DelivererImportRulesColumnNumberVO($this->importRuleEntity->import_column_number);
    }
}

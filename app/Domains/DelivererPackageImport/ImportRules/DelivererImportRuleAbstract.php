<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleColumnRepositoryInterface;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesColumnNumberVO;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use Exception;

abstract class DelivererImportRuleAbstract
{
    public $action;

    protected $importRuleEntity;

    protected $columnRepository;

    /* @var array */
    protected $line;

    protected $order;

    protected $dataToImport;

    private $column;

    public function __construct(
        DelivererImportRule $delivererImportRuleEntity,
        DelivererImportRuleColumnRepositoryInterface $columnRepository
    ) {
        $this->columnRepository = $columnRepository;
        $this->importRuleEntity = $delivererImportRuleEntity;

        $this->action = $delivererImportRuleEntity->getAction()->value;
        $this->column = $delivererImportRuleEntity->getColumnName()->value;
    }

    abstract public function run(array $line): ?Order;

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function getData()
    {
        $columnNumber = $this->getColumnNumber()->get();

        if (isset($this->line[$columnNumber-1])) {
            return $this->line[$columnNumber-1];
        }

        throw new Exception(sprintf(
            'No correct column number for %s column',
            $this->importRuleEntity->getColumnName()->value
        ));
    }

    protected function getValue()
    {
        return $this->importRuleEntity->value;
    }

    protected function getChangeTo()
    {
        return $this->importRuleEntity->change_to;
    }

    private function getColumnNumber(): ?DelivererImportRulesColumnNumberVO
    {
        return new DelivererImportRulesColumnNumberVO($this->importRuleEntity->import_column_number);
    }
}

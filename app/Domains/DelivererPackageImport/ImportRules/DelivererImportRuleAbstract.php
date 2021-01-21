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

    protected $parsedData;

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

    abstract public function run();

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function setData(array $line): void
    {
        $this->line = $line;
    }

    public function getImportRuleEntity(): DelivererImportRule
    {
        return $this->importRuleEntity;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getData()
    {
        $columnNumber = $this->getColumnNumber()->get();

        if (isset($this->line[$columnNumber-1])) {
            return $this->line[$columnNumber-1];
        }

        throw new Exception(sprintf(
            'W pliku CSV nie znaleziono kolumny %s',
            $this->importRuleEntity->getColumnName()->value
        ));
    }

    public function getParsedData(): ?string
    {
        return $this->parsedData;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function getConditionData()
    {
        $columnNumber = $this->getConditionColumnNumber()->get();

        if (isset($this->line[$columnNumber-1])) {
            return $this->line[$columnNumber-1];
        }

        throw new Exception(sprintf(
            'W pliku CSV nie znaleziono kolumny %s dla dodatkowego warunku',
            $this->importRuleEntity->getColumnName()->value
        ));
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
    protected function getConditionValue()
    {
        return $this->importRuleEntity->condition_value;
    }

    /**
     * @return mixed
     */
    protected function getChangeTo()
    {
        return $this->importRuleEntity->change_to;
    }

    private function getColumnNumber(): ?DelivererImportRulesColumnNumberVO
    {
        return new DelivererImportRulesColumnNumberVO($this->importRuleEntity->import_column_number);
    }

    private function getConditionColumnNumber(): ?DelivererImportRulesColumnNumberVO
    {
        return new DelivererImportRulesColumnNumberVO($this->importRuleEntity->condition_column_number);
    }
}

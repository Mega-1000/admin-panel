<?php

declare(strict_types=1);

namespace App\Http\DTOs;

class DelivererCreateImportRulesDTO
{
    private $actions;

    private $values;

    private $columnNames;

    private $columnNumbers;

    private $changeTo;

    private $conditionColumnNumber;

    private $conditionValue;

    public function __construct(array $importRules)
    {
        $this->actions = $importRules['actions'];
        $this->values = $importRules['values'];
        $this->columnNames = $importRules['columnNames'];
        $this->columnNumbers = $importRules['columnNumbers'];
        $this->changeTo = $importRules['changeTo'];
        $this->conditionColumnNumber = $importRules['conditionColumnNumber'];
        $this->conditionValue = $importRules['conditionValue'];
    }

    public function getRules(): array
    {
        return [
            'action' => $this->actions,
            'value' => $this->values,
            'columnName' => $this->columnNames,
            'columnNumber' => $this->columnNumbers,
            'changeTo' => $this->changeTo,
            'conditionColumnNumber' => $this->conditionColumnNumber,
            'conditionValue' => $this->conditionValue,
        ];
    }
}

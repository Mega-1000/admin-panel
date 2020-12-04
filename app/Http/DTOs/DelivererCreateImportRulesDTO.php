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

    public function __construct(array $importRules)
    {
        $this->actions = $importRules['actions'];
        $this->values = $importRules['values'];
        $this->columnNames = $importRules['columnNames'];
        $this->columnNumbers = $importRules['columnNumbers'];
        $this->changeTo = $importRules['changeTo'];
    }

    public function getRules(): array
    {
        return [
            'action' => $this->actions,
            'value' => $this->values,
            'columnName' => $this->columnNames,
            'columnNumber' => $this->columnNumbers,
            'changeTo' => $this->changeTo,
        ];
    }
}

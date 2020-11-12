<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ValueObjects;

class DelivererImportRulesColumnNumberVO
{
    private const MIN_COLUMN_NUMBER = 1;

    private const MAX_COLUMN_NUMBER = 20;

    private $columnNumber;

    public function __construct(int $columnNumber)
    {
        if (!$this->validate($columnNumber)) {
            throw new \InvalidArgumentException('Column number is out of the defined range');
        }

        $this->columnNumber = $columnNumber;
    }

    public function get(): int
    {
        return $this->columnNumber;
    }

    private function validate(int $columnNumber): bool
    {
        return $columnNumber >= self::MIN_COLUMN_NUMBER && $columnNumber <= self::MAX_COLUMN_NUMBER;
    }
}

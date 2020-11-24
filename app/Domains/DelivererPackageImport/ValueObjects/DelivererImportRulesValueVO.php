<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ValueObjects;

class DelivererImportRulesValueVO
{
    private $value;

    public function __construct(string $value)
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException('The value of import rule can not be empty');
        }

        $this->value = $value;
    }

    public function get(): string
    {
        return $this->value;
    }

    private function validate(string $value): bool
    {
        return !empty($value);
    }
}

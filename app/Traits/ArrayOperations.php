<?php

namespace App\Traits;

trait ArrayOperations
{
    public function stringify(): string
    {
        return implode(' ', $this->toArray());
    }

    public function contains(string $value): bool
    {
        return str_contains($this->stringify(), $value);
    }
}

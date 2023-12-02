<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

abstract class AbstractNonStandardColumnInvocable
{
    public abstract function __invoke(array $order): string;
}

<?php

namespace App\Helpers\interfaces;

use Illuminate\Database\Eloquent\Collection;

interface iPackageDivider
{
    public function setItems(Collection $itemList);
    public function divide();
}

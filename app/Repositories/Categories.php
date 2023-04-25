<?php

namespace App\Repositories;

use App\Entities\Category;

use Illuminate\Database\Eloquent\Collection;

class Categories
{
    public static function getElementsForCsvReloadJob(): Collection
    {
        return Category::query()
            ->Where('save_name', '!=', true)
            ->orWhere('save_description','!=', true)
            ->orWhere('save_image','!=', true)
            ->orWhere('artificially_created','!=', false)
            ->get();
    }

}

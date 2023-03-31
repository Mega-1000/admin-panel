<?php

namespace App\Repositories;

use App\Entities\Category;

class Categories
{
    public static function removeElementsForCsvReloadJob(): void
    {
        Category::query()
            ->where('save_name', true)
            ->where('save_description', true)
            ->where('save_image', true)
            ->where('artificially_created', false)
            ->delete();
    }

}

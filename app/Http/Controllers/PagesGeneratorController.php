<?php

namespace App\Http\Controllers;

use App\Entities\CustomPageCategory;

class PagesGeneratorController extends Controller
{
    public function getPages()
    {
        $pages = CustomPageCategory::root()->get();
        return view('pages.index')->withPages($pages);
    }

    public function createPage()
    {
        return view('pages.create');
    }

}

<?php

namespace App\Http\Controllers;

use App\Entities\CustomPageCategory;
use App\Http\Requests\PageCategoryCreateRequest;

class PagesGeneratorController extends Controller
{
    public function getPages()
    {
        $pages = CustomPageCategory::root()->get();
        return view('pages.index')->withPages($pages);
    }

    public function createPage()
    {
        $pages = CustomPageCategory::all();
        return view('pages.create')->withPages($pages);
    }

    public function store(PageCategoryCreateRequest $request)
    {
        $parentId = $request->input('parent_id');
        $order = $request->input('order');
        $category = new CustomPageCategory();
        $category->name = $request->input('name');
        $category->order = $order ?: 0;
        $category->parent_id = $parentId > 0 ? $parentId : null;
        $category->save();
        return $this->getPages();
    }
}

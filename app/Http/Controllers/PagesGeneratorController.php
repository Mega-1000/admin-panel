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
        $id = $request->input('id');
        if ($id) {
            $category = CustomPageCategory::find($id);

        } else {
            $category = new CustomPageCategory();
        }
        $category->name = $request->input('name');
        $category->order = $order ?: 0;
        $category->parent_id = $parentId > 0 ? $parentId : null;
        $category->save();
        return redirect()->route('pages.index');
    }

    public function edit(int $id)
    {
        $page = CustomPageCategory::findOrFail($id);
        $pages = CustomPageCategory::all();
        return view('pages.edit')->withPage($page)->withPages($pages);
    }

    public function delete(int $id)
    {
        $page = CustomPageCategory::findOrFail($id);
        $this->deleteRecursive($page);
        return redirect()->route('pages.index');
    }

    private function deleteRecursive($category)
    {
        if ($category->childrens()->count() > 0) {
            foreach ($category->childrens as $children) {
                $this->deleteRecursive($children);
            }
        }
        $category->delete();
    }
}

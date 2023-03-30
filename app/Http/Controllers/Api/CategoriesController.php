<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeCategoryImage;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriesController extends Controller
{
    //

    /**
     * @return ResponseFactory|Response
     */
    public function getCategoriesDetails()
    {
        $categories = Category::withCount('chimneyAttributes')->get();
        return response($categories->toJson());
    }

    public function getCategoryDetails(Request $request)
    {
        $category = Category
            ::with([
                'chimneyAttributes' => function ($q) {
                    $q->with('options');
                }
            ])
            ->find((int)$request->input('category'));

        return response($category?->toJson() ?? []);
    }

    public function changeImage(ChangeCategoryImage $request)
    {
        $category = Category::find($request->validated('category'));
        $image = $request->file('image')->store('public/images');
        $category->img = '/' . str_replace('public', 'storage', $image);

        $category->save();

        return response($category->toJson());
    }

    public function updateCategory(UpdateCategoryRequest $request)
    {
        $data = $request->validated();
        $category = Category::findorfail($request->validated('category'));
        $category->update($data);

        return response($category->toJson());
    }

    public function create(CreateCategoryRequest $request)
    {
        $category = Category::create($request->validated() + [
                'is_visible' => true,
                'priority' => 0,
                'img' => 'https://via.placeholder.com/150',
                'artificially_created' => true,
            ]);

        $category->rewrite = $request->validated('name');
        $category->save();

        return response($category->toJson());
    }

    public function delete(Category $category)
    {
        $category->delete();

        return response('Category deleted', 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeCategoryImage;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\GetCategoryDetailsRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\Categories;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psy\Util\Json;

class CategoriesController extends Controller
{
    public function getCategoriesDetails(): JsonResponse
    {
        $categories = Category::withCount('chimneyAttributes')->get();

        return response()->json($categories);
    }

    public function getCategoryDetails(GetCategoryDetailsRequest $request): JsonResponse
    {
        $category = Categories::getCategoryWithAllChimneyAttributesOptions(
            $request->validated('category')
    );

        return response()->json($category ?? []);
    }

    public function changeImage(ChangeCategoryImage $request): JsonResponse
    {
        $category = Category::find($request->validated('category'));
        $image = $request->file('image')->store('public/images');
        $category->img = '/' . str_replace('public', 'storage', $image);

        $category->save();

        return response()->json($category);
    }

    public function updateCategory(UpdateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $category = Category::findorfail($request->validated('category'));
        $category->update($data);

        return response()->json($category);
    }

    public function create(CreateCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated() + [
                'is_visible' => true,
                'priority' => 0,
                'img' => 'https://via.placeholder.com/150',
                'artificially_created' => true,
            ]);

        $category->rewrite = $request->validated('name');
        $category->save();

        return response()->json($category);
    }

    public function delete(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json('Category deleted', 201);
    }
}

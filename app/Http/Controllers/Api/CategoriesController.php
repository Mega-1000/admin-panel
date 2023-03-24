<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Http\Controllers\Controller;
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
}

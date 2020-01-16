<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    //

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
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
            ->find((int) $request->input('category'));

        return response($category->toJson());
    }
}

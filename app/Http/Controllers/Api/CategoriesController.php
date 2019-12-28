<?php

namespace App\Http\Controllers\Api;

use App\Entities\CategoryDetail;
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
        $categories = CategoryDetail::withCount('chimneyAttributes')->get();
        return response($categories->toJson());
    }

    public function getCategoryDetails(Request $request)
    {
        $category = $request->input('category');
        $categoryDetails = CategoryDetail
            ::where('category_navigation', 'like', '%' . $category . '%')
            ->with('product')
            ->with([
                'chimneyAttributes' => function ($q) {
                    $q->with('options');
                }
            ])
            ->first();
        $categoryDetails->name = $categoryDetails->product ? $categoryDetails->product->name : '';

        return response($categoryDetails->toJson());
    }
}

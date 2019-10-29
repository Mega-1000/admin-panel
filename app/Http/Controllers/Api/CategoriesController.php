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
        return response(DB::table('category_details')->get()->toJson());
    }

    public function getCategoryDetails(Request $request)
    {
        $category = $request->input('category');
        $categoryDetails = DB::table('category_details as cd')
            ->select(
                'cd.id',
                'cd.category',
                'cd.category_edited',
                'cd.description',
                'cd.img_url',
                'cd.url_for_website',
                'cd.category_navigation',
                'p.name',
                'cd.token_prod_cat'
                )
            ->where('cd.category_navigation', 'like', '%' . $category . '%')
            ->leftJoin('products as p', 'p.token_prod_cat', '=','cd.token_prod_cat')
            ->first();

        return response(json_encode($categoryDetails));
    }
}

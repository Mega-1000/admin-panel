<?php

namespace App\Http\Controllers\Api;

use App\Entities\ProductOpinion;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductOpinionController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $opinion = new ProductOpinion();
        $opinion->fill($request->all());
        $opinion->save();

        return response()->json([
            'success' => true,
        ]);
    }
}

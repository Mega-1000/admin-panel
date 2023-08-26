<?php

namespace App\Http\Controllers\Api;

use App\Entities\Product;
use App\Helpers\PackageDivider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackageController extends Controller
{
    use ApiResponsesTrait;

    public function countPackages(Request $request): JsonResponse
    {
        $responseArray = collect($request->all());

        $prodIds = [];
        foreach ($responseArray as $items) {
            $prodIds [] = $items['id'];
        }

        $prodList = Product::whereIn('id', $prodIds)->with('tradeGroups')->with('price')->get();

        $prodList->map(function ($item) use ($responseArray) {
            $product = $responseArray->where('id', $item->id)->first();
            $item->quantity = $product['amount'];
        });

        $warehouse = new PackageDivider();
        $warehouse->setItems($prodList);

        return response()->json($warehouse->divide());
    }
}

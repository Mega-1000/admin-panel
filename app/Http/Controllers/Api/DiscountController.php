<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService $discountService,
    ) {}

    /**
     * @param Category $category
     * @return mixed
     */
    public function getByCategory(Category $category): JsonResponse
    {
        return response()->json(
            $category->discounts()->with('product')->get(),
        );
    }

    /**
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        return response()->json(
            $this->discountService->getCategories(),
        );
    }
}

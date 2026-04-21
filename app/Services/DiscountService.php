<?php

namespace App\Services;

use App\DTO\Discounts\DiscountDTO;
use App\Entities\Discount;
use Illuminate\Support\Collection;

class DiscountService
{

    /**
     * @param Discount $discount
     * @param DiscountDTO $data
     * @return void
     */
    public function update(Discount $discount, DiscountDTO $data): void
    {
        $discount->update($data->toArray());

        $discount->product->price()->update([
            'gross_selling_price_commercial_unit' => $data->getNewAmount(),
        ]);
    }

    /**
     * @param DiscountDTO $data
     * @return Discount
     */
    public function create(DiscountDTO $data): Discount
    {
        return Discount::create($data->toArray());
    }

    /**
     * @param Discount $discount
     * @return void
     */
    public function delete(Discount $discount): void
    {
        $discount->product->price()->update([
            'gross_selling_price_commercial_unit' => $discount->old_price,
        ]);

        $discount->delete();
    }

    public function getCategories(): Collection
    {
        $discounts = Discount::with('product')->get();
        $categories = collect();

        foreach ($discounts as $discount) {
            $category = $discount->product->category;
            $categories->push($category);
        }

        return $categories->unique('id')->flatten();
    }
}

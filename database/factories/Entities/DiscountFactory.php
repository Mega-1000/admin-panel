<?php

namespace Database\Factories\Entities;

use App\Entities\Discount;
use App\Entities\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'description' => $this->faker->text(),
            'new_amount' => $this->faker->randomFloat(2, 0, 9999999999999999.99),
            'old_amount' => $this->faker->randomFloat(2, 0, 9999999999999999.99),
        ];
    }
}

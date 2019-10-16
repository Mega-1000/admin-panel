<?php

use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($j = 1; $j<=15; $j++) {
            for ($i = 1; $i <= rand(1, 3); $i++) {
                \App\Entities\ProductPrice::create([
                    'product_id' => $j,
                    'net_purchase_price_commercial_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_commercial_unit_after_discounts' => $faker->randomFloat(2, 1, 100),
                    'net_special_price_commercial_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_basic_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_basic_unit_after_discounts' => $faker->randomFloat(2, 1, 100),
                    'net_special_price_basic_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_calculated_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_calculated_unit_after_discounts' => $faker->randomFloat(2, 1, 100),
                    'net_special_price_calculated_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_aggregate_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_aggregate_unit_after_discounts' => $faker->randomFloat(2, 1, 100),
                    'net_special_price_aggregate_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_the_largest_unit' => $faker->randomFloat(2, 1, 100),
                    'net_purchase_price_the_largest_unit_after_discounts' => $faker->randomFloat(2, 1, 100),
                    'net_special_price_the_largest_unit' => $faker->randomFloat(2, 1, 100),
                    'net_selling_price_commercial_unit' => $faker->randomFloat(2, 1, 100),
                    'net_selling_price_basic_unit' => $faker->randomFloat(2, 1, 100),
                    'net_selling_price_calculated_unit' => $faker->randomFloat(2, 1, 100),
                    'net_selling_price_aggregate_unit' => $faker->randomFloat(2, 1, 100),
                    'net_selling_price_the_largest_unit' => $faker->randomFloat(2, 1, 100),
                    'discount1' => $faker->randomFloat(2, 1, 100),
                    'discount2' => $faker->randomFloat(2, 1, 100),
                    'discount3' => $faker->randomFloat(2, 1, 100),
                    'bonus1' => $faker->randomFloat(2, 1, 100),
                    'bonus2' => $faker->randomFloat(2, 1, 100),
                    'bonus3' => $faker->randomFloat(2, 1, 100),
                    'gross_price_of_packing' => $faker->randomFloat(2, 1, 100),
                    'table_price' => $faker->randomFloat(2, 1, 100),
                    'vat' => $this->getVat($i),
                    'additional_payment_for_milling' => $faker->randomFloat(2, 1, 100),
                    'coating' => $faker->randomFloat(2, 1, 100),
                ]);
            }
        }
    }

    protected function getVat($number) {
        switch ($number) {
            case 3:
                return 18;
            case 2:
                return 9;
            default:
                return 23;
        }
    }
}

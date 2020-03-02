<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 15; $i++) {
            $product = \App\Entities\Product::create([
                'category_id' => $faker->numberBetween(1, 10),
                'symbol' => $faker->text(15),
                'name' => $faker->streetName,
                'multiplier_of_the_number_of_pieces' => $faker->numberBetween(0, 10),
                'url' => $faker->url,
                'weight_trade_unit' => $faker->randomFloat(2, 1, 1000),
                'weight_collective_unit' => $faker->randomFloat(2, 1, 1000),
                'weight_biggest_unit' => $faker->randomFloat(2, 1, 1000),
                'weight_base_unit' => $faker->randomFloat(2, 1, 1000),
                'description' => $faker->text,
                'video_url' => $faker->url,
                'manufacturer_url' => $faker->url,
                'priority' => $faker->numberBetween(1, 150),
                'meta_price' => $faker->text(55),
                'meta_description' => $faker->text,
                'meta_keywords' => $faker->words(7, true),
                'status' => rand(1, 30) % 3 === 0 ? 'ACTIVE' : 'PENDING',
                'description_photo_promoted' => $faker->text,
                'description_photo_table' => $faker->text,
                'description_photo_contact' => $faker->text,
                'description_photo_details' => $faker->text,
                'set_symbol' => $faker->text(20),
                'set_rule' => $faker->text(15),
                'manufacturer' => $faker->company,
                'additional_info1' => $faker->text,
                'additional_info2' => $faker->text,
                'supplier_product_name' => $faker->text(40),
                'product_name_on_collective_box' => $faker->text(100),
                'product_name_supplier' => $faker->text(20),
                'product_name_supplier_on_documents' => $faker->text(30),
                'supplier_product_symbol' => $faker->text(20),
                'product_name_manufacturer' => $faker->company,
                'symbol_name_manufacturer' => $faker->company,
                'pricelist_name' => $faker->words(3, true),
                'calculator_type' => $faker->text(20),
                'product_group' => $faker->text(30),
                'price_change_date' => $faker->dateTimeThisYear(),
            ]);

            for ($k = 1; $k <= rand(1, 3); $k++) {
                \App\Entities\ProductPacking::create([
                    'product_id' => $product->id,
                    'calculation_unit' => 'szt',
                    'unit_consumption' => '1',
                    'unit_commercial' => 'szt',
                    'unit_basic' => 'szt',
                    'numbers_of_basic_commercial_units_in_pack' => 1,
                    'courier_volume_factor' => 0,
                    'max_pieces_in_one_package' => 1,
                    'number_of_volume_items_for_paczkomat' => 0,
                ]);
            }

            $productStock = \App\Entities\ProductStock::create([
                'product_id' => $product->id,
                'quantity' => '100',
                'min_quantity' => $faker->numberBetween(1, 5),
                'unit' => 'szt',
                'start_quantity' => $faker->numberBetween(6, 10),
                'number_on_a_layer' => $faker->numberBetween(1, 10)
            ]);

            for ($j = 1; $j <= 5; $j++) {
                $productStockPosition = \App\Entities\ProductStockPosition::create([
                    'product_stock_id' => $productStock->id,
                    'lane' => $faker->numberBetween(1, 10),
                    'bookstand' => $faker->numberBetween(1, 10),
                    'shelf' => $faker->numberBetween(1, 10),
                    'position' => $faker->numberBetween(1, 10),
                    'position_quantity' => '20'
                ]);

                \App\Entities\ProductStockLog::create([
                    'product_stock_id' => $productStock->id,
                    'product_stock_position_id' => $productStockPosition->id,
                    'action' => rand(1, 30) % 3 === 0 ? 'ADD' : 'DELETE',
                    'quantity' => rand(1,5),
                    'user_id' => $j
                ]);
            }
        }
    }
}

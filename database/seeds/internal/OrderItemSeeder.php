<?php

use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($i = 1; $i<=10; $i++) {
            for($j = 1; $j<=rand(1, 15); $j++) {
                \App\Entities\OrderItem::create([
                    'order_id' => $i,
                    'product_id' => $faker->numberBetween(1, 15),
                    'price' => $faker->randomFloat(2, 1, 1000),
                    'quantity' => $faker->numberBetween(1, 100),
                ]);
            }
        }
    }
}

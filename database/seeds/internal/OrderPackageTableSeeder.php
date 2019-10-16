<?php

use Illuminate\Database\Seeder;
use App\Entities\OrderPackage;

class OrderPackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for ($i = 1; $i <= 10; $i++) {
            OrderPackage::create([
                'order_id' => $i,
                'size_a' => $faker->randomFloat('2', 1, 100),
                'size_b' => $faker->randomFloat('2', 1, 100),
                'size_c' => $faker->randomFloat('2', 1, 100),
                'shipment_date' => $faker->date('Y-m-d'),
                'delivery_date' => $faker->date('Y-m-d'),
                'service_courier_name' => 'DPD',
                'delivery_courier_name' => 'DPD',
                'weight' => $faker->randomFloat('2',1,25),
                'cash_on_delivery' => $faker->randomFloat('2',1,25),
                'notices' => $faker->text(),
                'status' => 'CANCALLED',
                'sending_number' => '21312312312',
                'letter_number' => '123123123',
                'cost_for_client' => $faker->randomFloat('2',1,25),
                'cost_for_company' => $faker->randomFloat('2',1,25),
                'real_cost_for_company' => $faker->randomFloat('2',1,25),
            ]);
        }
    }
}

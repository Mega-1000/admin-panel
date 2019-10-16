<?php

use App\Entities\Order;
use App\Entities\OrderAddress;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
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
            $order = Order::create([
                'customer_id' => $i,
                'status_id' => $faker->numberBetween(1, 3),
                'last_status_update_date' => $faker->dateTime(),
                'total_price' => $faker->randomFloat(2, 300, 1000),
                'weight' => $faker->randomFloat(2, 0.2, 250),
                'shipment_price_for_client' => $faker->randomFloat(2, 0, 50),
                'shipment_price_for_us' => $faker->randomFloat(2, 0, 50),
                'customer_notices' => $faker->text(191),
                'cash_on_delivery_amount' => $faker->randomFloat(2, 1, 300),
                'allegro_transaction_id' => $faker->numberBetween(1, 99999),
                'employee_id' => null,
                'warehouse_id' => $faker->numberBetween(1, 10),
                'additional_service_cost' => $faker->randomFloat(2, 0, 100),
                'invoice_warehouse_file' => $faker->text(50) . ".pdf",
                'document_number' => $faker->numberBetween(1, 1000),
                'consultant_earning' => $faker->randomFloat(2, 1, 100),
                'warehouse_cost' => $faker->randomFloat(2, 1, 100),
                'printed' => rand(1, 30) % 3 === 0 ? 1 : "",
                'correction_description' => $faker->text(),
                'correction_amount' => $faker->randomFloat(2, 0, 100),
                'packing_warehouse_cost' => $faker->randomFloat(2, 0, 100),
                'rating' => $faker->numberBetween(1, 10),
                'rating_message' => $faker->text(),
                'shipping_abroad' => rand(1, 30) % 3 === 0 ? true : false,
            ]);

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'INVOICE_ADDRESS',
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'firmname' => $faker->company,
                'nip' => $faker->numberBetween(111111111, 999999999),
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'flat_number' => $faker->numberBetween(1, 99),
                'city' => $faker->city,
                'postal_code' => $faker->postcode,
                'email' => $faker->email,
            ]);

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'DELIVERY_ADDRESS',
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'firmname' => $faker->company,
                'nip' => $faker->numberBetween(111111111, 999999999),
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'flat_number' => $faker->numberBetween(1, 99),
                'city' => $faker->city,
                'postal_code' => $faker->postcode,
                'email' => $faker->email,
            ]);
        }


    }
}

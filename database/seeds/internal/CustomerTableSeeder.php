<?php

use Illuminate\Database\Seeder;
use App\Entities\Customer;
use App\Entities\CustomerAddress;

class CustomerTableSeeder extends Seeder
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
            $customer = Customer::create([
                'id_from_old_db' => null,
                'login' => $faker->name,
                'password' => bcrypt('password'),
                'nick_allegro' => null,
                'status' => 'ACTIVE'
            ]);

            CustomerAddress::create([
                'customer_id' => $customer->id,
                'type' => 'STANDARD_ADDRESS',
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

            CustomerAddress::create([
                'customer_id' => $customer->id,
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

            CustomerAddress::create([
                'customer_id' => $customer->id,
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

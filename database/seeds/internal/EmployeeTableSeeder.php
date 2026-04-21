<?php

use Illuminate\Database\Seeder;
use App\Entities\Employee;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 5; $i++) {
            Employee::create([
                'firm_id' => $i,
                'warehouse_id' => null,
                'email' => $faker->email,
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'phone' => '48'.$faker->numberBetween(111111111,999999999),
                'job_position' => 'CONSULTANT',
                'comments' => $faker->text,
                'additional_comments' => $faker->text,
                'postal_code' => $faker->postcode,
                'radius' => $faker->numberBetween('1', '100'),
                'status' => 'ACTIVE',
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Employee::create([
                'firm_id' => null,
                'warehouse_id' => $i,
                'email' => $faker->email,
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'phone' => '48'.$faker->numberBetween(111111111,999999999),
                'job_position' => 'SECRETARIAT',
                'comments' => $faker->text,
                'additional_comments' => $faker->text,
                'postal_code' => $faker->postcode,
                'radius' => $faker->numberBetween('1', '100'),
            ]);
        }


        for ($i = 1; $i <= 5; $i++) {
            Employee::create([
                'firm_id' => $i + 1,
                'warehouse_id' => $i,
                'email' => $faker->email,
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'job_position' => 'STOREKEEPER',
                'phone' => '48'.$faker->numberBetween(111111111,999999999),
                'comments' => $faker->text,
                'additional_comments' => $faker->text,
                'postal_code' => $faker->postcode,
                'radius' => $faker->numberBetween(1, 100),
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Employee::create([
                'firm_id' => $i,
                'warehouse_id' => $i + 1,
                'email' => $faker->email,
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'phone' => '48'.$faker->numberBetween(111111111,999999999),
                'job_position' => 'SALES',
                'comments' => $faker->text,
                'additional_comments' => $faker->text,
                'postal_code' => $faker->postcode,
                'radius' => $faker->numberBetween(1, 100),
            ]);
        }
    }
}

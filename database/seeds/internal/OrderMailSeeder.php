<?php

use App\Entities\OrderMail;
use Illuminate\Database\Seeder;

class OrderMailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($i = 1; $i<=30; $i++) {
            OrderMail::create([
                'order_id' => $faker->numberBetween(1, 10),
                'mail_subject' => $faker->text(rand(5, 100)),
                'mail_content' => $faker->text(rand(50, 1500)),
            ]);
        }
    }
}

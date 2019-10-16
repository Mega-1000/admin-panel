<?php

use Illuminate\Database\Seeder;
use App\Entities\Label;

class LabelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        Label::create([
            'name' => 'Test',
            'color' => $faker->hexColor,
        ]);
    }
}

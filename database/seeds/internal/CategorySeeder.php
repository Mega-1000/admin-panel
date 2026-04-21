<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
            \App\Entities\Category::create([
                'name' => $faker->word,
                'status' => rand(1, 30) % 3 === 0 ? 'ACTIVE' : 'PENDING',
                'url' => $faker->url,
            ]);
        }
    }
}

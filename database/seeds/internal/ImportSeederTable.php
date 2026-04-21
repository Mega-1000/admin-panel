<?php
namespace Database\Seeders\Internal;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        DB::table('import')->insert(
            [
                'name' => 'Import products',
                'last_import' => $faker->dateTime()
            ]
        );
        DB::table('import')->insert(
            [
                'name' => 'Import products',
                'last_import' => $faker->dateTime()
            ]
        );
    }
}

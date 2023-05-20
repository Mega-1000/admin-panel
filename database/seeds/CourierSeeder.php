<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('courier')->insert([
            [
                'courier_name' => 'Paczkomat',
                'courier_key' => 'INPOST',
                'item_number' => 1,
                'active' => 1,
            ],
            [
                'courier_name' => 'ALLEGRO-INPOST',
                'courier_key' => 'ALLEGRO_INPOST',
                'item_number' => 2,
                'active' => 0,
            ],
            [
                'courier_name' => 'Dpd',
                'courier_key' => 'DPD',
                'item_number' => 3,
                'active' => 1,
            ],
            [
                'courier_name' => 'Apaczka',
                'courier_key' => 'APACZKA',
                'item_number' => 4,
                'active' => 0,
            ],
            [
                'courier_name' => 'Pocztex',
                'courier_key' => 'POCZTEX',
                'item_number' => 5,
                'active' => 1,
            ],
            [
                'courier_name' => 'Jas',
                'courier_key' => 'JAS',
                'item_number' => 6,
                'active' => 0,
            ],
            [
                'courier_name' => 'Gls',
                'courier_key' => 'GLS',
                'item_number' => 7,
                'active' => 1,
            ],
            [
                'courier_name' => 'Odbiór osobisty',
                'courier_key' => 'ODBIOR_OSOBISTY',
                'item_number' => 8,
                'active' => 1,
            ],
            [
                'courier_name' => 'Giełda',
                'courier_key' => 'GIELDA',
                'item_number' => 9,
                'active' => 1,
            ],
            [
                'courier_name' => 'DB Schenker',
                'courier_key' => 'DB_SCHENKER',
                'item_number' => 10,
                'active' => 1,
            ],
            [
                'courier_name' => 'Paczkomat old',
                'courier_key' => 'PACZKOMAT',
                'item_number' => 11,
                'active' => 0,
            ]
        ]);
    }
}

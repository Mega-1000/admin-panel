<?php

use Illuminate\Database\Seeder;
use App\Entities\Firm;
use App\Entities\FirmAddress;

class FirmTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $firm = Firm::create([
            'name' => 'Mega1000',
            'short_name' => 'Mega1000',
            'email' => 'ebudownictwo@wp.pl',
        ]);

        FirmAddress::create([
            'firm_id' => $firm->id,
        ]);
    }
}

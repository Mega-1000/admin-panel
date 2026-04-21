<?php

use Illuminate\Database\Seeder;
use App\Entities\Warehouse;
use App\Entities\WarehouseAddress;
use App\Entities\WarehouseProperty;

class WarehouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouse = Warehouse::create([
            'firm_id' => 1,
            'symbol' => 'Mega 1000',
            'status' => 'ACTIVE',
        ]);

        WarehouseAddress::create([
            'warehouse_id' => $warehouse->id,
        ]);

        WarehouseProperty::create([
            'warehouse_id' => $warehouse->id,
        ]);
    }
}

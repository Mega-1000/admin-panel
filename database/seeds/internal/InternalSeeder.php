<?php
namespace Database\Seeders\Internal;

use Illuminate\Database\Seeder;

class InternalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        //$this->call(FirmTableSeeder::class);
        //gi$this->call(WarehouseTableSeeder::class);
        //$this->call(EmployeeTableSeeder::class);
        //$this->call(LabelTableSeeder::class);
        //$this->call(StatusTableSeeder::class);
        //$this->call(CustomerTableSeeder::class);
        //$this->call(TagTableSeeder::class);
        //$this->call(OrderTableSeeder::class);
        //$this->call(OrderMailSeeder::class);
        //$this->call(CategorySeeder::class);
        //$this->call(ProductSeeder::class);
        //$this->call(OrderItemSeeder::class);
        //$this->call(ProductPriceSeeder::class);
        //$this->call(OrderPackageTableSeeder::class);
        //$this->call(OauthClientsSeeder::class);
        $this->call(ImportSeederTable::class);
    }
}

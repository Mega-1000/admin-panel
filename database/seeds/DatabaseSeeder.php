<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(VoyagerDatabaseSeeder::class);
        $this->call(InternalSeeder::Class);
        $this->call(BanksDataSeeder::class);
        $this->call(DumpedTablesSeeder::class);
    }
}

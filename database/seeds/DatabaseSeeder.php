<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Seeders\Voyager\VoyagerDatabaseSeeder;
use Database\Seeders\Internal\InternalSeeder;
use Illuminate\Support\Facades\DB;

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
        $this->call(InternalSeeder::class);
        $this->call(BanksDataSeeder::class);
        $this->call(DumpedTablesSeeder::class);
        $this->call(PackageTemplateSeeder::class);
        $this->call(CourierSeeder::class);
        DB::unprepared(file_get_contents(database_path('seeds/SelTables.sql')));
    }
}

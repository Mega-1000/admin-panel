<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DumpedTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents(database_path("seeds/dump.json"));
        $data = json_decode($data, true);
        DB::table('statuses')->delete();
        DB::table('statuses')->insert($data['statuses']);
        DB::table('label_groups')->delete();
        DB::table('label_groups')->insert($data['label_groups']);
        DB::table('labels')->delete();
        DB::table('labels')->insert($data['labels']);
        DB::table('label_labels_to_add_after_removal')->delete();
        DB::table('label_labels_to_add_after_removal')->insert($data['label_labels_to_add_after_removal']);
        DB::table('label_labels_to_remove_after_addition')->delete();
        DB::table('label_labels_to_remove_after_addition')->insert($data['label_labels_to_remove_after_addition']);
        DB::table('label_labels_to_add_after_addition')->delete();
        DB::table('label_labels_to_add_after_addition')->insert($data['label_labels_to_add_after_addition']);
        DB::table('menu_items')->delete();
        DB::table('menu_items')->insert($data['menu_items']);
        DB::table('menus')->delete();
        DB::table('menus')->insert($data['menus']);
    }
}

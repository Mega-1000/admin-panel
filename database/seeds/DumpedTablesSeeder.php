<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DumpedTablesSeeder extends Seeder
{
    private $data;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = file_get_contents(database_path("seeds/dump.json"));
        $data = json_decode($data, true);
        $this->data = $data;
        $this->refresh('statuses');
        $this->refresh('label_groups');
        $this->refresh('labels');
        $this->refresh('label_labels_to_add_after_removal');
        $this->refresh('label_labels_to_remove_after_addition');
        $this->refresh('label_labels_to_add_after_addition');
    }

    private function refresh($table)
    {
        DB::table($table)->delete();
        if (!empty($this->data[$table])) {
            DB::table($table)->insert($this->data[$table]);
        }
    }
}

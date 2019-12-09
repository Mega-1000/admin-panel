<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\AutomaticMigratorHelper;
use Illuminate\Support\Facades\DB;

class AutomaticMigration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $statuses = DB::table('statuses')->get();
        $labels_group = DB::table('label_groups')->get();
        $labels = DB::table('labels')->get();
        $label_labels_to_add_after_removal = DB::table('label_labels_to_add_after_removal')->get();
        $label_labels_to_remove_after_addition = DB::table('label_labels_to_remove_after_addition')->get();
        $label_labels_to_add_after_addition = DB::table('label_labels_to_add_after_addition')->get();


        $dump = [
            'statuses' => $statuses,
            'labels_group' => $labels_group,
            'labels' => $labels,
            'label_labels_to_add_after_removal' => $label_labels_to_add_after_removal,
            'label_labels_to_remove_after_addition' => $label_labels_to_remove_after_addition,
            'label_labels_to_add_after_addition' => $label_labels_to_add_after_addition
        ];
        file_put_contents(database_path("seeds/dump.php"), '<?php return ' . var_export($dump, true) . ';');
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
        $label_groups = DB::table('label_groups')->get();
        $labels = DB::table('labels')->get();
        $label_labels_to_add_after_removal = DB::table('label_labels_to_add_after_removal')->get();
        $label_labels_to_remove_after_addition = DB::table('label_labels_to_remove_after_addition')->get();
        $label_labels_to_add_after_addition = DB::table('label_labels_to_add_after_addition')->get();

        $dump = [
            'statuses' => $statuses ?? '',
            'label_groups' => $label_groups ?? '',
            'labels' => $labels ?? '',
            'label_labels_to_add_after_removal' => $label_labels_to_add_after_removal ?? '',
            'label_labels_to_remove_after_addition' => $label_labels_to_remove_after_addition ?? '',
            'label_labels_to_add_after_addition' => $label_labels_to_add_after_addition ?? ''
        ];
        $dumpJSON = json_encode($dump, JSON_PRETTY_PRINT);

        $path = database_path("seeds/dump.json");
        $key = env('GIT_BOT_KEY');
        $login = env('GIT_BOT_LOGIN');
        $origin = "https://$login:$key@github.com/DawidZwiewka/mega-1000-backend.git";
        $branch = "automatic-db-migration-test";

        file_put_contents($path, $dumpJSON);

        shell_exec("git checkout -b $branch");
        shell_exec("git add " . $path);
        shell_exec('git commit -m "automatic update seeder"');

        shell_exec("git push -u $origin $branch -f");
        shell_exec("git checkout master");
        shell_exec("git branch -D $branch");
    }
}

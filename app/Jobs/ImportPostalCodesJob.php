<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ImportPostalCodesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->path = Storage::path('public/kody.csv');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $handle = fopen($this->path, 'r');
        $i = 1;
        while ($line = fgetcsv($handle, '', ';')) {
            if ($i > 1) {
                $latitudeExp = explode(',', $line[1]);
                $longitudeExp = explode(',', $line[2]);
                $array = [
                    'postal_code' => $line[0],
                    'latitude' => number_format($latitudeExp[0].'.'.$latitudeExp[1],4,'.', ''),
                    'longitude' => number_format($longitudeExp[0].'.'.$longitudeExp[1],4,'.', '')
                ];
                $postalCode = DB::table('postal_code_lat_lon')->where('postal_code', $array['postal_code'])->first();
                if($postalCode === null) {
                    DB::table('postal_code_lat_lon')->insert($array);
                }
            }
            var_dump($i);
            $i++;
        }
    }
}

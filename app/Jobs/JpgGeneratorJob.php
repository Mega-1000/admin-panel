<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\Browsershot\Browsershot;

class JpgGeneratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
        $rawData = \App\Entities\JpgDatum::orderBy('order')->get();
        $data = [];

        foreach ($rawData as $row) {
            $data[$row->filename]['rows'][$row->row][$row->col][$row->subcol] = [
                'price' => number_format($row->price, 2, ',', ''),
                'image' => $row->image,
                'name' => $row->name
            ];
            $data[$row->filename]['cols'][$row->col][$row->subcol] = true;
        }

        foreach ($data as $fileName => $fileData) {
            $fileData['hasSubcolumns'] = $this->hasSubcolumns($fileData['cols']);

            Browsershot
                ::html(
                    view('jpg/table', $fileData)
                    ->render()
                )
                ->windowSize(9999, 9999)
                ->select('table')
                ->save(storage_path('app/public/products/'.$fileName.'.jpg'))
            ;

            Browsershot
                ::html(
                    view('jpg/products', $fileData)
                    ->render()
                )
                ->windowSize(9999, 9999)
                ->select('table')
                ->delay(2000)
                ->save(storage_path('app/public/products/'.$fileName.'r.jpg'))
            ;
        }
    }

    private function hasSubcolumns($cols)
    {
        foreach ($cols as $subcols) {
            if (count($subcols) > 1) {
                return true;
            }
        }
        return false;
    }
}

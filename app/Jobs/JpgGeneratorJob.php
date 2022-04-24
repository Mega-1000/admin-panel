<?php

namespace App\Jobs;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

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

            $colsCount = count($fileData['cols'], COUNT_RECURSIVE);

            if ($colsCount < 30) {
                $size = 'a4';
            } else {
                $size = 'a2';
            }

            $pdf = PDF::loadView('jpg.table', [
                'hasSubcolumns' => $fileData['hasSubcolumns'],
                'cols' => $fileData['cols'],
                'rows' => $fileData['rows']
            ])->setPaper($size, 'landscape');

            $path = storage_path('app/public/products/' . $fileName . '.pdf');
            $pdf->save($path);

            $pdf = PDF::loadView('jpg.products', [
                'hasSubcolumns' => $fileData['hasSubcolumns'],
                'cols' => $fileData['cols'],
                'rows' => $fileData['rows']
            ])->setPaper('a4');

            $path = storage_path('app/public/products/' . $fileName . '-related-offers.pdf');
            $pdf->save($path);
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

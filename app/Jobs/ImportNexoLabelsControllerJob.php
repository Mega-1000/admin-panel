<?php

namespace App\Jobs;

use App\Entities\Label;
use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use App\Repositories\OrderRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportNexoLabelsControllerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file;

    private $orderRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
        $this->orderRepository = app(OrderRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $header = [];

        $orders = $this->orderRepository
            ->where([["created_at", ">", '2021-06-01']])
            ->whereHas('labels', function ($query) {
                $query->where('label_id', 137);
            })->orWhereHas('labels', function ($query) {
                $query->where('label_id', 206);
            })->orWhereHas('labels', function ($query) {
                $query->where('label_id', 207);
            })
            // ->toSql();
            ->get();
        dd($orders);
        foreach ($orders as $order) {
            dispatch_now(new RemoveLabelJob($order, [137, 206, 207]));
        }

        dd($orders);

        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 3000, ';')) !== FALSE) {
                if (!$header) {
                    foreach ($row as &$headerName) {
                        $headerName = $headerName;
                    }
                    $header = $row;
                } else {
                    if (is_numeric($row[0])) {
                        $order = $this->orderRepository->find($row[0]);
                        dd($order);
                    }
                }
            }

            fclose($handle);
        }
    }
}

<?php

namespace App\Jobs;

use Carbon\Carbon;
use DateTime;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Repositories\OrderRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportNexoLabelsControllerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file;

    private $orderRepository;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $header = $verificatedOrders = [];
        $this->orderRepository = app(OrderRepository::class);
        $this->file = Storage::path('user-files/nexo-controller.csv');

        $orders = $this->orderRepository
            ->where([["created_at", ">", '2022-06-01']])
            ->whereHas('labels', function ($query) {
                $query->where('label_id', 137);
            })->orWhereHas('labels', function ($query) {
                $query->where('label_id', 206);
            })->orWhereHas('labels', function ($query) {
                $query->where('label_id', 207);
            })
            ->get();

        try {
            if (($handle = fopen($this->file, 'r')) !== false) {
                while (($row = fgetcsv($handle, 3000, ';')) !== false) {
                    $row = explode(',', $row[0], 3);
                    $labelsToAdd = [];
                    if (!$header) {
                        foreach ($row as &$headerName) {
                            $headerName = $headerName;
                        }
                        $header = $row;
                    } else {
                        if (is_numeric($row[0])) {
                            $order = $this->orderRepository->find($row[0]);

                            if ($order === null || $order->created_at < '2022-06-01') {
                                continue;
                            }

                            $labelsToAdd[] = 137;
                            if ($order->getSumOfGrossValues() !==  (float)str_replace(',', '.', $row[1])) {
                                $labelsToAdd[] = 206;
                            }

                            if ($order->hasLabel(66) && !$order->hasLabel(137)) {
                                $labelsToAdd[] = 207;
                            }

                            $orderDate = new Carbon($order->preferred_invoice_date);
                            $date = new Carbon($row[2]);

                            if ($orderDate->format('Y-m') !== $date->format('Y-m')) {
                                $labelsToAdd[] = 210;
                            }

                            if ($order->hasLabel(177) || !empty($order->allegro_form_id)) {
                                if (empty($order->allegro_payment_id) || empty($order->allegro_form_id) || $order->sum_of_gross_values === 0) {
                                    $labelsToAdd[] = 208;
                                }
                            }

                            $verificatedOrders[$order->id] = $labelsToAdd;
                        }
                    }
                }

                fclose($handle);
            }
        } catch (Throwable $ex) {
            Log::error(
                'Problem with nexo controller import' . $ex->getMessage(),
                [
                    'class' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'orderId' => $row[0]
                ]
            );
        }

        foreach ($orders as $order) {
            dispatch(new RemoveLabelJob($order, [137, 206, 207, 208, 209, 210]));
        }

        foreach ($verificatedOrders as $orderId => $labelsToAdd) {
            dispatch(new AddLabelJob($this->orderRepository->find($orderId), $labelsToAdd));
        }

        Storage::disk()->delete('user-files/nexo-controller.csv');
    }
}

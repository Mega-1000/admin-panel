<?php

namespace App\Jobs;

use App\Repositories\ProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class DeleteProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ProductRepository $repository)
    {
        $products = $repository->all();
        foreach ($products as $product) {
            if (strpos($product->symbol, '-')) {
                $valExp = explode('-', $product->symbol);
                if (end($valExp) > 0) {
                    $product->packing->delete();
                    if (!empty($product->photos)) {
                        foreach ($product->photos as $photo) {
                            $photo->delete();
                        }
                    }
                    $product->price->delete();
                    if (!empty($product->stock->position)) {
                        foreach ($product->stock->position as $position) {
                            $position->delete();
                        }
                    }
                    if (!empty($product->stock->logs)) {
                        foreach ($product->stock->logs as $log) {
                            $log->delete();
                        }
                    }
                    $product->stock->delete();
                    $product->delete();
                }
            }
        }
    }
}

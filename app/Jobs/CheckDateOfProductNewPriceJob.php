<?php

namespace App\Jobs;

use App\Entities\Product;
use App\Services\ProductPriceCalculator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckDateOfProductNewPriceJob implements ShouldQueue
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
    public function handle()
    {
        $calculator = new ProductPriceCalculator();

        $products = Product::with(['packing', 'price', 'parentProduct'])
            ->whereDate('date_of_the_new_prices', '<=', Carbon::today()->addDay())
            ->get();

        foreach ($products as $product) {
            if (!$product->packing || !$product->price) {
                Log::warning('CheckDateOfProductNewPriceJob: missing packing or price', ['product_id' => $product->id]);
                continue;
            }

            $prices = $calculator->buildFullPriceArray($product);
            $product->price->update($prices);
        }
    }
}

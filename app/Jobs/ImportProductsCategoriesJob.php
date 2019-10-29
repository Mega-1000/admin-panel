<?php

namespace App\Jobs;

use App\Entities\Category;
use App\Entities\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\CategoryRepositoryEloquent;
use App\Repositories\ProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportProductsCategoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product
     */
    protected $products;

    /**
     * @var Category
     */
    protected $categories;

    /**
     * @var DB
     */
    protected $db;

    public function handle()
    {
        $categoriesList = DB::table('products')
            ->select(DB::raw('distinct(product_url)'))
            ->whereRaw('char_length(product_url) > 3')
            ->get()
            ->toJson();

        $categoriesListArray = json_decode($categoriesList, true);

        $arrayOfUrls = [];

        foreach($categoriesListArray as $item) {
            $arrayOfUrls[] = explode('/', $item['product_url']);
        }
        dd(json_encode($arrayOfUrls));
    }

}
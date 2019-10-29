<?php

namespace App\Http\Controllers\Api;


use App\Entities\Category;
use App\Entities\Product;
use App\Http\Requests\Api\Products\ProductPricesUpdateRequest;
use App\Repositories\CategoryRepositoryEloquent;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use App\Traits\Paginatable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductsController
{
    use ApiResponsesTrait;
    use Paginatable;

    protected $repository;

    protected $warehouseRepository;

    protected $productPriceRepository;

    public function __construct(
        ProductRepository $repository,
        WarehouseRepository $warehouseRepository,
        ProductPriceRepository $productPriceRepository
    ) {
        $this->repository = $repository;
        $this->warehouseRepository = $warehouseRepository;
        $this->productPriceRepository = $productPriceRepository;
    }

    /**
     * @param $id
     * @return array
     */
    public function getProductsForPriceUpdates($id)
    {
        $warehouse = $this->warehouseRepository->find($id);
        if (empty($warehouse)) {
            abort(404);
        }

        $products = $this->repository->findWhere([
            ['date_of_price_change', '!=', null],
            ['product_name_supplier', '=', $warehouse->symbol]
        ]);

        $productsReturnArray = [];

        foreach ($products as $product) {
            $group = $product->product_group_for_change_price;
            if ($group != null) {
                $exp = explode('-', $group);
                $groupExp = $exp[1];
                $numberGroup = $exp[0];
                if($product->date_of_price_change !== null) {
                    $dateOfPriceChange = new Carbon($product->date_of_price_change);
                } else {
                    $dateOfPriceChange = null;
                }
                $array = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'symbol' => $product->symbol,
                    'product_name_supplier' => $product->product_name_supplier,
                    'product_name_supplier_on_documents' => $product->product_name_supplier_on_documents,
                    'date_of_price_change' => $dateOfPriceChange->addDay()->toDateString(),
                    'date_of_the_new_prices' => null,
                    'value_of_price_change_data_first' => $product->value_of_price_change_data_first,
                    'value_of_price_change_data_second' => $product->value_of_price_change_data_second,
                    'value_of_price_change_data_third' => $product->value_of_price_change_data_third,
                    'value_of_price_change_data_fourth' => $product->value_of_price_change_data_fourth,
                ];

                if ($product->text_price_change_data_first != null) {
                    $productsReturnArray[$groupExp][$numberGroup]['mainText'] = [
                        'text_price_change' => $product->text_price_change,
                    ];
                    $productsReturnArray[$groupExp][$numberGroup]['header'] = [
                        'text_price_change_data_first' => $product->text_price_change_data_first,
                        'text_price_change_data_second' => $product->text_price_change_data_second,
                        'text_price_change_data_third' => $product->text_price_change_data_third,
                        'text_price_change_data_fourth' => $product->text_price_change_data_fourth,
                    ];
                }
                $productsReturnArray[$groupExp][$numberGroup][] = $array;
            }
        }

        return $productsReturnArray;
    }

    public function updateProductsPrice(ProductPricesUpdateRequest $request)
    {
        try {
            $request->validated();
            foreach ($request->all() as $item) {
                unset($item['name']);
                unset($item['symbol']);
                unset($item['product_name_supplier_on_documents']);
                $product = $this->repository->find($item['id']);
                if (empty($product)) {
                    abort(404);
                }
                $datePriceChange = new Carbon($item['date_of_price_change']);
                $dateNewPrice = new Carbon($item['date_of_the_new_prices']);
                $item['date_of_price_change'] = $datePriceChange->toDateString();
                $item['date_of_the_new_prices'] = $dateNewPrice->toDateString();
                $this->repository->update($item, $product->id);
                $products = $this->repository->findByField('products_related_to_the_automatic_price_change', $product->symbol);
                foreach($products as $prod){
                    unset($item['date_of_price_change']);
                    $item['product_group_for_change_price'] = $product->product_group_for_change_price;
                    $this->repository->update($item, $prod->id);
                }

            }

            return $this->createdResponse();
        } catch (\Exception $e) {
            Log::error('Problem with update product prices.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getProducts(Request $request)
    {
        $perPage = $this->getPerPage();
        $products = Product::where('show_on_page', '=', 1)->paginate($perPage)->toJson();

        $products = json_decode($products, true, JSON_PRETTY_PRINT);

        foreach ($products['data'] as $productKey => $productValue) {
            if (array_key_exists('url_for_website', $productValue)) {
                if (isset($productValue['url_for_website']) && !File::exists(public_path($productValue['url_for_website']))) {
                    $products['data'][$productKey]['url_for_website'] = null;
                }
            }
        }

        return response($products);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getProduct($id)
    {
        return response(Product::where('id', (int) $id)->first()->toJson());
    }

    /**
     * @param Request $request
     */
    public function getProductsByCategory(Request $request)
    {
        $product_url = $request->input('param');

        $perPage = $this->getPerPage();

        $products = Product::where('product_url', 'like', '%' . Input::get('param') . '%')->where('show_on_page', '=', 1)->paginate($perPage)->toJson();

        $products = json_decode($products, true, JSON_PRETTY_PRINT);

        foreach ($products['data'] as $productKey => $productValue) {
            if (array_key_exists('url_for_website', $productValue)) {
                if (isset($productValue['url_for_website']) && !File::exists(public_path($productValue['url_for_website']))) {
                    $products['data'][$productKey]['url_for_website'] = null;
                }
            }
        }

        return response($products);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getCategoriesTree()
    {
        $categoriesList = DB::table('products')
            ->select(DB::raw('distinct(product_url)'))
            ->whereRaw('char_length(product_url) > 3')
            ->orderBy('priority', 'asc')
            ->get()
            ->toJson();

        $categoriesListArray = json_decode($categoriesList, true);

        $arrayOfUrls = [];
        $navigationTree = [];

        $map = [];

        foreach($categoriesListArray as $url) {
            $folders = explode('/', $url['product_url']);

            //clear empty strings

            $this->applyChain($map, $folders, []);

        }
        return response($map);

//        foreach ($map as $key => $value) {
//
//            $key = array_keys($key);
//            dump('key', $key);
//
//            $lastInsertId = DB::table('categories')->insertGetId(
//                [
//                    'name' => $key[0],
//                    'status' => 1,
//                ]
//            );
//            dump($lastInsertId);
//        }

        //struktura bazy
//        $root_id = 0;
//        $parent_id = 0;
//        foreach($map as $item) {
//            foreach ( $item as $root_key => $root_value ) {
//
//                $parent_id = $this->insertRecord($root_key, $parent_id);
//                $root_id = $parent_id;
//
//                if ( is_array($root_value) ) {
//                    foreach ( $root_value as $parent_key => $parent_value ) {
//
//                        $parent_id = $this->insertRecord($parent_key, $root_id);
//                        $keep_parent_id = $parent_id;
//
//                        if ( is_array($parent_value) ) {
//                            foreach ( $parent_value as $child_key => $child_value ) {
//
//                                $parent_id = $this->insertRecord($child_key, $keep_parent_id);
//                                $this->getlevel($child_value, $parent_id);
//                            }
//                        }
//                    }
//                }
//            }
//        }

    }

    private function applyChain(&$arr, $indexes, $value) { //Here's your recursion
        if(!is_array($indexes)) {
            return;
        }

        if(count($indexes) == 0 || empty($indexes)) {
            $arr = $value;
        } else {
            $this->applyChain($arr[array_shift($indexes)], $indexes, $value);
        }
    }




    private function getlevel($sub_childs, $new_parent_id) {
        $keep_new_parent_id = $new_parent_id;

        if ( is_array($sub_childs) ) {
            foreach ( $sub_childs as $sub_child => $sub_child_sub ) {

                $new_parent_id = $this->insertRecord($sub_child, $keep_new_parent_id);
                if ( is_array($sub_child_sub) ) {
                    $this->getlevel($sub_child_sub, $new_parent_id);
                }
            }
        }
    }

    private function insertRecord($name, $parent_id) {
        $lastInsertId = DB::table('categories')->insertGetId(
            [
                'name' => $name,
                'status' => 1,
                'parent_category' => $parent_id
            ]
        );

        return $lastInsertId;
    }
}

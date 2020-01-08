<?php

namespace App\Http\Controllers\Api;


use App\Entities\Category;
use App\Entities\Product;
use App\Entities\ChimneyAttribute;
use App\Http\Requests\Api\Products\ProductPricesUpdateRequest;
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

    public function getHiddenProducts(Request $request)
    {
        $products = Product::where('products_related_to_the_automatic_price_change', '=', $request->symbol)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->get()
            ->toJson();

        return response($products);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getProducts(Request $request)
    {
        $perPage = $this->getPerPage();
        $products = Product::where('show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->paginate($perPage)->toJson();

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
        $perPage = $this->getPerPage();

        $products = Product::where('products.product_url', 'like', '%' . Input::get('param') . '%')
            ->where('products.show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->paginate($perPage)->toJson();
        $products = json_decode($products, true, JSON_PRETTY_PRINT);
        foreach ($products['data'] as $productKey => $productValue) {
            if (!empty($productValue['url_for_website']) && !File::exists(public_path($productValue['url_for_website']))) {
                $products['data'][$productKey]['url_for_website'] = null;
            }
        }

        return response($products);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getCategoriesTree()
    {
        $categoriesList = Product::select(DB::raw('distinct(product_url)'))
            ->whereRaw('char_length(product_url) > 3')
            ->orderBy('priority', 'asc')
            ->get()
            ->toJson();

        $categoriesListArray = json_decode($categoriesList, true);

        $map = [];

        foreach($categoriesListArray as $url) {
            $folders = explode('/', $url['product_url']);
            $this->applyChain($map, $folders, []);
        }
        return response($map);
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

    public function getProductsForChimney(Request $request)
    {
        if (!is_array($request->attr) || empty($request->attr)) {
            return response("You must provide attributes", 400);
        }

        try {
            $categoryDetail = $this->getCategoryFromRequest($request);
            $params = $this->getParamsFromRequest($request, $categoryDetail);
            $replacements = $this->getReplacements($categoryDetail, $params);
            $products = $this->getProductsFromParams($params, $categoryDetail);
            $replaceProducts = $this->getProductsForSymbols(array_keys($replacements['products_replace']));
            $this->attachReplaceParams($products, $replaceProducts, $replacements);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }

        
        return response(json_encode([
            'products' => $products,
            'replacements' => $replacements['replacements'],
            'products_replace' => $replaceProducts
        ]));
    }

    private function getCategoryFromRequest($request)
    {
        foreach ($request->attr as $id => $value) {
            $attribute = ChimneyAttribute
                ::with(['category' => function ($q) {
                    $q->with(['chimneyAttributes' => function ($q) {
                        $q->with('options');
                    }]);
                    $q->with(['chimneyProducts' => function ($q) {
                        $q->with('replacements');
                    }]);
                }])
                ->find($id)
            ;
            if (!$attribute) {
                throw new \Exception("Wrong attribute ID ($id)");
            }
        }
        return $attribute->category;
    }

    private function getParamsFromRequest($request, $categoryDetail)
    {
        $params = [];

        foreach ($categoryDetail->chimneyAttributes as $attribute) {
            if (empty($request->attr[$attribute->id])) {
                throw new \Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
            }
            $value = null;
            foreach ($attribute->options as $option) {
                if ($request->attr[$attribute->id] == $option->id) {
                    $value = $option->name;
                    break;
                }
            }
            if (empty($value)) {
                throw new \Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
            }
            $params[$attribute->column_number] = $value;
        }

        return $params;
    }

    private function getProductsFromParams($params, $categoryDetail)
    {
        $productsData = [];
        foreach ($categoryDetail->chimneyProducts as $product) {
            $code = $this->replaceParams($product->product_code, $params);
            $quantity = $this->getQuantity($product->formula, $params);
            if ($quantity == 0) {
                continue;
            }
            $productsData[$code] = [
                'quantity' => round($quantity, 2),
                'optional' => $product->optional
            ];
        }

        $products = $this->getProductsForSymbols(array_keys($productsData));

        foreach ($products as $product) {
            $product->quantity = $productsData[$product->symbol]['quantity'];
            $product->optional = $productsData[$product->symbol]['optional'];
        }

        return $products;
    }

    private function getReplacements($categoryDetail, $params)
    {
        $out = [
            'replacements' => [],
            'products' => [],
            'products_replace' => []
        ];

        foreach ($categoryDetail->chimneyProducts as $product) {
            if (count($product->replacements) == 0) {
                continue;
            }
            $id = count($out['replacements']) + 1;
            $replacements = [
                'description' => $product->replacement_description,
                'img' => $product->replacement_img,
                'id' => $id,
                'products' => []
            ];
            $exists = false;
            foreach ($product->replacements as $replacement) {
                $symbol = $this->replaceParams($replacement->product, $params);
                $quantity = $this->getQuantity($replacement->quantity, $params);
                if ($quantity == 0) {
                    continue;
                }
                $replacements['products'][$symbol] = $quantity;
                $out['products'][$this->replaceParams($product->product_code, $params)] = $id;
                $out['products_replace'][$symbol] = [
                    'quantity' => $quantity,
                    'id' => $id
                ];
                $exists = true;
            }
            if ($exists) {
                $out['replacements'][$id] = $replacements;
            }
        }
        return $out;
    }

    private function getProductsForSymbols($symbols)
    {
        $products = Product::whereIn('products.symbol', $symbols)
            ->where('products.show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->get()
        ;

        return $products;
    }

    private function attachReplaceParams($products, $replaceProducts, $replacements)
    {
        foreach ($products as $product) {
            if (isset($replacements['products'][$product->symbol])) {
                $product->changer = $replacements['products'][$product->symbol];
            } else {
                $product->changer = 0;
            }
        }

        foreach ($replaceProducts as $product) {
            if (!isset($replacements['products_replace'][$product->symbol])) {
                throw new \Exception('Unexpected unexisting replacement for symbol '.$product->symbol);
            }
            $product->changer = $replacements['products_replace'][$product->symbol]['id'];
            $product->quantity = $replacements['products_replace'][$product->symbol]['quantity'];
        }
    }

    private function getQuantity($formula, $params)
    {
        $formula = $this->replaceParams($formula, $params);
        $formula = str_replace(',', '.', $formula);
        $wrongChars = preg_replace('/(ceil|round|floor|\d|\.|\+|-|\*|\/|\(|\))/m', '', $formula);
        if (!empty($wrongChars)) {
            return 0;
        }
        try {
            return eval("return $formula;");
        } catch (\ParseError $e) {
            return 0;
        }
    }

    private function replaceParams($text, $params)
    {
        foreach ($params as $key => $value) {
            $text = str_replace("[$key]", $value, $text);
        }
        return $text;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Entities\Product;
use App\Entities\ChimneyAttribute;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use App\Traits\Paginatable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;

class ProductsController extends Controller
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
    )
    {
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
            ['product_name_supplier', '=', $warehouse->symbol]
        ]);

        $productsReturnArray = [];

        foreach ($products as $product) {
            $group = $product->product_group_for_change_price;
            if ($group == null) {
                continue;
            }
            $exp = explode('-', $group);
            $groupExp = $exp[1];
            $numberGroup = $exp[0];
            if ($product->date_of_price_change !== null) {
                $dateOfPriceChange = (new Carbon($product->date_of_price_change))->addDay()->toDateString();
            } else {
                $dateOfPriceChange = '';
            }
            $array = [
                'id' => $product->id,
                'name' => $product->name,
                'symbol' => $product->symbol,
                'product_name_supplier' => $product->product_name_supplier,
                'product_name_supplier_on_documents' => $product->product_name_supplier_on_documents,
                'date_of_price_change' => $dateOfPriceChange,
                'date_of_the_new_prices' => null,
                'value_of_price_change_data_first' => $product->value_of_price_change_data_first ?: 0,
                'value_of_price_change_data_second' => $product->value_of_price_change_data_second ?: 0,
                'value_of_price_change_data_third' => $product->value_of_price_change_data_third ?: 0,
                'value_of_price_change_data_fourth' => $product->value_of_price_change_data_fourth ?: 0,
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

        return $productsReturnArray;
    }

    public function updateProductsPrice(Request $request)
    {
        try {
            $this->validate($request, [
                '*.date_of_price_change' => 'required|date|after:today',
                '*.date_of_the_new_prices' => 'required|date',
            ]);
            foreach ($request->all() as $item) {
                $product = \App\Entities\Product::find($item['id']);
                if (empty($product)) {
                    continue;
                }
                $product->date_of_price_change = (new Carbon($item['date_of_price_change']))->toDateString();
                $product->date_of_the_new_prices = (new Carbon($item['date_of_the_new_prices']))->toDateString();
                $product->value_of_price_change_data_first = (float) str_replace(',', '.', $item['value_of_price_change_data_first'] ?? 0);
                $product->value_of_price_change_data_second = (float) str_replace(',', '.', $item['value_of_price_change_data_second'] ?? 0);
                $product->value_of_price_change_data_third = (float) str_replace(',', '.', $item['value_of_price_change_data_third'] ?? 0);
                $product->value_of_price_change_data_fourth = (float) str_replace(',', '.', $item['value_of_price_change_data_fourth'] ?? 0);
                $product->save();
            }

            return $this->createdResponse();
        } catch (\Exception $e) {
            Log::error('Problem with update product prices.',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]
            );
            die();
        }
    }

    public function getHiddenProducts(Request $request)
    {
        $products = Product
            ::with(['children' => function ($q) {
                $q->join('product_prices', 'products.id', '=', 'product_prices.product_id');
                $q->join('product_packings', 'products.id', '=', 'product_packings.product_id');
                $q->orderBy('priority');
                $q->orderBy('name');
            }])
            ->find((int)$request->product)
            ->children
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
            ->orderBy('priority')
            ->orderBy('name')
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
        return response(
            Product
                ::join('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
                ->find($id)
                ->toJson()
        );
    }

    /**
     * @param Request $request
     */
    public function getProductsByCategory(Request $request)
    {
        $category = Category::find((int)$request->category_id);

        if (!$category) {
            return response("Wrong category_id {$request->category_id}", 400);
        }

        $products = $category
            ->products()
            ->where('products.show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->with('media')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate($this->getPerPage())
            ->toJson();

        $products = json_decode($products, true, JSON_PRETTY_PRINT);
        foreach ($products['data'] as $productKey => $productValue) {
            $products['data'][$productKey]['id'] = $productValue['product_id'];
            if (!empty($productValue['url_for_website']) && !File::exists(public_path($productValue['url_for_website']))) {
                $products['data'][$productKey]['url_for_website'] = null;
            }
            foreach ($productValue['media'] as $mediaKey => $mediaValue) {
                $mediaData = explode('|', $mediaValue['url']);
                if (count($mediaData) == 3) {
                    if (strpos($mediaData[2], \App\Helpers\MessagesHelper::SHOW_FRONT) !== FALSE) {
                        $products['data'][$productKey]['media'][$mediaKey]['url'] = null;
                    } else {
                        unset($products['data'][$productKey]['media'][$mediaKey]);
                    }
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
        $allCategories = Category::orderBy('parent_id')->orderBy('priority')->get()->toArray();
        $tree = $this->parseTree($allCategories);
        return response(json_encode($tree));
    }

    private function parseTree($tree, $root = 0)
    {
        $return = [];
        foreach ($tree as $i => $row) {
            $child = $row['id'];
            $parent = $row['parent_id'];
            if ($parent == $root) {
                unset($tree[$i]);
                $return[] = array_merge($row, ['children' => $this->parseTree($tree, $child)]);
            }
        }
        return $return;
    }

    public function getProductsForChimney(Request $request)
    {
        if (!is_array($request->attr) || empty($request->attr)) {
            return response("You must provide attributes", 400);
        }

        try {
            $category = $this->getCategoryFromRequest($request);
            $params = $this->getParamsFromRequest($request, $category);
            $replacements = $this->getReplacements($category, $params);
            $products = $this->getProductsFromParams($params, $category);
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
                ->find($id);
            if (!$attribute) {
                throw new \Exception("Wrong attribute ID ($id)");
            }
        }
        return $attribute->category;
    }

    private function getParamsFromRequest($request, $category)
    {
        $params = [];

        foreach ($category->chimneyAttributes as $attribute) {
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
                if (count($attribute->options) > 0) {
                    throw new \Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
                }
                $value = $request->attr[$attribute->id];
                $value = trim(str_replace(',', '.', $value));
                if (filter_var($value, FILTER_VALIDATE_FLOAT) === false || $value < 0) {
                    throw new \Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
                }
                $value = number_format($value, 2, '.', '');
            }
            $params[$attribute->column_number] = $value;
        }

        return $params;
    }

    private function getProductsFromParams($params, $category)
    {
        $productsData = [];
        foreach ($category->chimneyProducts as $product) {
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

    private function getReplacements($category, $params)
    {
        $out = [
            'replacements' => [],
            'products' => [],
            'products_replace' => []
        ];

        foreach ($category->chimneyProducts as $product) {
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
            ->orderBy('priority')
            ->orderBy('name')
            ->get();

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
                throw new \Exception('Unexpected unexisting replacement for symbol ' . $product->symbol);
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

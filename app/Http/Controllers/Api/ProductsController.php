<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Entities\Firm;
use App\Entities\PostalCodeLatLon;
use App\Entities\Product;
use App\Entities\Warehouse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetHiddenProductsRequest;
use App\Http\Requests\GetProductsForChimneyRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Categories;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\Products;
use App\Repositories\WarehouseRepository;
use App\Services\ProductsService;
use App\Traits\Paginatable;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    use ApiResponsesTrait;
    use Paginatable;

    public function __construct(
        protected readonly ProductRepository      $repository,
        protected readonly WarehouseRepository    $warehouseRepository,
        protected readonly ProductPriceRepository $productPriceRepository,
        protected readonly ProductsService        $productsService,
    ) {}

    /**
     * @param $id
     * @return array
     */
    public function getProductsForPriceUpdates($id)
    {
        $warehouse = Firm::findOrFail($id);

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
                'order' => $product->order ?: 0,
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

        return $productsReturnArray['UB'];
        usort($productsReturnArray['UB'][0], function($a, $b) {
            return $a->order - $b->order;
        });


        return gettype($productsReturnArray['UB'][0]); // array
    }

    public function updateProductsPrice(Request $request)
    {
        try {
            $this->validate($request, [
                '*.date_of_price_change' => 'required|date|after:today',
                '*.date_of_the_new_prices' => 'required|date',
            ]);
            foreach ($request->all() as $item) {
                $product = Product::find($item['id']);
                if (empty($product)) {
                    continue;
                }
                $productsRelatedIds = Product::where('products_related_to_the_automatic_price_change', $product->symbol)->pluck('id');
                $productsRelatedIds[] = $product->id;

                $array['date_of_price_change'] = (new Carbon($item['date_of_price_change']))->toDateString();
                $array['date_of_the_new_prices'] = (new Carbon($item['date_of_the_new_prices']))->toDateString();
                $array['value_of_price_change_data_first'] = (float)str_replace(',', '.', $item['value_of_price_change_data_first'] ?? 0);
                $array['value_of_price_change_data_second'] = (float)str_replace(',', '.', $item['value_of_price_change_data_second'] ?? 0);
                $array['value_of_price_change_data_third'] = (float)str_replace(',', '.', $item['value_of_price_change_data_third'] ?? 0);
                $array['value_of_price_change_data_fourth'] = (float)str_replace(',', '.', $item['value_of_price_change_data_fourth'] ?? 0);
                Product::whereIn('id', $productsRelatedIds)->update($array);
            }

            dispatch_now(new \App\Jobs\CheckDateOfProductNewPriceJob());

            return $this->createdResponse();
        } catch (Exception $e) {
            Log::error('Problem with update product prices.',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]
            );
            die();
        }
    }

    #[NoReturn] public function getCurrentPrices()
    {
        $products = Product::where('subject_to_price_change', 1)->whereNotNull('date_of_price_change')->get();
        $data = array();
        foreach ($products as $product) {
            $data[] = array(
                $product->id,
                $product->name,
                $product->symbol,
                $product->product_group_for_change_price,
                $product->date_of_price_change,
                $product->date_of_the_new_prices,
                $product->value_of_price_change_data_first,
                $product->value_of_price_change_data_second,
                $product->value_of_price_change_data_third,
                $product->value_of_price_change_data_fourth,
            );
        }
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="aktualneCeny.csv"');
        $file = fopen('php://output', 'w');
        fputcsv($file, array('id', 'Nazwa produktu', 'Symbol Produktu', 'Wed�ug czego przeliczane beda ceny(kolumna 108)', 'Data Zmiany', 'Wst�pna data nast�pnej zmiany ceny', 'Zmienna1', 'Zmienna2', 'Zmienna3', 'Zmienna4'));
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        exit();
    }

    public function getHiddenProducts(GetHiddenProductsRequest $request): JsonResponse
    {
        $products = Product
            ::with(['children' => function ($q) {
                $q->select('product_prices.*', 'product_packings.*', 'products.*');
                $q->join('product_prices', 'products.id', '=', 'product_prices.product_id');
                $q->join('product_packings', 'products.id', '=', 'product_packings.product_id');
                $q->orderBy('priority');
                $q->orderBy('name');
            }])
            ->find((int)$request->product)
            ->children;

        return response()->json($products);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function getProducts(Request $request): Response|ResponseFactory
    {
        $perPage = $this->getPerPage();
        $products = Product::where('show_on_page', '=', 1)
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->join('product_packings', 'products.id', '=', 'product_packings.product_id')
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate($perPage)->toJson();


        return response($products);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getProduct($id): JsonResponse
    {
        return response()->json(
            Products::getProductByIdWithPrices($id)
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getProductsByCategory(Request $request): JsonResponse
    {
        $category = $this->productsService->getCategory($request->all());
        $products = $this->productsService->getProducts($category, $request->query('zipCode'));
        $this->productsService->prepareProductData($products);

        return response()->json($products);
    }

    /**
     * @return ResponseFactory|Response
     */
    public function getCategoriesTree(): JsonResponse
    {
        $allCategories = Category::orderBy('parent_id')->orderBy('priority')->get();

//        foreach ($allCategories as $category) {
//            $products = $category->products;
//
//            if ($products->count() === 0) {
//                continue;
//            }
//
//            $product = $products->first();
//            $userZipCode = request()->query('zip-code');
//            if (!$userZipCode || !$product?->firm) {
//                continue;
//            }
//
//            $deliveryAddressLatLon = PostalCodeLatLon::where('postal_code', $userZipCode)->first();
//
//            $raw = DB::selectOne(
//                'SELECT w.id, pc.latitude, pc.longitude, 1.609344 * SQRT(
//                        POW(69.1 * (pc.latitude - :latitude), 2) +
//                        POW(69.1 * (:longitude - pc.longitude) * COS(pc.latitude / 57.3), 2)) AS distance
//                        FROM postal_code_lat_lon pc
//                             JOIN warehouse_addresses wa on pc.postal_code = wa.postal_code
//                             JOIN warehouses w on wa.warehouse_id = w.id
//                        WHERE w.firm_id = :firmId AND w.status = \'ACTIVE\'
//                        ORDER BY distance
//                    limit 1',
//                [
//                    'latitude' => $deliveryAddressLatLon->latitude,
//                    'longitude' => $deliveryAddressLatLon->longitude,
//                    'firmId' => $product->firm->id,
//                ]
//            );
//
//            if (!empty($raw)) {
//                $radius = $raw->distance;
//            } else {
//                $category->blured = true;
//                continue;
//            }
//
//            $warehouse = Warehouse::find($raw->id);
//
//            if ($radius > $warehouse->radius) {
//                $category->blured = false;
//            } else {
//                $category->blured = true;
//            }
//
//            unset($category->products);
//        }

        $allCategories = $allCategories->toArray();
        $tree = $this->parseTree($allCategories);

        return response()->json($tree);
    }

    private function parseTree($tree, $root = 0): array
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

    /**
     * @param int $id
     * @return Category
     */
    public function getCategory(int $id): Category
    {
        /** @var Category $category */
        $category = Category::where('id', $id)->get()->first();
        $children = Categories::getElementsForCsvReloadJobByParentId($id)->toArray();
        $category->children = $children ?? [];

        return $category;
    }

    public function getProductsForChimney(GetProductsForChimneyRequest $request): Response|Application|ResponseFactory
    {
        try {
            $category = $this->productsService->getCategoryFromRequest($request);
            $params = $this->productsService->getParamsFromRequest($request, $category);
            $replacements = $this->productsService->getReplacements($category, $params);
            $products = $this->productsService->getProductsFromParams($params, $category);
            $replaceProducts = Categories::getProductsForSymbols(array_keys($replacements['products_replace']));
            $this->productsService->attachReplaceParams($products, $replaceProducts, $replacements);
        } catch (Exception $e) {
            return response($e->getMessage(), 400);
        }

        return response(json_encode([
            'products' => $products,
            'replacements' => $replacements['replacements'],
            'products_replace' => $replaceProducts
        ]));
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        $product->update($request->validated());

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::random(10);
            $image->move(public_path('images/products'), $imageName);
            $product->update(['url_for_website' => '/images/products/' . $imageName]);

            return Product::find($product->id)->url_for_website;
        }

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function getSingleProduct(Product $product): JsonResponse
    {
        $product->load('stock');

        return response()->json($product);
    }

    public function getProductsByCategoryForMobile(Request $request): JsonResponse
    {
        $category = $this->productsService->getCategory($request->all());
        $products = $this->productsService->getProductsMobile($category, $request->query('zipCode'));
//        $this->productsService->prepareProductData($products);

        return response()->json($products);
    }
}

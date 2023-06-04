<?php

namespace App\Http\Controllers\Api;

use App\Entities\Category;
use App\Entities\ChimneyAttribute;
use App\Entities\Product;
use App\Helpers\MessagesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetHiddenProductsRequest;
use App\Http\Requests\GetProductsForChimneyRequest;
use App\Repositories\Categories;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\Products;
use App\Repositories\ProductStockLogs;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use ParseError;

class ProductsController extends Controller
{
    use ApiResponsesTrait;
    use Paginatable;

    public function __construct(
        protected readonly ProductRepository       $repository,
        protected readonly WarehouseRepository     $warehouseRepository,
        protected readonly ProductPriceRepository  $productPriceRepository,
        protected readonly ProductsService         $productsService,
    ) {}

    /**
     * @param $id
     * @return array
     */
    public function getProductsForPriceUpdates($id): array
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
        $products = $this->productsService->getProducts($category);
        $this->productsService->prepareProductData($products);

        return response()->json($products);
    }

    /**
     * @return ResponseFactory|Response
     */
    public function getCategoriesTree(): Response|ResponseFactory
    {
        $allCategories = Category::orderBy('parent_id')->orderBy('priority')->get()->toArray();
        $tree = $this->parseTree($allCategories);

        return response(json_encode($tree));
    }

    private function parseTree($tree,  $root = 0): array
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
}

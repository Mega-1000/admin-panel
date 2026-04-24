<?php

namespace App\Http\Controllers;

use App\Entities\Firm;
use App\Entities\Product;
use App\Entities\ProductPrice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PriceListController extends Controller
{
    public function index(): View
    {
        $firms = Firm::whereHas('products')->orderBy('name')->get(['id', 'name', 'symbol']);

        return view('price-list.index', compact('firms'));
    }

    public function getProducts(int $firmId, Request $request): JsonResponse
    {
        $firm    = Firm::findOrFail($firmId);
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = 50;

        $styrofoamCategoryIds = $this->getDescendantCategoryIds(42);

        $paginator = Product::with(['packing', 'price', 'children.packing', 'children.price'])
            ->whereNull('parent_id')
            ->where('product_name_supplier', $firm->symbol)
            ->orderBy('order')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        $products = [];
        $header   = [];

        foreach ($paginator->items() as $product) {
            // Skip products whose group code has no '-' separator (numeric-only codes are not valid groups)
            $group = $product->product_group_for_change_price;
            if ($group !== null) {
                [, $groupExp] = array_pad(explode('-', $group, 2), 2, '');
                if (empty($groupExp)) {
                    continue;
                }
            }

            if (empty($header) && $product->text_price_change_data_first !== null) {
                $header = [
                    'text_price_change_data_first'  => $product->text_price_change_data_first,
                    'text_price_change_data_second' => $product->text_price_change_data_second,
                    'text_price_change_data_third'  => $product->text_price_change_data_third,
                    'text_price_change_data_fourth' => $product->text_price_change_data_fourth,
                ];
            }

            $products[] = $this->buildProductData($product, $styrofoamCategoryIds, false);
            foreach ($product->children->sortBy('order') as $child) {
                $products[] = $this->buildProductData($child, $styrofoamCategoryIds, true);
            }
        }

        return response()->json([
            'products'     => $products,
            'header'       => $header,
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'total'        => $paginator->total(),
            'per_page'     => $perPage,
        ]);
    }

    public function saveProducts(Request $request, int $firmId): JsonResponse
    {
        Firm::findOrFail($firmId);

        try {
            foreach ($request->json()->all() as $item) {
                if (!array_key_exists('id', $item)) {
                    continue;
                }

                $product = Product::with('packing')->find($item['id']);
                if (!$product) {
                    continue;
                }

                $relatedIds   = Product::where('products_related_to_the_automatic_price_change', $product->symbol)
                    ->pluck('id')
                    ->push($product->id)
                    ->unique()
                    ->all();

                $priceFirst = (float) str_replace(',', '.', $item['value_of_price_change_data_first'] ?? 0);
                $packUnits  = $product->packing?->numbers_of_basic_commercial_units_in_pack ?? 1;

                ProductPrice::whereIn('product_id', $relatedIds)->update([
                    'gross_selling_price_basic_unit'                        => $priceFirst * 1.23,
                    'net_selling_price_basic_unit'                          => $priceFirst,
                    'net_purchase_price_basic_unit_after_discounts'         => $priceFirst,
                    'gross_purchase_price_basic_unit_after_discounts'       => $priceFirst * 1.23,
                    'gross_selling_price_aggregate_unit'                    => $priceFirst * $packUnits,
                    'gross_selling_price_commercial_unit'                   => $priceFirst * $packUnits,
                    'gross_selling_price_the_largest_unit'                  => $priceFirst * $packUnits,
                    'gross_purchase_price_aggregate_unit_after_discounts'   => $priceFirst * $packUnits,
                    'gross_purchase_price_commercial_unit_after_discounts'  => $priceFirst * $packUnits,
                    'gross_purchase_price_the_largest_unit_after_discounts' => $priceFirst * $packUnits,
                    'gross_price_of_packing'                                => $priceFirst * $packUnits * 1.23,
                    'net_selling_price_commercial_unit'                     => $priceFirst * $packUnits,
                    'net_selling_price_calculated_unit'                     => $priceFirst,
                    'net_selling_price_aggregate_unit'                      => $priceFirst * $packUnits,
                    'net_selling_price_the_largest_unit'                    => $priceFirst * $packUnits,
                    'table_price'                                           => $priceFirst * $packUnits * 1.23,
                    'additional_payment_for_milling'                        => (float) str_replace(',', '.', $item['additional_payment_for_milling'] ?? 0),
                ]);

                Product::whereIn('id', $relatedIds)->update([
                    'date_of_price_change'              => Carbon::parse($item['date_of_price_change'])->toDateString(),
                    'date_of_the_new_prices'            => Carbon::parse($item['date_of_the_new_prices'])->toDateString(),
                    'value_of_price_change_data_first'  => $priceFirst,
                    'value_of_price_change_data_second' => (float) str_replace(',', '.', $item['value_of_price_change_data_second'] ?? 0),
                    'value_of_price_change_data_third'  => (float) str_replace(',', '.', $item['value_of_price_change_data_third']  ?? 0),
                    'value_of_price_change_data_fourth' => (float) str_replace(',', '.', $item['value_of_price_change_data_fourth'] ?? 0),
                ]);
            }

            return response()->json(['message' => 'Ceny zostały zaktualizowane.'], 200);

        } catch (Exception $e) {
            Log::error('Problem with update product prices.', [
                'exception' => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            return response()->json(['message' => 'Błąd serwera. Sprawdź logi.'], 500);
        }
    }

    private function buildProductData(Product $product, array $styrofoamCategoryIds, bool $isVariant): array
    {
        $basicUnitPrice     = (float) ($product->price?->net_purchase_price_basic_unit_after_discounts ?? 0);
        $millingCost        = (float) ($product->price?->additional_payment_for_milling ?? 0);
        $rawUnits    = (float) ($product->packing?->numbers_of_basic_commercial_units_in_pack ?? 0);
        $unitsInPack = $rawUnits > 0 ? $rawUnits : 1;
        $calculatedNetPrice = round($basicUnitPrice + $millingCost, 2);

        $dateOfPriceChange = $product->date_of_price_change
            ? Carbon::parse($product->date_of_price_change)->addDay()->toDateString()
            : '';

        return [
            'id'                               => $product->id,
            'name'                             => $product->name,
            'symbol'                           => $product->symbol,
            'product_name_supplier'            => $product->product_name_supplier,
            'product_name_supplier_on_documents' => $product->product_name_supplier_on_documents,
            'date_of_price_change'             => $dateOfPriceChange,
            'date_of_the_new_prices'           => null,
            'value_of_price_change_data_first' => $product->value_of_price_change_data_first  ?: 0,
            'value_of_price_change_data_second'=> $product->value_of_price_change_data_second ?: 0,
            'value_of_price_change_data_third' => $product->value_of_price_change_data_third  ?: 0,
            'value_of_price_change_data_fourth'=> $product->value_of_price_change_data_fourth ?: 0,
            'numbers_of_basic_commercial_units_in_pack' => $unitsInPack,
            'vat'                              => $product->price?->vat ?? 23,
            'additional_payment_for_milling'   => $millingCost,
            'show_milling'                     => in_array($product->category_id, $styrofoamCategoryIds),
            'order'                            => $product->order ?: 0,
            'pattern_to_set_the_price'         => $product->pattern_to_set_the_price ?? '',
            'calculated_net_price'             => $calculatedNetPrice,
            'is_variant'                       => $isVariant,
        ];
    }

    private function getDescendantCategoryIds(int $rootId): array
    {
        $all = \App\Entities\Category::select('id', 'parent_id')->get()->keyBy('id');

        $ids     = [$rootId];
        $queue   = [$rootId];

        while (!empty($queue)) {
            $parentId = array_shift($queue);
            foreach ($all as $cat) {
                if ((int) $cat->parent_id === $parentId) {
                    $ids[]   = $cat->id;
                    $queue[] = $cat->id;
                }
            }
        }

        return $ids;
    }
}

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
        $firms = Firm::orderBy('name')->get(['id', 'name', 'symbol']);

        return view('price-list.index', compact('firms'));
    }

    public function getProducts(int $firmId): JsonResponse
    {
        $firm = Firm::findOrFail($firmId);

        $products = Product::where('product_name_supplier', $firm->symbol)->get();

        $result = [];

        foreach ($products as $product) {
            $group = $product->product_group_for_change_price;
            if ($group === null) {
                continue;
            }

            [$numberGroup, $groupExp] = array_pad(explode('-', $group, 2), 2, '');

            if (empty($groupExp)) {
                continue;
            }

            $dateOfPriceChange = $product->date_of_price_change
                ? Carbon::parse($product->date_of_price_change)->addDay()->toDateString()
                : '';

            if ($product->text_price_change_data_first !== null) {
                $result[$groupExp][$numberGroup]['mainText'] = [
                    'text_price_change' => $product->text_price_change,
                ];
                $result[$groupExp][$numberGroup]['header'] = [
                    'text_price_change_data_first'  => $product->text_price_change_data_first,
                    'text_price_change_data_second' => $product->text_price_change_data_second,
                    'text_price_change_data_third'  => $product->text_price_change_data_third,
                    'text_price_change_data_fourth' => $product->text_price_change_data_fourth,
                ];
            }

            $result[$groupExp][$numberGroup][] = [
                'id'                                  => $product->id,
                'name'                                => $product->name,
                'symbol'                              => $product->symbol,
                'product_name_supplier'               => $product->product_name_supplier,
                'product_name_supplier_on_documents'  => $product->product_name_supplier_on_documents,
                'date_of_price_change'                => $dateOfPriceChange,
                'date_of_the_new_prices'              => null,
                'value_of_price_change_data_first'    => $product->value_of_price_change_data_first  ?: 0,
                'value_of_price_change_data_second'   => $product->value_of_price_change_data_second ?: 0,
                'value_of_price_change_data_third'    => $product->value_of_price_change_data_third  ?: 0,
                'value_of_price_change_data_fourth'   => $product->value_of_price_change_data_fourth ?: 0,
                'order'                               => $product->order ?: 0,
            ];
        }

        // Sort product entries by 'order' within each subgroup
        foreach ($result as $groupName => &$subgroups) {
            foreach ($subgroups as $subNum => &$entries) {
                $products = [];
                $meta     = [];
                foreach ($entries as $key => $entry) {
                    if (is_array($entry) && isset($entry['id'])) {
                        $products[] = $entry;
                    } else {
                        $meta[$key] = $entry;
                    }
                }
                usort($products, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
                $entries = $meta + array_values($products);
            }
        }
        unset($subgroups, $entries);

        return response()->json($result);
    }

    public function saveProducts(Request $request, int $firmId): JsonResponse
    {
        Firm::findOrFail($firmId);

        try {
            foreach ($request->json()->all() as $item) {
                if (!array_key_exists('id', $item)) {
                    continue;
                }

                $product = Product::find($item['id']);
                if (!$product) {
                    continue;
                }

                $relatedIds   = Product::where('products_related_to_the_automatic_price_change', $product->symbol)
                    ->pluck('id')
                    ->push($product->id)
                    ->unique()
                    ->all();

                $priceFirst = (float) str_replace(',', '.', $item['value_of_price_change_data_first'] ?? 0);
                $packUnits  = $product->packing->numbers_of_basic_commercial_units_in_pack ?? 1;

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
}

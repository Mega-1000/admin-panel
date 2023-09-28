<?php

namespace App\Jobs;

use App\Entities;
use App\Entities\Category;
use App\Entities\Employee;
use App\Entities\EmployeeRole;
use App\Entities\Firm;
use App\Entities\JpgDatum;
use App\Entities\PostalCodeLatLon;
use App\Entities\Product;
use App\Entities\ProductTradeGroup;
use App\Entities\Warehouse;
use App\Repositories\Categories;
use DateTime;
use Exception;
use FontLib\TrueType\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class ImportCsvFileJob
 *
 * @package App\Jobs
 */
class ImportCsvFileJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        IsMonitored;

    public $currentCategories;

    private array $categories = ['id' => 0, 'children' => []];
    private array $productsRelated = [];
    private array $jpgData = [];

    private $currentLine;
    private $existingProducts;

    /**
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $path = Storage::path('user-files/baza/baza.csv');

        if (!file_exists($path) || !$this->tryStartImport()) {
            return;
        }

        $this->log('Import start: ' . Carbon::now());

        $handle = fopen($path, 'rb');
        if (!$handle) {
            $msg = 'CSV file not found';
            $this->log($msg);
            throw new FileNotFoundException($msg);
        }

        $this->log('Clear tables start');
        $this->clearTables();
        $this->log('Clear tables end');

        $time = microtime(true);

        for ($i = 1; $line = fgetcsv($handle, 0, ';'); $i++) {
            $this->currentLine = $i;
            if ($i % 100 === 0) {
                $this->log($i . ' - time ' . round(microtime(true) - $time, 3));
                $time = microtime(true);
            }

            // intentional variable assigning here, not an error
            if (!$categoryColumn = $this->getCategoryColumn($line)) {
                continue;
            }

            $array = $this->getProductArray($line, $categoryColumn);
            $categoryTree = $this->getCategoryTree($line, $categoryColumn);

            $array = $this->attachEmployeesToProduct($line, $array);

            try {
                $multiCalcBase = trim($line[$categoryColumn + 12]);
                $multiCalcCurrent = trim($line[$categoryColumn + 8]);
                if (empty($array['symbol']) && empty($multiCalcBase) && empty($multiCalcCurrent)) {
                    $this->saveCategory($line, $categoryTree, $categoryColumn);
                } elseif ($line[6] == 1) {
                    $product = $this->saveProduct($array, $categoryTree, !empty($multiCalcCurrent));
                    $media = $this->getProductsMedia($line);
                    if ($media) {
                        $this->createProductMedia($media, $product);
                    }

                    if ($line[500]) {
                        $productAnalyze = new Entities\ProductAnalyzer();
                        $productAnalyze->product_id = $product->id;
                        $productAnalyze->parse_service = 'allegro';
                        $productAnalyze->parse_url = $line[500];
                    }

                    /** @var Product $existingProducts */
                    $existingProduct = $this->existingProducts->where('symbol', $array['symbol'])->where('save_name', false)->first();

                    $this->setProductTradeGroups($line, $product);
                    if (!empty($multiCalcBase)) {
                        $this->productsRelated[$categoryColumn . '-' . $multiCalcBase] = $product->id;
                    } elseif (!empty($multiCalcCurrent) && !empty($this->productsRelated[$categoryColumn . '-' . $multiCalcCurrent])) {
                        $product->parent_id = $this->productsRelated[$categoryColumn . '-' . $multiCalcCurrent];
                        $product->category_id = Entities\Product::find($product->parent_id)->category_id;
                        $product->save();
                    }

                    $product->update([
                        'save_name' => $existingProduct?->save_name ?? true,
                        'name' => $existingProduct?->name ?? $product->name,
                    ]);
                }
                $this->generateJpgData($line, $categoryColumn, $product ?? null);
            } catch (Exception $e) {
                $this->log("Row $i EXCEPTION: " . $e->getMessage() . ", File: " . $e->getFile() . ", Line: " . $e->getLine());
            }
        }
        $this->saveJpgData();

        $this->updateImportTable();
        $this->makeBackups();

        $this->log('Import end: ' . Carbon::now());
    }

    private function clearTables()
    {
        $this->existingProducts = Product::where('save_name', false)->get();
        Product::withTrashed()->where('symbol', '')->orWhereNull('symbol')->forceDelete();
        Product::withTrashed()->update([
            'category_id' => null,
            'parent_id' => null,
            'product_name_supplier' => '',
            'product_name_supplier_on_documents' => '',
            'product_group_for_change_price' => '',
            'products_related_to_the_automatic_price_change' => '',
            'deleted_at' => Carbon::now()
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->currentCategories = Categories::getElementsForCsvReloadJob();
        Categories::deleteElementsForCsvReloadJob();

        DB::statement("TRUNCATE product_media");
        DB::statement("TRUNCATE product_trade_groups");
        DB::statement("TRUNCATE chimney_replacements");
        DB::statement("TRUNCATE chimney_products");
        DB::statement("TRUNCATE chimney_attribute_options");
        DB::statement("TRUNCATE chimney_attributes");
        DB::statement("TRUNCATE jpg_data");
        DB::statement("TRUNCATE categories");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function getUrl($url): array|string
    {
        $imgUrlExploded = explode('\\', $url);
        $imgUrlExploded = end($imgUrlExploded);
        $imgUrlWebsite = 'products' . DIRECTORY_SEPARATOR . $imgUrlExploded;
        $imgUrlWebsite = Storage::url($imgUrlWebsite);
        return str_replace("\\", '/', $imgUrlWebsite);
    }

    private function getShowOnPageParameter(array $line, int $columnIterator): bool
    {
        return array_key_exists($columnIterator + 14, $line) && $line[$columnIterator + 14] == 1;
    }

    private function getProductsOrder(array $line, int $columnIterator): int
    {
        return ((int)$line[$columnIterator + 7]) ?: 1000000;
    }

    /**
     * @throws Exception
     */
    private function saveCategory(array $line, array $categoryTree, int $categoryColumn)
    {
        $parent = &$this->getCategoryParent($categoryTree);

        /** @var Category $existingCategory */
        $existingCategory = $this->currentCategories->filter(function ($category) use ($categoryTree) {
            return $category->name === end($categoryTree);
        })->first();

        $image = $existingCategory?->save_image === false ? $existingCategory?->img : $line[303] ?? 'https://via.placeholder.com/300';
        if (strpos($image, "\\")) {
            $image = $this->getUrl($image);
        }


        /** @var Category $category */
        $category = Category::query()->create([
            'name' => end($categoryTree),
            'description' => $existingCategory?->description ?? $line[310],
            'img' => $image,
            'rewrite' => $this->rewrite(end($categoryTree)),
            'is_visible' => $this->getShowOnPageParameter($line, $categoryColumn),
            'priority' => $this->getProductsOrder($line, $categoryColumn),
            'save_name' => $existingCategory?->save_name ?? true,
            'save_description' => $existingCategory?->save_description ?? true,
            'save_image' => $existingCategory?->save_image ?? true,
            'parent_id' => $parent['id'],
        ]);

        $parent['children'][$category->name] = ['id' => $category->id, 'children' => []];

        $replacements = $this->getChimneyReplacements($line);
        $this->appendChimneyAttributes($category, $line);
        $this->appendChimneyProducts($category, $line, 422, 40, false, $replacements);
        $this->appendChimneyProducts($category, $line, 518, 38, true);
    }

    /**
     * @throws Exception
     */
    private function &getCategoryParent($categoryTree, $isProduct = false)
    {
        $current = &$this->categories;
        $iMax = count($categoryTree) - ($isProduct ? 0 : 1);
        foreach ($categoryTree as $i => $cat) {
            if (isset($current['children'][$cat])) {
                $current = &$current['children'][$cat];
                if ($i == $iMax) {
                    if ($isProduct) {
                        if (empty($current['children'])) {
                            return $current;
                        }
                        $this->log("Row {$this->currentLine} WARNING: Products should be placed in deepest category only");
                        return $this->categories;
                    }
                    throw new Exception("Category already exists");
                }
                continue;
            } elseif ($i < $iMax) {
                if ($isProduct) {
                    if (empty($current['children'])) {
                        return $current;
                    }
                    $this->log("Row {$this->currentLine} WARNING: Products should be placed in deepest category only");
                    return $this->categories;
                }
                throw new Exception("Missing category parent");
            }
        }
        return $current;
    }

    private function appendChimneyAttributes($category, $line)
    {
        $start = 407;
        for ($i = $start; $i < 422; $i++) {
            if (empty($line[$i])) {
                continue;
            }
            $arr = explode('||', $line[$i]);
            if (count($arr) != 2) {
                continue;
            }
            $attribute = $category->chimneyAttributes()->create([
                'name' => $arr[0],
                'column_number' => $i - $start + 1
            ]);
            $options = explode('|', $arr[1]);
            $array = [];
            foreach ($options as $opt) {
                $opt = trim($opt);
                if (!$opt) {
                    continue;
                }
                $array[] = [
                    'name' => $opt,
                    'chimney_attribute_id' => $attribute->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            Entities\ChimneyAttributeOption::insert($array);
        }
    }

    private function appendChimneyProducts($category, $line, $start, $count, $optional, $replacements = [])
    {
        for ($i = $start; $i < $start + $count; $i = $i + ($optional ? 2 : 1)) {
            if (empty($line[$i])) {
                continue;
            }
            $arr = explode('||', $line[$i]);
            if (count($arr) != 2) {
                continue;
            }
            $product = new Entities\ChimneyProduct([
                'product_code' => $arr[0],
                'formula' => $arr[1],
                'column_number' => $optional ? 0 : $i - $start + 1,
                'optional' => $optional ? 1 : 0
            ]);
            if (isset($replacements[$product->column_number])) {
                $product->replacement_description = $replacements[$product->column_number]['description'];
                $product->replacement_img = $replacements[$product->column_number]['img'];
            }
            $category->chimneyProducts()->save($product);



            if (isset($replacements[$product->column_number])) {
                $array = $replacements[$product->column_number]['products'];
                foreach ($array as $key => $value) {
                    $array[$key]['chimney_product_id'] = $product->id;
                    $array[$key]['created_at'] = Carbon::now();
                    $array[$key]['updated_at'] = Carbon::now();
                }
                Entities\ChimneyReplacement::insert($array);
            }
        }
    }

    private function getChimneyReplacements($line): array
    {
        $replacements = [];
        $start = 462;
        for ($i = $start; $i < 518; $i += 4) {
            if (empty($line[$i]) || empty($line[$i + 1]) || empty($line[$i + 2]) || empty($line[$i + 3])) {
                continue;
            }
            $arrs = explode('//', $line[$i + 2]);
            foreach ($arrs as $arr) {
                $arr = explode('||', $arr);
                if (count($arr) != 2) {
                    continue;
                }
                $productNumber = trim($line[$i + 1], "[]");
                if (!isset($replacements[$productNumber])) {
                    $replacements[$productNumber] = [
                        'img' => $this->getUrl($line[$i + 3]),
                        'products' => []
                    ];
                }
                $replacements[$productNumber]['products'][] = [
                    'product' => $arr[0],
                    'quantity' => $arr[1],
                ];
            }
        }
        return $replacements;
    }

    private function saveProduct($array, $categoryTree, $isChildProduct)
    {
        var_dump($array);
        $product = null;
        if (!empty($array['symbol'])) {
            $product = Entities\Product::withTrashed()->where('symbol', $array['symbol'])->first();
        }

        if (!$product) {
            $product = new Entities\Product();
        }

        if (!$isChildProduct) {
            $category = $this->getCategoryParent($categoryTree, $array);
            $array['category_id'] = $category['id'] ?: null;
        } else {
            $array['category_id'] = null;
        }

        $updatePrices = !$product->price || !$array['subject_to_price_change'];

        if (!$updatePrices) {
            unset($array['value_of_price_change_data_first']);
            unset($array['value_of_price_change_data_second']);
            unset($array['value_of_price_change_data_third']);
            unset($array['value_of_price_change_data_fourth']);
            unset($array['date_of_price_change']);
            unset($array['date_of_the_new_prices']);
        }

        if (empty($array['date_of_price_change'])) {
            unset($array['date_of_price_change']);
        }
        if (empty($array['date_of_the_new_prices'])) {
            unset($array['date_of_the_new_prices']);
        }

        if (!empty($array['products_related_to_the_automatic_price_change'])) {
            $array['date_of_the_new_prices'] = null;
            $array['date_of_price_change'] = null;
            $array['product_group_for_change_price'] = '';
        }

        $product->fill($array);

        $product->assortment_quantity = $array['assortment_quantity'] ?? null;
        $product->delivery_type = $array['delivery_type'] ?? null;
        $product->low_order_quantity_alert_text = $array['low_order_quantity_alert_text'] ?? null;
        $product->layers_in_package = $array['layers_in_package'] ?? null;

        $product->save();
        $product->restore();

        if (!empty($array['newsletter'])) {
            $product->discounts()->create([
                'old_price' => $product->value,
                'new_price' => $product->value,
                'description' => 'Newsletter',
            ]);
        }

        if ($updatePrices) {
            $price = $product->price ?? new Entities\ProductPrice();
            $price->fill($array);
            $product->price()->save($price);
        }

        $packing = $product->packing ?? new Entities\ProductPacking();
        $packing->fill($array);
        $product->packing()->save($packing);

        if ($product->stock()->exists() === false) {
            $product->stock()->create([
                'quantity' => 0
            ]);
        }

        return $product;
    }

    private function getProductArray($line, $categoryColumn): array
    {
        $trade = explode('|', $line[378]);
        $tradeGroup = $trade[0];
        $tradeGroupDisplay = $trade[1] ?? null;
        $array = [
            'name' => $line[4],
            'symbol' => $line[5],
            'manufacturer' => $line[16],
            'product_name_manufacturer' => $line[17],
            'symbol_name_manufacturer' => $line[18],
            'product_name_supplier' => $line[20],
            'product_name_supplier_on_documents' => $line[20],
            'product_name_on_collective_box' => $line[22],
            'supplier_product_symbol' => $line[24],
            'supplier_product_name' => $line[26],
            'ean_of_commercial_packing' => $line[29],
            'ean_of_collective_packing' => $line[30],
            'ean_of_biggest_packing' => $line[31],
            'producent_override' => $line[42],
            'vat' => $line[54],
            'calculation_unit' => $line[57],
            'unit_commercial' => $line[59],
            'unit_basic' => $line[58],
            'unit_of_collective' => $line[60],
            'unit_biggest' => $line[61],
            'number_on_a_layer' => $line[62],
            'unit_consumption' => $line[72],
            'numbers_of_basic_commercial_units_in_pack' => $line[73],
            'number_of_sale_units_in_the_pack' => $line[74],
            'number_of_trade_items_in_the_largest_unit' => $line[75],
            'min_quantity' => $line[87],
            'weight_collective_unit' => floatval(str_replace(',', '.', $line[103])),
            'weight_trade_unit' => floatval(str_replace(',', '.', $line[100])),
            'weight_biggest_unit' => floatval(str_replace(',', '.', $line[104])),
            'weight_base_unit' => floatval(str_replace(',', '.', $line[102])),
            'net_purchase_price_commercial_unit' => $line[116],
            'net_purchase_price_calculated_unit' => $line[117],
            'net_purchase_price_basic_unit' => $line[118],
            'net_purchase_price_aggregate_unit' => $line[119],
            'net_purchase_price_the_largest_unit' => $line[120],
            'discount1' => $line[134],
            'discount2' => $line[135],
            'discount3' => $line[136],
            'solid_discount' => $line[138],
            'net_purchase_price_commercial_unit_after_discounts' => $line[148],
            'net_purchase_price_calculated_unit_after_discounts' => $line[149],
            'net_purchase_price_basic_unit_after_discounts' => $line[150],
            'net_purchase_price_aggregate_unit_after_discounts' => $line[151],
            'net_purchase_price_the_largest_unit_after_discounts' => $line[152],
            'net_special_price_commercial_unit' => $line[163],
            'net_special_price_calculated_unit' => $line[164],
            'net_special_price_basic_unit' => $line[165],
            'net_special_price_aggregate_unit' => $line[211],
            'net_special_price_the_largest_unit' => $line[212],
            'bonus1' => $line[223],
            'bonus2' => $line[224],
            'bonus3' => $line[225],
            'coating' => $line[237],
            'allegro_selling_gross_commercial_price' => $line[250],
            'gross_price_of_packing' => $line[252],
            'gross_selling_price_commercial_unit' => $line[252],
            'net_selling_price_commercial_unit' => $line[270],
            'net_selling_price_basic_unit' => $line[272],
            'net_selling_price_calculated_unit' => $line[271],
            'net_selling_price_aggregate_unit' => $line[273],
            'net_selling_price_the_largest_unit' => $line[274],
            'additional_info1' => $line[289],
            'additional_info2' => $line[290],
            'url' => $line[303],
            'manufacturer_url' => $line[304],
            'video_url' => $line[305],
            'calculator_type' => $line[306],
            'meta_price' => $line[309],
            'meta_description' => $line[311],
            'meta_keywords' => $line[312],
            'status' => $line[313],
            'description_photo_promoted' => $line[314],
            'description_photo_table' => $line[315],
            'description_photo_contact' => $line[316],
            'description_photo_details' => $line[317],
            'pricelist_name' => $line[318],
            'product_group' => $line[291],
            'table_price' => $line[325],
            'warehouse_physical' => $line[346],
            'warehouse' => $line[347],
            'packing_type' => $line[348],
            'number_of_pieces_in_total_volume' => $line[349],
            'recommended_courier' => $line[350],
            'packing_name' => $line[351],
            'max_pieces_in_one_package' => $line[352],
            'dimension_x' => $line[353],
            'dimension_y' => $line[354],
            'dimension_z' => $line[355],
            'paczkomat_size_a' => $line[356],
            'paczkomat_size_b' => $line[357],
            'paczkomat_size_c' => $line[358],
            'allegro_courier' => $line[365],
            'set_rule' => $line[368],
            'max_in_pallete_80' => $line[369],
            'max_in_pallete_100' => $line[370],
            'per_package_factor' => $line[371],
            'trade_group_name' => $tradeGroup,
            'displayed_group_name' => $tradeGroupDisplay,
            'additional_payment_for_milling' => $line[473],
            'date_of_price_change' => $this->getDateOrNull($line[106]),
            'date_of_the_new_prices' => $this->getDateOrNull($line[107]),
            'product_group_for_change_price' => $line[108],
            'products_related_to_the_automatic_price_change' => $line[110],
            'text_price_change' => $line[111],
            'text_price_change_data_first' => $line[112],
            'text_price_change_data_second' => $line[113],
            'text_price_change_data_third' => $line[114],
            'text_price_change_data_fourth' => $line[115],
            'subject_to_price_change' => $line[124],
            'value_of_price_change_data_first' => $line[125],
            'value_of_price_change_data_second' => $line[126],
            'value_of_price_change_data_third' => $line[127],
            'value_of_price_change_data_fourth' => $line[128],
            'pattern_to_set_the_price' => $line[129],
            'euro_exchange' => $line[131],
            'variation_unit' => $line[292],
            'variation_group' => $line[293],
            'review' => $line[279],
            'quality' => $line[280],
            'quality_to_price' => $line[281],
            'comments' => $line[284],
            'value_of_the_order_for_free_transport' => $line[282],
            'gross_selling_price_basic_unit' => $line[254],
            'gross_selling_price_calculated_unit' => $line[253],
            'gross_selling_price_aggregate_unit' => $line[255],
            'gross_selling_price_the_largest_unit' => $line[256],
            'show_on_page' => $this->getShowOnPageParameter($line, $categoryColumn),
            'priority' => $this->getProductsOrder($line, $categoryColumn),
            'average_amount_of_product_in_package' => $line[244],
            'newsletter' => $line[13],
            'assortment_quantity' => $line[216],
            'delivery_type' => $line[11],
            'low_order_quantity_alert_text' => $line[13],
            'layers_in_package' => $line[64],
        ];

        foreach ($array as $key => $value) {
            if ($value === '#ARG!' || $value === '#DZIEL/0!' || $value === '$ADR!') {
                unset($array[$key]);
            }
            if (is_string($value) && str_contains($value, ',')) {
                if ($key !== 'symbol') {
                    $value = str_replace(',', '.', $value);
                    $array[$key] = $value;
                }
            }
        }

        return $array;
    }

    public function attachEmployeesToProduct(array $line, array $array): array
    {

        $employeesIds = [];
        // single employee has 17 columns, let's take 10 employees for each product line, we've got result 170 of columns total
        $employeesColumns = 17;
        $numberOfEmployees = 10;
        $employeesLines = array_slice($line, 1120, $employeesColumns * $numberOfEmployees);
        // get rows with every employee
        $employeesRows = array_chunk($employeesLines, $employeesColumns);

        foreach ($employeesRows as $row) {
            // missing firstname, lastname or email
            $firstName = $row[0];
            $lastName = $row[2];
            $email = $row[4];
            $postalCode = $row[14];
            if (!$firstName || !$lastName || !$email || !$postalCode) continue;

            $employee = Employee::where([
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $email,
            ])->first();

            if (!$employee) {
                $employee = new Employee();
                $postal = PostalCodeLatLon::where('postal_code', $postalCode)->first();
                if (!$postal) continue;
                $employee->latitude = $postal->latitude;
                $employee->longitude = $postal->longitude;
            }

            $firmSymbol = $line[20];
            $firm = Firm::where('symbol', trim($firmSymbol))->first();

            if (isset($firm->id)) {
                $employee->firm_id = $firm->id;
            }
            $employee->firstname = $firstName;
            $employee->firstname_visibility = !empty($row[1]);
            $employee->lastName = $lastName;
            $employee->lastname_visibility = !empty($row[3]);
            $employee->email = $email;
            $employee->email_visibility = !empty($row[5]);
            $employee->phone = $row[6];
            $employee->phone_visibility = !empty($row[7]);
            $employee->comments = $row[10];
            $employee->comments_visibility = !empty($row[11]);
            $employee->additional_comments = $row[12];
            $employee->faq = $row[13];
            $employee->postal_code = $postalCode;
            $employee->radius = intval($row[15]);
            $employee->status = ($row[16] == 1) ? 'ACTIVE' : 'PENDING';

            $employee->save();

            $roles = explode(',', $row[8]);
            $warehouses = explode(',', $row[9]);

            // attach roles
            if (!empty($roles)) {
                $rolesToAttach = [];
                foreach ($roles as $roleSymbol) {

                    $employeeRole = EmployeeRole::where('symbol', trim($roleSymbol))->first();
                    if (!empty($employeeRole)) {
                        $rolesToAttach[] = $employeeRole->id;
                    }
                }
                if (!empty($rolesToAttach)) $employee->employeeRoles()->sync($rolesToAttach);
            }
            // attach warehouses
            if (!empty($warehouses)) {
                $warehousesToAttach = [];
                foreach ($warehouses as $warehouseSymbol) {

                    $warehouse = Warehouse::where('symbol', trim($warehouseSymbol))->first();
                    if (!empty($warehouse)) {
                        $warehousesToAttach[] = $warehouse->id;
                    }
                }
                if (!empty($warehousesToAttach)) $employee->warehouses()->sync($warehousesToAttach);
            }
            $employeesIds[] = $employee->id;
        }
        $array['employees_ids'] = json_encode($employeesIds);
        return $array;
    }

    private function getCategoryColumn($line): ?int
    {
        for ($col = 598; $col <= count($line) - 15; $col += 16) {
            if (!empty($line[$col]) || !empty($line[$col + 8])) {
                return $col;
            }
        }
        return null;
    }

    private function getCategoryTree($line, $categoryColumn): array
    {
        $category = [];
        for ($j = 1; $j <= 6; $j++) {
            $value = trim($line[$categoryColumn + $j]);
            if (empty($value)) {
                break;
            }
            $category[] = $value;
        }
        return $category;
    }

    private function rewrite($string): string
    {
        return strtolower(
            str_replace(
                ["'", "\"", ".", ",", "!", "?", ":", ";", "\\", "[", "]", "(", ")", "<", ">", "@", "#", "$", "%", "^", "*", "+", "=", "/", "&", "#", ":", "."],
                '',
                str_replace(
                    ['ą', 'ę', 'ó', 'ś', 'ż', 'ź', 'ć', 'ł', 'ń', 'Ś', 'Ą', 'Ę', 'Ó', 'Ż', 'Ź', 'Ć', 'Ł', 'Ń', ' ', "\xc2\xa0", "\t", "\n", "\r"],
                    ['a', 'e', 'o', 's', 'z', 'z', 'c', 'l', 'n', 's', 'a', 'e', 'o', 'z', 'z', 'c', 'l', 'n', '-', '-', '-', '', ''],
                    $string
                )
            )
        );
    }

    public function getProductsMedia($line): array
    {
        $media = [];
        for ($i = 304; $i <= 308; $i++) {
            if (!empty($line[$i])) {
                $media[] = $this->prepareMediaData($line[$i]);
            }
        }
        return $media;
    }

    public function prepareMediaData($line): array
    {
        $temp = explode('||', $line);
        return ['url' => $temp[0], 'description' => $temp[1]];
    }

    private function createProductMedia(array $media, $product)
    {
        foreach ($media as $link) {
            $newMedia = new Entities\ProductMedia;
            $newMedia->product_id = $product->id;
            $newMedia->url = $link['url'];
            $newMedia->description = $link['description'];
            $newMedia->save();
        }
    }

    /**
     * @throws Exception
     */
    private function setProductTradeGroups(array $line, Entities\Product $product)
    {
        $this->getTradeGroupParams(379, 'price', $line, $product);
        $this->getTradeGroupParams(385, 'weight', $line, $product);
    }

    /**
     * @throws Exception
     */
    private function getTradeGroupParams($firstParam, $type, $line, Entities\Product $product)
    {
        $tradeGroup = new ProductTradeGroup();
        $tradeGroup->type = $type;
        for ($i = 0; $i < 6; $i += 2) {
            $prefix = match ($i / 2) {
                0 => 'first',
                1 => 'second',
                2 => 'third',
                default => throw new Exception('Błąd ustawiania grupy'),
            };
            $conditionField = $prefix . '_condition';
            $priceField = $prefix . '_price';
            if ($line[$firstParam + $i] === '' || $line[$firstParam + $i + 1] === '') {
                continue;
            }
            $tradeGroup->$conditionField = $line[$firstParam + $i];
            $tradeGroup->$priceField = $line[$firstParam + $i + 1];
        }
        if (empty($tradeGroup->first_condition)) {
            return;
        }
        $tradeGroup->product_id = $product->id;
        $tradeGroup->save();
    }

    private function generateJpgData($line, $categoryColumn, ?Entities\Product $product = null)
    {
        $columns = [9 => 10, 11 => 13];
        foreach ($columns as $fileNameColumn => $orderColumn) {
            $fileName = trim($line[$categoryColumn + $fileNameColumn]);
            if (empty($fileName)) {
                continue;
            }
            $order = ((int)trim($line[$categoryColumn + $orderColumn])) ?: 1000000;
            if (!trim($line[309])) {
                continue;
            }
            $priceType = $line[309][0];
            $price = $line[$priceType == 'h' ? 252 : ($priceType == 'o' ? 253 : 254)];
            if ($price == 0) {
                continue;
            }
            $this->jpgData[$fileName][$line[1]][$line[2]][$line[3]] = [
                'price' => (isset($product) && $product->price !== null) ? $product->price->gross_selling_price_basic_unit : $price,
                'order' => $order,
                'name' => $line[4],
                'image' => $this->getUrl($line[303])
            ];
        }
    }

    private function saveJpgData()
    {
        $data = [];

        foreach ($this->jpgData as $fileName => $table) {
            foreach ($table as $row => $rowData) {
                foreach ($rowData as $col => $subdata) {
                    foreach ($subdata as $subcol => $details) {
                        $data[] = [
                            'filename' => $fileName,
                            'row' => $row,
                            'col' => $col,
                            'subcol' => $subcol,
                            'price' => $details['price'],
                            'order' => $details['order'],
                            'image' => $details['image'],
                            'name' => $details['name']
                        ];
                    }
                }
            }
        }

        JpgDatum::insert($data);
    }

    private function log($text)
    {
        Log::channel('import')->info($text);
        echo $text . "\n";
    }

    private function getDateOrNull($date)
    {
        $d = DateTime::createFromFormat('Y/m/d', $date);
        return $d && $d->format('Y/m/d') == $date ? $d->format('Y-m-d') : null;
    }

    private function tryStartImport()
    {
        $import = Entities\Import::find(1);
        if ($import->processing) {
            if (time() - strtotime($import->last_import) > 1800) {
                if (file_exists(Storage::path('user-files/baza/baza.csv'))) {
                    $this->makeBackups();
                }
                $import->processing = 0;
                $import->save();
            }
            return false;
        }
        $import->processing = 1;
        $import->last_import = Carbon::now();
        $import->save();
        return true;
    }

    private function makeBackups()
    {
        for ($i = 98; $i >= 0; $i--) {
            $iStr = $i < 10 ? "0$i" : $i;
            $oldName = "baza_backup_$iStr";
            $iStr = ($i + 1) < 10 ? "0" . ($i + 1) : $i + 1;
            $newName = "baza_backup_$iStr";
            $this->replaceFile($oldName, $newName);
        }
        $this->replaceFile('baza', 'baza_backup_00');
    }

    private function replaceFile($old, $new)
    {
        $old = Storage::path("user-files/baza/$old.csv");
        $new = Storage::path("user-files/baza/$new.csv");

        if (!file_exists($old)) {
            return;
        }

        if (file_exists($new)) {
            unlink($new);
        }

        rename($old, $new);
    }

    private function updateImportTable()
    {
        $import = Entities\Import::find(1);
        $import->name = 'Import products';
        $import->processing = 0;
        $import->save();
        $import = Entities\Import::find(2);
        $import->name = 'Import products done';
        $import->last_import = Carbon::now();
        $import->save();
    }
}

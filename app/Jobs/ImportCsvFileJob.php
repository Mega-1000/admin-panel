<?php

namespace App\Jobs;

use App\Repositories\ProductPackingRepository;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Entities;

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
        SerializesModels;
    protected $path;
    protected $startRow;
    protected $imgStoragePath;
    public $timeout = 3600;
    public $tries   = 1;
    
    private $productRepository;
    private $productPackingRepository;
    private $productPriceRepository;
    private $productStockRepository;
    private $productStockPositionRepository;

    private $categories = ['id' => 0, 'children' => []];
    private $productsRelated = [];

    /**
     * Create a new job instance.
     *
     * @param int $startRow
     */
    public function __construct(int $startRow = 0)
    {
        $this->path           = Storage::path('public/Baza.csv');
        $this->imgStoragePath = 'products';
        $this->startRow       = $startRow;
    }

    /**
     * Execute the job.
     *
     * @param ProductRepository $productRepository
     * @param ProductPackingRepository $productPackingRepository
     * @param ProductPriceRepository $productPriceRepository
     * @param ProductStockRepository $productStockRepository
     * @param ProductStockPositionRepository $productStockPositionRepository
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(
        ProductRepository $productRepository,
        ProductPackingRepository $productPackingRepository,
        ProductPriceRepository $productPriceRepository,
        ProductStockRepository $productStockRepository,
        ProductStockPositionRepository $productStockPositionRepository
    ): void
    {
        $handle = fopen($this->path, 'rb');
        if (!$handle) {
            throw new FileNotFoundException('CSV file "'.$this->path.'" not found');
        }

        $this->productRepository = $productRepository;
        $this->productPackingRepository = $productPackingRepository;
        $this->productPriceRepository = $productPriceRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockPositionRepository = $productStockPositionRepository;

        DB::table('chimney_replacements')->delete();
        DB::table('chimney_products')->delete();
        DB::table('chimney_attribute_options')->delete();
        DB::table('chimney_attributes')->delete();
        Entities\Product::query()->update([
            'category_id' => null,
            'parent_id' => null
        ]);
        DB::table('categories')->delete();
        DB::statement("ALTER TABLE categories AUTO_INCREMENT = 1;");

        $data = Carbon::now();
        Log::channel('import')->info('Import start: '.$data);

        for ($i = 1; $line = fgetcsv($handle, 0, ';'); $i++) {
            if ($i % 100 === 0) {
                var_dump($i);
            }
            if ($i <= $this->startRow) {
                continue;
            }
            
            //intentionally variable assigning here, not an error
            if (!$categoryColumn = $this->getCategoryColumn($line)) {
                continue;
            }

            $array = $this->getProductArray($line, $categoryColumn);
            $categoryTree = $this->getCategoryTree($line, $categoryColumn);

            try {
                $multiCalcBase = trim($line[$categoryColumn + 12]);
                $multiCalcCurrent = trim($line[$categoryColumn + 8]);
                if (empty($array['symbol']) && empty($multiCalcBase) && empty($multiCalcCurrent)) {
                    $this->saveCategory($line, $categoryTree, $categoryColumn);
                } else {
                    $product = $this->saveProduct($array, $categoryTree);
                    if (!empty($multiCalcBase)) {
                        $this->productsRelated[$multiCalcBase] = $product->id;
                    } elseif (!empty($multiCalcCurrent) && !empty($this->productsRelated[$multiCalcCurrent])) {
                        $product->parent_id = $this->productsRelated[$multiCalcCurrent];
                        $product->save();
                    }
                }
            } catch (\Exception $e) {
                Log::channel('import')->debug("Row $i EXCEPTION: ".$e->getMessage());
                Log::channel('import')->debug($array);
            }
        }
        DB::table('import')->where('id', 1)->update(
            ['name' => 'Import products', 'processing' => 0]
        );
        DB::table('import')->where('id', 2)->update(
            ['name' => 'Import products done', 'last_import' => Carbon::now()]
        );
        Log::channel('import')->info('Import end: '.Carbon::now());
    }

    private function getUrl($url)
    {
        $imgUrlExploded           = explode('\\', $url);
        $imgUrlExploded           = end($imgUrlExploded);
        $imgUrlWebsite            = $this->imgStoragePath.DIRECTORY_SEPARATOR.$imgUrlExploded;
        $imgUrlWebsite            = Storage::url($imgUrlWebsite);
        return $imgUrlWebsite;
    }

    private function getShowOnPageParameter(array $line, int $columnIterator)
    {
        return array_key_exists($columnIterator + 14, $line) && $line[$columnIterator + 14] == 1;
    }

    private function getProductsOrder(array $line, int $columnIterator)
    {
        return ((int) $line[$columnIterator + 7]) ?: 1000000;
    }

    private function saveCategory($line, $categoryTree, $categoryColumn)
    {
        if (empty($line[301]) && empty($line[302])) {
            return;
        }

        $parent = &$this->getCategoryParent($categoryTree);

        $category = new Entities\Category;
        $category->name = end($categoryTree);
        $category->rewrite = $this->rewrite($category->name);
        $category->description = $line[310];
        $category->img = $line[303];
        $category->is_visible = $this->getShowOnPageParameter($line, $categoryColumn);
        $category->priority = $this->getProductsOrder($line, $categoryColumn);
        $category->parent_id = $parent['id'];

        if (!empty($category->img) && strpos($category->img, "\\")
        ) {
            $category->img = $this->getUrl($category->img);
        }
        $category->save();

        $parent['children'][$category->name] = ['id' => $category->id, 'children' => []];

        $replacements = $this->getChimneyReplacements($line);
        $this->appendChimneyAttributes($category, $line);
        $this->appendChimneyProducts($category, $line, 422, 40, false, $replacements);
        $this->appendChimneyProducts($category, $line, 518, 38, true);
    }

    private function &getCategoryParent($categoryTree, $isProduct = false)
    {
        $current = &$this->categories;
        $iMax = count($categoryTree) -  ($isProduct ? 0 : 1);
        foreach ($categoryTree as $i => $cat) {
            if (isset($current['children'][$cat])) {
                $current = &$current['children'][$cat];
                if ($i == $iMax) {
                    if ($isProduct) {
                        if (empty($current['children'])) {
                            return $current;
                        }
                        throw new \Exception("Products can be placed in deepest category only");
                    }
                    throw new \Exception($isProduct ? "Missing category for product (A)" : "Category already exists");
                }
                continue;
            } elseif ($i < $iMax) {
                if ($isProduct) {
                    if (empty($current['children'])) {
                        return $current;
                    }
                    throw new \Exception("Products can be placed in deepest category only");
                }
                throw new \Exception($isProduct ? "Missing category for product (B)" : "Missing category parent");
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
            $attribute = new Entities\ChimneyAttribute([
                'name' => $arr[0],
                'column_number' => $i - $start + 1
            ]);
            $category->chimneyAttributes()->save($attribute);
            $options = explode('|', $arr[1]);
            foreach ($options as $opt) {
                $opt = trim($opt);
                if (!$opt) {
                    continue;
                }
                $attribute->options()->save(new Entities\ChimneyAttributeOption(['name' => $opt]));
            }
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
                $product->replacements()->saveMany($replacements[$product->column_number]['products']);
            }
        }
    }

    private function getChimneyReplacements($line)
    {
        $replacements = [];
        $start = 462;
        for ($i = $start; $i < 518; $i += 4) {
            if (empty($line[$i]) || empty($line[$i+1]) || empty($line[$i+2]) || empty($line[$i+3])) {
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
                        'description' => $line[$i],
                        'img' => $this->getUrl($line[$i + 3]),
                        'products' => []
                    ];
                }
                $replacements[$productNumber]['products'][] = new Entities\ChimneyReplacement([
                    'product' => $arr[0],
                    'quantity' => $arr[1],
                ]);
            }
        }
        return $replacements;
    }

    private function saveProduct($array, $categoryTree)
    {
        $item = $this->productRepository->findWhere(['symbol' => $array['symbol']])->first();

        $category = $this->getCategoryParent($categoryTree, $array);
        $array['category_id'] = $category['id'];

        if ($item !== null) {
            $product = $this->productRepository->update($array, $item->id);
            $this->productPriceRepository->update(array_merge(['product_id', $product->id], $array), $product->id);
            $this->productPackingRepository->update(array_merge(['product_id', $product->id], $array), $product->id);
        } else {
            $product = $this->productRepository->create($array);
            $this->productPriceRepository->create(array_merge(['product_id' => $product->id], $array));
            $this->productPackingRepository->create(array_merge(['product_id' => $product->id], $array));
            $this->productStockRepository->create([
                'product_id' => $product->id,
                'quantity' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return $product;
    }

    private function getProductArray($line, $categoryColumn)
    {
        $array = [
            'name' => $line[4],
            'symbol' => $line[5],
            'manufacturer' => $line[16],
            'product_name_manufacturer' => $line[17],
            'symbol_name_manufacturer' => $line[18],
            'product_name_supplier' => $line[19],
            'product_name_supplier_on_documents' => $line[20],
            'product_name_on_collective_box' => $line[22],
            'product_symbol_on_supplier_documents' => $line[24],
            'product_symbol_on_collective_box' => $line[26],
            'ean_of_commercial_packing' => $line[29],
            'ean_of_collective_packing' => $line[30],
            'ean_of_biggest_packing' => $line[31],
            'vat' => $line[45],
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
            'weight_collective_unit' => (float) $line[103],
            'weight_trade_unit' => (float) $line[100],
            'weight_biggest_unit' => (float) $line[104],
            'weight_base_unit' => (float) $line[102],
            'net_purchase_price_commercial_unit' => $line[116],
            'net_purchase_price_calculated_unit' => $line[117],
            'net_purchase_price_basic_unit' => $line[118],
            'net_purchase_price_aggregate_unit' => $line[119],
            'net_purchase_price_the_largest_unit' => $line[120],
            'discount1' => $line[134],
            'discount2' => $line[135],
            'discount3' => $line[136],
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
            'gross_price_of_packing' => $line[252],
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
            'meta_title' => $line[309],
            'description' => $line[310],
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
            'number_of_items_per_30_kg' => $line[347],
            'packing_type' => $line[348],
            'number_of_pieces_in_total_volume' => $line[349],
            'recommended_courier' => $line[350],
            'courier_volume_factor' => $line[351],
            'max_pieces_in_one_package' => $line[352],
            'number_of_items_per_25_kg' => $line[353],
            'number_of_volume_items_for_paczkomat' => $line[354],
            'inpost_courier_type' => $line[355],
            'volume_ratio_paczkomat' => $line[356],
            'number_of_items_for_paczkomat' => $line[357],
            'set_rule' => $line[368],
            'set_symbol' => $line[369],
            'additional_payment_for_milling' => $line[473],
            'product_group_for_change_price' => $line[108],
            'products_related_to_the_automatic_price_change' => $line[110],
            'text_price_change' => $line[111],
            'text_price_change_data_first' => $line[112],
            'text_price_change_data_second' => $line[113],
            'text_price_change_data_third' => $line[114],
            'text_price_change_data_fourth' => $line[115],
            'subject_to_price_change' => $line[124],
            'pattern_to_set_the_price' => $line[129],
            'euro_exchange' => $line[250],
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
            'priority' => $this->getProductsOrder($line, $categoryColumn)
        ];

        foreach ($array as $key => $value) {
            if ($key === 'description' || $key === 'name' || $key === 'url') {
                $value       = iconv("utf-8", "ascii//IGNORE", $value);
                $array[$key] = $value;
            }
            if ($value === '#ARG!' || $value === '#DZIEL/0!' || $value === '$ADR!') {
                unset($array[$key]);
            }
            if (strpos($value, ',') !== false) {
                if ($key !== 'symbol') {
                    $value       = str_replace(',', '.', $value);
                    $array[$key] = $value;
                }
            }
            if ($key === 'subject_to_price_change') {
                if ($value == '1') {
                    unset($array['net_purchase_price_commercial_unit']);
                    unset($array['net_purchase_price_calculated_unit']);
                    unset($array['net_purchase_price_basic_unit']);
                    unset($array['net_purchase_price_aggregate_unit']);
                    unset($array['net_purchase_price_the_largest_unit']);
                    unset($array['net_purchase_price_commercial_unit_after_discounts']);
                    unset($array['net_purchase_price_calculated_unit_after_discounts']);
                    unset($array['net_purchase_price_basic_unit_after_discounts']);
                    unset($array['net_purchase_price_aggregate_unit_after_discounts']);
                    unset($array['net_purchase_price_the_largest_unit_after_discounts']);
                    unset($array['net_special_price_commercial_unit']);
                    unset($array['net_special_price_calculated_unit']);
                    unset($array['net_special_price_basic_unit']);
                    unset($array['net_special_price_aggregate_unit']);
                    unset($array['net_special_price_the_largest_unit']);
                    unset($array['net_selling_price_commercial_unit']);
                    unset($array['net_selling_price_basic_unit']);
                    unset($array['net_selling_price_calculated_unit']);
                    unset($array['net_selling_price_aggregate_unit']);
                    unset($array['net_selling_price_the_largest_unit']);
                }
            }
            /** MT-19 checking if url for product image exists and changing prefix of path to match server path */
            if (!empty($array['url']) && strpos($array['url'], "\\")
            ) {
                $array['url_for_website'] = $this->getUrl($array['url']);
            }
        }

        return $array;
    }

    private function getCategoryColumn($line)
    {
        for ($col = 598; $col <= count($line) - 16; $col += 16) {
            if (!empty($line[$col])) {
                return $col;
            }
        }
        return null;
    }

    private function getCategoryTree($line, $categoryColumn)
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

    private function rewrite($string)
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
}
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

        DB::table('chimney_products')->delete();
        DB::table('chimney_attribute_options')->delete();
        DB::table('chimney_attributes')->delete();
        DB::table('category_details')->delete();
        DB::table('products')->delete();

        $data = Carbon::now();
        Log::channel('import')->info('Import start: '.$data);

        for ($i = 1; $line = fgetcsv($handle, 0, ';'); $i++) {
            /** token produktu i kategori */
            $tokenProductAndCategory = (string) Str::uuid();
            if ($i % 100 === 0) {
                var_dump($i);
            }
            if ($i <= $this->startRow) {
                continue;
            }

            $array          = [
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
            ];
            /** MT-20 import kategorii produktów - podejście 2 */
            $categoryColumn = null;
            //598 - 100.dzialki budowlane - pierwsza kategoria
            //1030 - 370. - ostatnia jawna kategoria
            //1046 - TWSU etc.
            //1078 i dalej już tylko śmieci
            for ($col = 598; $col <= 1046; $col += 16) {
                if (!empty($line[$col])) {
                    $categoryColumn = $col;
                    break;
                }
            }
            if ($categoryColumn !== null) {
                $category = [];
                $categoryColumn++;
                for ($j = 0; $j < 8; $j++) {
                    if ((string) $line[$categoryColumn] !== '') {
                        $category[] = $line[$categoryColumn++];
                    }
                    $array['show_on_page'] = $this->getShowOnPageParameter($line, $col);
                    $array['priority']     = $this->getProductsOrder($line, $col);
                }
                $array['token_prod_cat'] = $tokenProductAndCategory;
                $array['product_url']    = implode('/', $category);
            }

            try {
                foreach ($array as $key => $value) {
                    if ($key === 'description' || $key === 'name' || $key === 'url') {
                        $value       = iconv("utf-8", "ascii//IGNORE", $value);
                        $array[$key] = $value;
                    }
                    if ($value === null || $value === '') {
                        unset($array[$key]);
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
                        $imgUrlExploded           = explode('\\', $array['url']);
                        $imgUrlExploded           = end($imgUrlExploded);
                        $imgUrlWebsite            = $this->imgStoragePath.DIRECTORY_SEPARATOR.$imgUrlExploded;
                        $imgUrlWebsite            = Storage::url($imgUrlWebsite);
                        $array['url_for_website'] = $imgUrlWebsite;
                    }
                }

                $this->saveCategory($line, $tokenProductAndCategory, $array);

                if (empty($array['symbol'])) {
                    continue;
                }

                $this->createProduct($array);
            } catch (\Exception $exception) {
                Log::channel('import')->debug($array);
                Log::channel('import')->debug($exception);
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

    private function getShowOnPageParameter(array $line, int $columnIterator)
    {
        if (array_key_exists($columnIterator + 14, $line) && $line[$columnIterator + 14] == 1
        ) {
            return true;
        }
        return false;
    }

    private function getProductsOrder(array $line, int $columnIterator)
    {
        if (array_key_exists($columnIterator + 7, $line)) {
            $l = (int) $line[$columnIterator + 7];
            $l = $l ?? 0;
            return $l;
        }
        return 0;
    }

    private function saveCategory($line, $token, $productArray)
    {
        if (empty($line[301]) && empty($line[302])) {
            return;
        }

        $categoryDetails = new Entities\CategoryDetail;
        $categoryDetails->category_navigation = $line[300];
        $categoryDetails->category = $line[301];
        $categoryDetails->category_edited = $line[302];
        $categoryDetails->description = $line[310];
        $categoryDetails->img_url = $line[303];
        $categoryDetails->token_prod_cat = $token;

        if (!empty($categoryDetails->img_url) && strpos($categoryDetails->img_url, "\\")
        ) {
            $imgUrlExploded                     = explode('\\', $categoryDetails->img_url);
            $imgUrlExploded                     = end($imgUrlExploded);
            $imgUrlWebsite                      = $this->imgStoragePath.DIRECTORY_SEPARATOR.$imgUrlExploded;
            $imgUrlWebsite                      = Storage::url($imgUrlWebsite);
            $categoryDetails->url_for_website   = $imgUrlWebsite;
        }
        $categoryDetails->save();
        $this->appendChimneyAttributes($categoryDetails, $line);
        $this->appendChimneyProducts($categoryDetails, $line, 422, 40, false);
        $this->appendChimneyProducts($categoryDetails, $line, 518, 30, true);
        if (count($categoryDetails->chimneyAttributes) > 0) {
            $this->createProduct($productArray);
        }
    }
    
    private function appendChimneyAttributes($categoryDetails, $line)
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
            $categoryDetails->chimneyAttributes()->save($attribute);
            $options = explode('|', $arr[1]);
            foreach ($options as $opt) {
                $attribute->options()->save(new Entities\ChimneyAttributeOption(['name' => $opt]));
            }
        }
    }

    private function appendChimneyProducts($categoryDetails, $line, $start, $count, $optional)
    {
        for ($i = $start; $i < $start + $count; $i++) {
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
            $categoryDetails->chimneyProducts()->save($product);
        }
    }

    private function createProduct($array)
    {
        if (empty($array['symbol'])) {
            $array['symbol'] = 'fake-'.Str::random();
        }
        $item = $this->productRepository->findWhere(['symbol' => $array['symbol']])->first();

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
    }
}
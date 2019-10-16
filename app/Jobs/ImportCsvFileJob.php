<?php

namespace App\Jobs;

use App\Repositories\ProductPackingRepository;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStockPositionRepository;
use App\Repositories\ProductStockRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ImportCsvFileJob
 * @package App\Jobs
 */
class ImportCsvFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    protected $path;

    /**
     * @var ProductRepository
     */
    protected $repository;

    /**
     * @var ProductPackingRepository
     */
    protected $productPackingRepository;

    /**
     * @var ProductPriceRepository
     */
    protected $productPriceRepository;

    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * @var ProductStockPositionRepository
     */
    protected $productStockPositionRepository;

    protected $start;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($start = 5)
    {
        $this->path = Storage::path('public/Baza.csv');
        $this->start = $start;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ProductRepository $repository,
        ProductPackingRepository $productPackingRepository,
        ProductPriceRepository $productPriceRepository,
        ProductStockRepository $productStockRepository,
        ProductStockPositionRepository $productStockPositionRepository
    ) {
        $this->repository = $repository;
        $this->productPackingRepository = $productPackingRepository;
        $this->productPriceRepository = $productPriceRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productStockPositionRepository = $productStockPositionRepository;

        $handle = fopen($this->path, 'r');
        if ($handle) {
            $i = 1;
            $data = Carbon::now();
            Log::channel('import')->info('Import start: ' . $data);
            while ($line = fgetcsv($handle, 0, ';')) {
                if ($i > $this->start) {
                    if (strpos($line[5], '-')) {
                        $valExp = explode('-', $line[5]);
                        if (end($valExp) > 0) {
                            $item = $this->repository->findWhere(['symbol' => $line[5]])->first();
                            if (!empty($item)) {
                                $item->packing->delete();
                                if (!empty($item->photos)) {
                                    foreach ($item->photos as $photo) {
                                        $photo->delete();
                                    }
                                }
                                $item->price->delete();
                                if (!empty($item->stock->position)) {
                                    foreach ($item->stock->position as $position) {
                                        $position->delete();
                                    }
                                }
                                if (!empty($item->stock->logs)) {
                                    foreach ($item->stock->logs as $log) {
                                        $log->delete();
                                    }
                                }
                                $item->stock->delete();
                                $item->delete();
                            }
                            continue;
                        }
                    }
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
                        'weight_collective_unit' => (float)$line[103],
                        'weight_trade_unit' => (float)$line[100],
                        'weight_biggest_unit' => (float)$line[104],
                        'weight_base_unit' => (float)$line[102],
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
                        'priority' => $line[308],
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
                        'product_url' => $line[319] . $line[320] . $line[321] . $line[322] . $line[323],
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
                        'date_of_price_change' => $line[106] != null ? new Carbon($line[106]) : null,
                        'date_of_the_new_prices' => $line[107] != null ? new Carbon($line[107]) : null,
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
//                        'gross_selling_price_basic_unit' => $line[254],
//                        'gross_purchase_price_basic_unit_after_discounts' => $line[195],
//                        'gross_selling_price_commercial_unit' => $line[252],
//                        'gross_purchase_price_commercial_unit_after_discounts' => $line[193],
//                        'gross_selling_price_calculated_unit' => $line[253],
//                        'gross_purchase_price_calculated_unit_after_discounts' => $line[194],
//                        'gross_selling_price_aggregate_unit' => $line[255],
//                        'gross_purchase_price_aggregate_unit_after_discounts' => $line[196],
//                        'gross_selling_price_the_largest_unit' => $line[256],
//                        'gross_purchase_price_the_largest_unit_after_discounts' => $line[197],
                    ];
                    try {
                        if ($array['symbol'] === null || $array['symbol'] === '') {
                            continue;
                        }
                        foreach ($array as $key => $value) {
                            if ($key === 'description' || $key === 'name' || $key === 'url') {
                                $value = iconv("utf-8", "ascii//IGNORE", $value);;
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
                                    $value = str_replace(',', '.', $value);
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
                        }

                        $item = $this->repository->findWhere(['symbol' => $array['symbol']])->first();
                        if ($item !== null) {
                            $product = $this->repository->update($array, $item->id);
                            $this->productPriceRepository->update(array_merge(['product_id', $product->id], $array),
                                $product->id);
                            $this->productPackingRepository->update(array_merge(['product_id', $product->id], $array),
                                $product->id);
                        } else {
                            $product = $this->repository->create($array);
                            $this->productPriceRepository->create(array_merge(['product_id' => $product->id], $array));
                            $this->productPackingRepository->create(array_merge(['product_id' => $product->id],
                                $array));
                        }
                    } catch (\Exception $exception) {
                        Log::channel('import')->debug($array);
                        Log::channel('import')->debug($exception);
                    }
                }
                var_dump($i);
                $i++;
            }
        }

        DB::table('import')->where('id', 1)->update(
            ['name' => 'Import products', 'processing' => 0]
        );
        DB::table('import')->where('id', 2)->update(
            ['name' => 'Import products done', 'last_import' => Carbon::now()]
        );

        Log::channel('import')->info('Import end: ' . Carbon::now());
    }

}

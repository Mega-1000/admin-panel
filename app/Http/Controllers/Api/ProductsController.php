<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\Api\Products\ProductPricesUpdateRequest;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProductsController
{
    use ApiResponsesTrait;

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

}
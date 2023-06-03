<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Order;
use App\Entities\Product;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Helpers\ProductSymbolCoreExtractor;
use App\Repositories\FirmRepository;
use App\Repositories\OrderAddressRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

readonly class ProductService
{
    public function __construct(
        protected ProductRepository    $productRepository,
        private OrderAddressRepository $orderAddressRepository,
        private FirmRepository         $firmRepository,
        private WarehouseRepository    $warehouseRepository
    ) {}

    public function checkForSimilarProducts(int $productId): ?Collection
    {
        $product = $this->productRepository->find($productId);
        $productSymbolCore = ProductSymbolCoreExtractor::getProductSymbolCore($product->symbol);

        return $this->productRepository->findWhere([
            ['symbol', 'LIKE', '%' . $productSymbolCore . '%']
        ]);
    }

    public function getStockProduct(int $productId): ?Product
    {
        $similarProducts = $this->checkForSimilarProducts($productId);

        return $similarProducts->first(function ($similarProduct) {
            return $similarProduct->stock_product === true;
        });
    }

    /**
     * Get users based of all order variations for auction
     *
     * @param Order $order
     * @return Collection
     * @throws DeliverAddressNotFoundException
     */
    public function getUsersFromVariations(Order $order): Collection
    {
        $orders = collect($this->getVariations($order))->first();
        $users = new Collection();

        foreach ($orders as $order) {
            $orderObj = Product::find($order['id']);
            $orderObj->firm->employees->each(function ($employee) use ($order, &$users) {
                if ($employee->status !== 'PENDING') {
                    $users[] = $employee;

                    $user = $users->last();
                    $user->radius = $order['radius'];
                }
            });
        }

        return $users->unique('email');
    }


    /**
     * GetVariations
     *
     * @param Order $order
     *
     * @return array
     * @throws DeliverAddressNotFoundException
     */
    public function getVariations(Order $order): array
    {
        $productsVariation = [];

        $orderDeliveryAddress = $this->orderAddressRepository->findWhere([
            "order_id" => $order->id,
            'type' => 'DELIVERY_ADDRESS',
        ])->first();

        $deliveryAddressLatLon = DB::table('postal_code_lat_lon')->where('postal_code', $orderDeliveryAddress->postal_code)->get()->first();
        if ($deliveryAddressLatLon === null) {
            throw new DeliverAddressNotFoundException();
        }

        foreach ($order->items as $product) {
            if ($product->product->product_group == null) {
                continue;
            }
            $productVar = $this->productRepository->findByField('product_group', $product->product->product_group);
            foreach ($productVar as $prod) {
                $firm = $this->firmRepository->findByField('symbol', $prod->product_name_supplier);
                $radius = 0;

                if ($firm->isEmpty() || $firm->first->id->warehouses->isEmpty()) {
                    continue;
                }

                if ($deliveryAddressLatLon != null) {
                    $raw = DB::selectOne(
                        'SELECT w.id, pc.latitude, pc.longitude, 1.609344 * SQRT(
                        POW(69.1 * (pc.latitude - :latitude), 2) +
                        POW(69.1 * (:longitude - pc.longitude) * COS(pc.latitude / 57.3), 2)) AS distance
                        FROM postal_code_lat_lon pc
                             JOIN warehouse_addresses wa on pc.postal_code = wa.postal_code
                             JOIN warehouses w on wa.warehouse_id = w.id
                        WHERE w.firm_id = :firmId AND w.status = \'ACTIVE\'
                        ORDER BY distance
                    limit 1',
                        [
                            'latitude' => $deliveryAddressLatLon->latitude,
                            'longitude' => $deliveryAddressLatLon->longitude,
                            'firmId' => $firm->first->id->id
                        ]
                    );
                    if (!empty($raw)) {
                        $radius = $raw->distance;
                    } else {
                        continue;
                    }
                }

                switch ($prod->variation_unit) {
                    case 'UB':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity * $prod->packing->numbers_of_basic_commercial_units_in_pack;
                        break;
                    case 'UC':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity;
                        break;
                    case 'UCA':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity * $prod->packing->numbers_of_basic_commercial_units_in_pack / $prod->packing->unit_consumption;
                        break;
                    case 'UCO':
                        $unitData = $prod->price->gross_selling_price_basic_unit * $product->quantity / $prod->packing->number_of_sale_units_in_the_pack;
                        break;
                    default:
                        Log::info(
                            'Invalid variation unit: ' . $prod->variation_unit,
                            ['product_id' => $prod->id, 'class' => get_class($this), 'line' => __LINE__]
                        );
                }
                $warehouse = $this->warehouseRepository->find($raw->id);
                if (
                    $radius > $warehouse->radius ||
                    $prod->price->gross_selling_price_commercial_unit === null ||
                    $prod->price->gross_selling_price_basic_unit === null ||
                    $prod->price->gross_selling_price_calculated_unit === null
                ) {
                    continue;
                }

                if ($unitData == 0) {
                    $diff = null;
                } else if ($prod->id == $product->product->id) {
                    $diff = 0.0;
                } else {
                    $diff = number_format((($product->gross_selling_price_commercial_unit * $product->quantity) - number_format($unitData, 2, '.', '')), 2, '.', '');
                }

                $array = [
                    'id' => $prod->id,
                    'name' => $prod->name,
                    'gross_selling_price_commercial_unit' => $prod->price->gross_selling_price_commercial_unit,
                    'gross_selling_price_basic_unit' => $prod->price->gross_selling_price_basic_unit,
                    'gross_selling_price_calculated_unit' => $prod->price->gross_selling_price_calculated_unit,
                    'sum' => number_format($unitData, 2, '.', ''),
                    'different' => $diff,
                    'radius' => $radius,
                    'product_name_supplier' => $prod->product_name_supplier,
                    'phone' => $firm->first->id->phone,
                    'review' => $prod->review,
                    'quality' => $prod->quality,
                    'quality_to_price' => $prod->quality_to_price,
                    'comments' => $prod->comments,
                    'variation_group' => $prod->variation_group,
                    'value_of_the_order_for_free_transport' => $prod->value_of_the_order_for_free_transport,
                    'warehouse_property' => $warehouse->property->comments
                ];
                $productsVariation[$product->product->id][] = $array;
            }
            foreach ($productsVariation as $variation) {
                if (isset($productsVariation[$product->product->id])) {
                    $productsVariation[$product->product->id] = collect($variation)->sortBy('different', 1, true);
                }
            }
        }

        return $productsVariation;
    }
}

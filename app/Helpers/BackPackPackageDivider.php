<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\OrderOtherPackage;
use App\Entities\OrderPackage;
use App\Entities\PackageTemplate;
use App\Entities\Product;
use App\Helpers\interfaces\iDividable;
use Carbon\Carbon;

class BackPackPackageDivider implements iDividable
{
    public static function calculatePackagesForOrder($order): void
    {
        if ($order->sello_id) {
            return;
        }
        $divider = new BackPackPackageDivider();
        $items = $order->items->map(function ($item) {
            $item->id = $item->product_id;
            $item->amount = $item->quantity;
            return $item;
        });
        $divider->divide($items->toArray(), $order);
    }

    public function divide($data, Order $order): bool
    {
        $packages = self::divideToPackages($data);
        self::createPackages($packages, $order->id);
        if (count($packages['not_calculated']) == 0) {
            $canPay = true;
        } else {
            self::saveNotCalculable($packages, $order->id);
            $canPay = false;
        }
        self::saveFactory($packages, $order->id);
        return $canPay;
    }

    public static function createPackage($packTemplate, $orderId, $packageNumber)
    {
        $pack = new OrderPackage();
        $pack->order_id = $orderId;
        $pack->size_a = $packTemplate->sizeA;
        $pack->size_b = $packTemplate->sizeB;
        $pack->size_c = $packTemplate->sizeC;
        $pack->delivery_courier_name = $packTemplate->delivery_courier_name;
        $pack->service_courier_name = $packTemplate->service_courier_name;
        $pack->weight = $packTemplate->weight;
        $pack->number = $packageNumber;
        $pack->chosen_data_template = $packTemplate->name;
        $pack->cost_for_client = $packTemplate->approx_cost_client;
        $pack->cost_for_company = $packTemplate->approx_cost_firm;
        $pack->content = $packTemplate->content ?? 'Materiały budowlane';
        $pack->notices = $orderId . '/' . $packageNumber;
        $pack->symbol = $packTemplate->symbol;
        $helper = new OrderPackagesDataHelper();

        if (file_exists(storage_path('app/public/protocols/day-close-protocol-' . $packTemplate->delivery_courier_name . '-' . Carbon::today()->toDateString() . '.pdf'))) {
            $date = Carbon::today()->addWeekday();
        } else if ($packTemplate->accept_time) {
            $date = $helper->calculateShipmentDate($packTemplate->accept_time, $packTemplate->accept_time);
        } else {
            $date = $helper->calculateShipmentDate(9, 9);
        }
        $pack->shipment_date = $date;
        $pack->cost_for_client = $packTemplate->approx_cost_client;
        $pack->quantity = 1;
        $pack->status = 'NEW';
        $pack->container_type = $packTemplate->container_type;
        $pack->packing_type = $packTemplate->packing_type;
        $pack->shape = $packTemplate->shape;
        $pack->save();
        return $pack;
    }

    private static function saveFactory(array $packages, $orderId)
    {
        if (count($packages['transport_groups']) == 0) {
            return;
        }
        foreach ($packages['transport_groups'] as $transport_group) {
            $container = new OrderOtherPackage();
            $container->type = 'from_factory';
            $container->description = $transport_group['name'];
            $container->order_id = $orderId;
            $container->price = $transport_group['transport_price'];
            $container->save();
            foreach ($transport_group['items'] as $item) {
                $container->products()->attach($item->id, ['quantity' => $item->quantity]);
            }
        }
    }

    private static function createPackages(array $packages, $orderId)
    {
        $packageNumber = 1;
        foreach ($packages['packages'] as $package) {
            if (!empty($package->type)) {
                $packTemplate = PackageTemplate::where('symbol', $package->type)->firstOrFail();
                $pack = self::createPackage($packTemplate, $orderId, $packageNumber);
                $products = [];
                foreach ($package->packagesList as $singlePack) {
                    foreach ($singlePack->productList as $product) {
                        $quantity = empty($products[$product->id]) ? $product->quantity : $products[$product->id] + $product->quantity;
                        $products[$product->id] = $quantity;
                    }
                }
                foreach ($products as $k => $quantity) {
                    $pack->packedProducts()->attach($k, ['quantity' => $quantity]);
                }
            }
            if (!empty($package->packageName)) {
                $packTemplate = PackageTemplate::where('symbol', $package->packageName)->firstOrFail();
                $pack = self::createPackage($packTemplate, $orderId, $packageNumber);
                $products = [];
                foreach ($package->productList as $product) {
                    $quantity = empty($products[$product->id]) ? $product->quantity : $products[$product->id] + $product->quantity;
                    $products[$product->id] = $quantity;
                }
                foreach ($products as $k => $quantity) {
                    $pack->packedProducts()->attach($k, ['quantity' => $quantity]);
                }
            }
            $packageNumber++;
        }
    }

    private static function saveNotCalculable(array $packages, $orderId)
    {
        $container = new OrderOtherPackage();
        $container->type = 'not_calculable';
        $container->order_id = $orderId;
        $container->save();
        foreach ($packages['not_calculated'] as $item) {
            $container->products()->attach($item->id, ['quantity' => $item->quantity]);
        }
    }

    private static function divideToPackages($orderedItems): array
    {
        $prodIds = [];
        $orderCollection = collect($orderedItems);
        foreach ($orderCollection as $items) {
            $prodIds [] = $items['id'];
        }
        $prodList = Product::whereIn('id', $prodIds)->with('tradeGroups')->with('price')->get();
        $prodList->map(function ($item) use ($orderCollection) {
            $product = $orderCollection->where('id', $item->id)->first();
            $item->quantity = $product['amount'];
        });
        $warehouse = new PackageDivider();
        $warehouse->setItems($prodList);
        return $warehouse->divide();
    }
}

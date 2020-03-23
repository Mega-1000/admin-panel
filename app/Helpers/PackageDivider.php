<?php

namespace App\Helpers;

use App\Helpers\interfaces\iPackageDivider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PackageDivider implements iPackageDivider
{
    const TRANSPORT_GROUPS = 'transport_group';
    private $itemList;
    private const LONG = 'long';
    private const NOT_CALCULABLE = 'not_calculable';

    public function setItems(Collection $itemList)
    {
        $this->itemList = $itemList;
    }

    public function divide()
    {
        $sorted = $this->groupByPackageType();
        $parcels = $this->divideToParcels($sorted);
        return $this->divideToPalette($parcels);
    }

    private function groupByPackageType()
    {
        $warehouses = [];
        foreach ($this->itemList as $product) {
            if ($product->isInTransportGroup()) {
                $warehouses [self::TRANSPORT_GROUPS] [$product->trade_group_name] [] = $product;
            } else if ($product->hasAllTransportParameters()) {
                $warehouses = $this->insertToWarehouseArray($product, $warehouses);
            } else {
                $warehouses[self::NOT_CALCULABLE] [] = $product;
            }
        }
        return $warehouses;
    }

    private function divideToParcels($sorted)
    {
        $divided = [];
        $notCalculated = isset($sorted[self::NOT_CALCULABLE]) ? $sorted[self::NOT_CALCULABLE] : [];
        unset($sorted[self::NOT_CALCULABLE]);
        $transportCalculations = [];
        foreach ($sorted as $key => $items) {
            if ($key === self::TRANSPORT_GROUPS) {
                $transportCalculations = $this->calculateTransportGroups($items);
            }
        }
        unset($sorted[self::TRANSPORT_GROUPS]);
        if (isset($transportCalculations['cantsend'])) {
            foreach ($transportCalculations['cantsend'] as $item) {
                if ($item->hasAllTransportParameters()) {
                    $sorted = $this->insertToWarehouseArray($item, $sorted);
                } else {
                    $sorted[self::NOT_CALCULABLE] [] = $item;
                }
            }
            unset($transportCalculations['cantsend']);
        }
        $failed = [];
        $totalPacks = [];
        foreach ($sorted as $key => $items) {
            if (strpos($key, self::LONG)) {
                $items = $this->sortByLength($items);
                ['packages' => $divided, 'failed' => $failed] = $this->calculatePackages($items, true);
            } elseif ($key !== self::TRANSPORT_GROUPS) {
                uasort($items, array('App\Helpers\PackageDivider', 'weightAndVolumeSort'));
                ['packages' => $divided, 'failed' => $failed] = $this->calculatePackages($items);
            }
            $totalPacks = array_merge($divided, $totalPacks);
        }
        return ['packages' => array_values($totalPacks),
            'transport_groups' => isset($transportCalculations['calculated']) ? $transportCalculations['calculated'] : [],
            'not_calculated' => array_merge($notCalculated, $failed)];
    }

    private function calculatePackages($items, $isLong = false)
    {
        $packages = [];
        $failed = [];
        foreach ($items as $item) {
            ['packages' => $package, 'failed' => $fail] = $this->createHomoPackage($item, $isLong);
            if ($package) {
                $packages = array_merge($packages, $package);
            }
            if ($fail) {
                $failed[] = $fail;
            }
        }
        array_reverse($packages);
        foreach ($packages as $key => $singlePackage) {
            unset($packages[$key]);
            if (!$this->packAsMuchYouCan(clone $singlePackage, $packages)) {
                $packages[$key] = $singlePackage;
            }
        }
        foreach ($packages as $pack) {
            $pack->removeEmpty();
            $pack->productList = $pack->productList->values();
        }
        return ['packages' => $packages, 'failed' => $failed];
    }

    private static function weightAndVolumeSort($first, $second)
    {
        if ($first->weight_trade_unit == $second->weight_trade_unit) {
            if ($first->packing->getVolume() == $second->packing->getVolume()) {
                return 0;
            }
            return $first->packing->getVolume() > $second->packing->getVolume() ? -1 : 1;
        }
        return $first->weight_trade_unit > $second->weight_trade_unit ? -1 : 1;
    }

    private function sortByLength($items)
    {
        $sortByLength = function ($first, $second) {
            if ($first->packing->dimension_x == $second->packing->dimension_x) {
                return self::weightAndVolumeSort($first, $second);
            }
            return $first->packing->dimension_x > $second->packing->dimension_x ? -1 : 1;
        };
        uasort($items, $sortByLength);
        return $items;
    }

    private function createHomoPackage($item, $isLong)
    {
        $packageName = sprintf("%s_%s",
            $item->packing->recommended_courier,
            $item->packing->packing_name);
        try {
            $package = new Package($packageName, env('PACKAGE_DIVIDE_MARGIN'));
        } catch (\Exception $exception) {
            return ['packages' => false, 'failed' => $item];
        }
        $package->setIsLong($isLong);
        $packageList = [$package];
        do {
            try {
                $package->addItem($item, 1);
                $item->quantity -= 1;
            } catch (\Exception $exception) {
                if ($exception->getMessage() != Package::CAN_NOT_ADD_MORE) {
                    Log::error('Błąd budownaia paczek: ' . $exception->getMessage(), ['class' => get_class($this), 'line' => __LINE__]);
                } else if ($package->getProducts()->count() === 0) {
                    return ['packages' => false, 'failed' => $item];
                } else {
                    $package = new Package($packageName, env('PACKAGE_DIVIDE_MARGIN'));
                    $packageList[] = $package;
                }
            }
        } while ($item->quantity > 0);
        return ['packages' => $packageList, 'failed' => false];
    }

    private function calculateTransportGroups($sorted)
    {
        $sums = [];
        foreach ($sorted as $key => $group) {
            $sums = array_merge($sums, [$key => $this->sumGroupWeightAndPrice($group)]);
        }
        $cantSend = [];
        $calculated = [];
        foreach ($sums as $key => $sum) {
            $priceCondition = $sum['factory_group']->where('type', 'price')->first();
            $weightCondition = $sum['factory_group']->where('type', 'weight')->first();
            $firstPrice = $this->checkConditions($sum['price'], $priceCondition);
            $secondPrice = $this->checkConditions($sum['weight'], $weightCondition);
            unset($sum['factory_group']);
            if ($firstPrice === false && $secondPrice === false) {
                $cantSend = array_merge($cantSend, $sum['items']);
                continue;
            } else if ($firstPrice === false || $secondPrice === false) {
                $sum['transport_price'] = $firstPrice ? $firstPrice : $secondPrice;
            } else {
                $sum['transport_price'] = $firstPrice < $secondPrice ? $firstPrice : $secondPrice;
            }
            array_push($calculated, $sum);
        }
        return ['calculated' => $calculated, 'cantsend' => $cantSend];
    }

    private function sumGroupWeightAndPrice($group)
    {
        $sums = [];
        foreach ($group as $item) {
            $price = isset($sums['price']) ? $sums['price'] : 0;
            $weight = isset($sums['weight']) ? $sums['weight'] : 0;
            $items = isset($sums['items']) ? $sums['items'] : [];
            array_push($items, $item);
            $sums = ['price' => $price + $item->quantity * $item->price->net_purchase_price_commercial_unit,
                'weight' => $weight + $item->quantity * $item->weight_trade_unit,
                'factory_group' => $item->tradeGroups,
                'items' => $items,
                'name' => $items[0]->trade_group_name,
                'displayed_group_name' => $items[0]->displayed_group_name ?: $items[0]->trade_group_name];
        }
        return $sums;
    }

    private function packAsMuchYouCan($packageToSplit, array &$allPackages)
    {
        foreach ($allPackages as $package) {
            $this->repackToAnotherPackage($packageToSplit, $package);
        }
        return $packageToSplit->getProducts()->count() === 0;
    }

    private function repackToAnotherPackage(&$packageToSplit, Package &$package)
    {
        foreach ($packageToSplit->getProducts() as $product) {
            while ($product->quantity > 0 && $package->canPutNewItem($product, 1)) {
                try {
                    $package->addItem($product, 1);
                    $product->quantity--;
                } catch (\Exception $exception) {
                    if ($exception->getMessage() != Package::CAN_NOT_ADD_MORE) {
                        Log::error('Package Building: repack', [$exception->getMessage()]);
                    }
                }
            }
        }
        $packageToSplit->removeEmpty();
    }

    private function checkConditions($sum, $condition)
    {
        if (empty($condition)) {
            return false;
        }
        if ($sum > $condition->first_condition) {
            return $condition->first_price;
        } else if (isset($condition->second_condition) && $sum > $condition->second_condition) {
            return $condition->second_price;
        } else if (isset($condition->second_condition) && $sum > $condition->third_condition) {
            return $condition->third_price;
        }
        return false;
    }

    private function insertToWarehouseArray($product, array $warehouses): array
    {
        if ($product->packing()->first()->isLong()) {
            $packingName = self::LONG;
        } else {
            $packingName = $product->packing->packing_name;
        }
        $warehouses [sprintf("%s_%s_%s",
            $product->packing->warehouse,
            $product->packing->recommended_courier,
            $packingName)] [] = $product;
        return $warehouses;
    }

    private function divideToPalette(array $parcels)
    {
        $packageCollection = collect($parcels['packages']);
        $packageCollection->sort(function (Package $first, Package $second) {
            if ($first->getTotalVolume() == $second->getTotalVolume()) {
                return 0;
            }
            return $first->getTotalVolume() > $second->getTotalVolume() ? -1 : 1;
        })->values()->all();

        $i = 0;
        $palette = new Palette();
        $palettes = [$palette];
        while ($packageCollection->count() > 0) {
            $package = $packageCollection->get($i);
            if (isset($package)) {
                if ($palette->canPutNewItem($package)) {
                    $palette->addItem($package);
                    $packageCollection->forget($i);
                    $packageCollection = $packageCollection->values();
                } else {
                    $palette = new Palette();
                    array_push($palettes, $palette);
                    $i++;
                }
            } else {
                $i = 0;
            }
        }
        $parcels['packages'] = [];
        foreach ($palettes as $k => $palette) {
            try {
                $palette->tryFitInSmallerPalette();
            } catch (\Exception $exception) {
                Log::info('Problem z pasowaniem palety na mniejszą: ',
                    ['exception' => $exception->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
                );
            }
            $palette->setPackageCost();
            if ($palette->price > $palette->packagesCost) {
                $parcels['packages'] = array_merge($parcels['packages'], $palette->packagesList->toArray());
            } else {
                $parcels['packages'][] = $palette;
            }
        }
        return $parcels;
    }
}

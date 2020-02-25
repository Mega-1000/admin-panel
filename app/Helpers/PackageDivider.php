<?php

namespace App\Helpers;

use App\Helpers\interfaces\iPackageDivider;
use Illuminate\Database\Eloquent\Collection;

class PackageDivider implements iPackageDivider
{
    const TRANSPORT_GROUPS = 'transport_group';
    //todo make this adjustable
    const MARGIN = 1.3;
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
            if ($product->hasAllTransportParameters()) {
                if ($product->isInTransportGroup()) {
                    $warehouses [self::TRANSPORT_GROUPS] [$product->trade_group_name] [] = $product;
                } else {
                    $warehouses = $this->insertToWarehouseArray($product, $warehouses);
                }
            } else {
                $warehouses[self::NOT_CALCULABLE] [] = $product;
            }
        }
        return $warehouses;
    }

    private function divideToParcels($sorted)
    {
        $divided = [];
        $allCalculated = empty($sorted[self::NOT_CALCULABLE]);
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
                $sorted = $this->insertToWarehouseArray($item, $sorted);
            }
            unset($transportCalculations['cantsend']);
        }
        foreach ($sorted as $key => $items) {
            if (strpos($key, self::LONG)) {
                $items = $this->sortByLength($items);
                $divided = $this->calculatePackages($items, true);
            } elseif ($key !== self::TRANSPORT_GROUPS) {
                uasort($items, array('App\Helpers\PackageDivider', 'weightAndVolumeSort'));
                $divided = $this->calculatePackages($items);
            }
        }
        return ['packages' => array_values($divided),
            'transport_groups' => isset($transportCalculations['calculated']) ? $transportCalculations['calculated'] : [],
            'not_calculated' => $notCalculated];
    }

    private function calculatePackages($items, $isLong = false)
    {
        $packages = [];
        foreach ($items as $item) {
            $packages = array_merge($packages, $this->createHomoPackage($item, $isLong));
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
            $pack->productList = array_values($pack->productList->toArray());
        }
        return $packages;
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
        $package = new Package($packageName, self::MARGIN);
        $package->setIsLong($isLong);
        $packageList = [$package];
        do {
            try {
                $package->addItem($item, 1);
                $item->quantity -= 1;
            } catch (\Exception $exception) {
                if ($exception->getMessage() != Package::CAN_NOT_ADD_MORE) {
                    error_log($exception->getMessage());
                } else if ($package->getProducts()->count() === 0) {
                    return ["Element nie mieści się w paczce"];
                } else {
                    $package = new Package($packageName, self::MARGIN);
                    $packageList[] = $package;
                }
            }
        } while ($item->quantity > 0);
        return $packageList;
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
                'name' => $items[0]->trade_group_name];
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
                        error_log($exception->getMessage());
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
        return $parcels;
    }
}

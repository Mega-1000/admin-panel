<?php

namespace App\Helpers;

use App\Entities\Product;
use Illuminate\Database\Eloquent\Collection;

class PackageDivider
{
    const TRANSPORT_GROUPS = 'transport_group';
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
        return $this->divideToParcels($sorted);
    }

    private function groupByPackageType()
    {
        $warehouses = [];

        foreach ($this->itemList as $product) {
            $product->quantity = 5; //todo remove
            if ($product->hasAllTransportParameters()) {
                if ($product->packing()->first()->isLong()) {
                    $warehouses [sprintf("%s_%s_%s",
                        $product->packing->warehouse,
                        $product->packing->recommended_courier,
                        self::LONG)] [] = $product;
                } else if ($product->isInTransportGroup()) {
                    $warehouses [self::TRANSPORT_GROUPS] [] = $product;
                } else {
                    $warehouses [$product->packing->warehouse . '_' .
                    $product->packing->recommended_courier . '_' .
                    $product->packing->packing_name] [] = $product;
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
        unset($sorted[self::NOT_CALCULABLE]);
        foreach ($sorted as $key => $items) {
            if (strpos($key, self::LONG)) {
                $this->calculateLongPackages($items);
            } elseif ($key === self::TRANSPORT_GROUPS) {
                $this->calculateTransportGroups($sorted);
            } else {
                $divided[$key] = $this->calculatePackages($items);
            }
        }

        return $divided;
    }

    public function calculatePackages($items)
    {
        $items = $this->orderByWeightAndVolume($items);
        $packages = [];
        foreach ($items as $item) {
            $packages = array_merge($packages, $this->createHomoPackage($item));
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
        }
        return $packages;
    }

    public function orderByWeightAndVolume($items)
    {
        $sorter = function ($first, $second) {
            if ($first->weight_trade_unit == $second->weight_trade_unit) {
                if ($first->packing->getVolume() == $second->packing->getVolume()) {
                    return 0;
                }
                return $first->packing->getVolume() > $second->packing->getVolume() ? -1 : 1;
            }
            return $first->weight_trade_unit > $second->weight_trade_unit ? -1 : 1;
        };
        uasort($items, $sorter);
        return $items;
    }

    private function calculateLongPackages($items)
    {

    }

    private function createHomoPackage($item)
    {
        $package = new Package(self::MARGIN);
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
                    $package = new Package(self::MARGIN);
                    $packageList[] = $package;
                }
            }
        } while ($item->quantity > 0);
        return $packageList;
    }

    private function calculateTransportGroups($sorted)
    {
        //todo implement
    }

    private function makeDeepCopyOfPackageArray(array $packages)
    {
        $deepCopyPackages = [];
        foreach ($packages as $package) {
            $deepCopyPackages [] = $package->deepCopy();
        }
        return $deepCopyPackages;
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

}

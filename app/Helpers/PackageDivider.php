<?php

namespace App\Helpers;

use App\Helpers\interfaces\iPackageDivider;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PackageDivider implements iPackageDivider
{
    const TRANSPORT_GROUPS = 'transport_group';
    private const LONG = 'long';
    private const NOT_CALCULABLE = 'not_calculable';
    private $itemList;

    public function setItems(Collection $itemList)
    {
        $this->itemList = $itemList;
    }

    public function divide(): array
    {
        $sorted = $this->groupByPackageType();
        $parcels = $this->divideToParcels($sorted);
        return $this->divideToPalette($parcels);
    }

    private function groupByPackageType(): array
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

    private function divideToParcels($sorted): array
    {
        $divided = [];
        $notCalculated = $sorted[self::NOT_CALCULABLE] ?? [];
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
            'transport_groups' => $transportCalculations['calculated'] ?? [],
            'not_calculated' => array_merge($notCalculated, $failed)];
    }

    private function calculateTransportGroups($sorted): array
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
                $sum['transport_price'] = min($firstPrice, $secondPrice);
            }
            $calculated[] = $sum;
        }
        return ['calculated' => $calculated, 'cantsend' => $cantSend];
    }

    private function sumGroupWeightAndPrice($group): array
    {
        $sums = [];
        foreach ($group as $item) {
            $price = $sums['price'] ?? 0;
            $weight = $sums['weight'] ?? 0;
            $items = $sums['items'] ?? [];
            $items[] = $item;
            $sums = ['price' => $price + $item->quantity * $item->price->net_purchase_price_commercial_unit,
                'weight' => $weight + $item->quantity * $item->weight_trade_unit,
                'factory_group' => $item->tradeGroups,
                'items' => $items,
                'name' => $items[0]->trade_group_name,
                'displayed_group_name' => $items[0]->displayed_group_name ?: $items[0]->trade_group_name];
        }
        return $sums;
    }

    private function checkConditions($sum, $condition): bool
    {
        if (empty($condition)) {
            return false;
        }
        if ($sum > $condition->first_condition) {
            return $condition->first_price ?? false;
        } else if (isset($condition->second_condition) && $sum > $condition->second_condition) {
            return $condition->second_price ?? false;
        } else if (isset($condition->second_condition) && $sum > $condition->third_condition) {
            return $condition->third_price ?? false;
        }
        return false;
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

    private function createHomoPackage($item, $isLong)
    {
        $packageName = sprintf("%s_%s",
            $item->packing->recommended_courier,
            $item->packing->packing_name);
        try {
            $package = new Package($packageName, config('shipping.package_divide_margin'));
        } catch (Exception $exception) {
            return ['packages' => false, 'failed' => $item];
        }
        $package->setIsLong($isLong);
        $packageList = [$package];
        do {
            try {
                $package->addItem($item, 1);
                $item->quantity -= 1;
            } catch (Exception $exception) {
                if ($exception->getMessage() != Package::CAN_NOT_ADD_MORE) {
                    Log::error('Błąd budownaia paczek: ' . $exception->getMessage(), ['class' => $exception->getFile(), 'line' => $exception->getLine()]);
                } else if ($package->getProducts()->count() === 0) {
                    return ['packages' => false, 'failed' => $item];
                } else {
                    $package = new Package($packageName, config('shipping.package_divide_margin'));
                    $packageList[] = $package;
                }
            }
        } while ($item->quantity > 0);
        return ['packages' => $packageList, 'failed' => false];
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
                } catch (Exception $exception) {
                    if ($exception->getMessage() != Package::CAN_NOT_ADD_MORE) {
                        Log::error('Package Building: repack', [$exception->getMessage()]);
                    }
                }
            }
        }
        $packageToSplit->removeEmpty();
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
        $onlyPackages = $packageCollection->filter(function ($package) use ($palette) {
            return $package->getTotalVolume() < Palette::PALETTE_100_VOLUME && $package->getTotalWeight() < Palette::PALETTE_100_WEIGHT;
        })
            ->values();
        $onlyPallets = $packageCollection->filter(function ($package) use ($palette) {
            return $package->getTotalVolume() > Palette::PALETTE_100_VOLUME || $package->getTotalWeight() > Palette::PALETTE_100_WEIGHT;
        })
            ->values();
        while ($onlyPackages->count() > 0) {
            $package = $onlyPackages->get($i);
            if (isset($package)) {
                if ($palette->canPutNewItem($package)) {
                    $palette->addItem($package);
                    $onlyPackages->forget($i);
                    $onlyPackages = $onlyPackages->values();
                } else {
                    $palettes[] = new Palette();
                    $i++;
                }
            } else {
                $i = 0;
            }
        }
        $parcels['packages'] = $onlyPallets->toArray();
        foreach ($palettes as $k => $palette) {
            try {
                $palette->tryFitInSmallerPalette();
            } catch (Exception $exception) {
                Log::info('Problem z pasowaniem palety na mniejszą: ',
                    ['exception' => $exception->getMessage(), ['class' => $exception->getFile(), 'line' => $exception->getLine()]]
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

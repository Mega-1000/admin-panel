<?php

namespace App\Helpers;

use App\Entities\PackageTemplate;
use Exception;

/**
 * @property int $id
 *
 * @property string $delivery_courier_name
 * @property string $service_courier_name
 * @property string $symbol
 *
 */
class Package
{
    const CAN_NOT_ADD_MORE = 'Nie można dodać produktu do koszyka';
    const DPD_COURIER = 'DPD';
    public $productList;
    public $packageName;
    public $displayed_name;
    public $price;
    protected $visible = ['packageName', 'productList'];
    private $maxWeight;
    private $volumeRatio;
    private $volumeMargin;
    private $isLong = false;

    public function __construct($packageName, $margin)
    {
        $packageTemplate = PackageTemplate::where('symbol', strtolower($packageName))->firstOrFail();

        $this->price = $packageTemplate->approx_cost_client;
        $this->volumeRatio = $packageTemplate->volume;
        $this->maxWeight = $packageTemplate->max_weight;
        $this->displayed_name = $packageTemplate->displayed_name ?: $packageName;

        $this->volumeMargin = $margin;
        $this->packageName = $packageName;
        $this->productList = collect([]);
    }

    public function addItem($product, $quantity)
    {
        if ($this->canPutNewItem($product, $quantity)) {
            $this->productList = $this->icreaseAmount($this->productList, $product, $quantity);
        } else {
            throw new Exception(self::CAN_NOT_ADD_MORE);
        }
    }

    public function canPutNewItem($product, $quantity)
    {
        $moreItems = $this->productList->map(function ($item) {
            return clone $item;
        });

        $moreItems = $this->icreaseAmount($moreItems, $product, $quantity);
        $total = $moreItems->reduce(function ($carry, $item) use ($moreItems) {
            $carry['weight'] += $item->weight_trade_unit * $item->quantity;
            $carry['volume'] += $this->calculateVolumeForItem($item, $moreItems);
            return $carry;
        }, ['weight' => 0, 'volume' => 0]);
        return $total['weight'] < $this->maxWeight && $total['volume'] < $this->volumeRatio;
    }

    private function icreaseAmount($list, $product, $quantity)
    {
        if (empty($list->firstWhere('id', $product->id))) {
            $productClone = clone $product;
            $productClone->quantity = 0;
            $list->prepend($productClone);
        }
        $list->firstWhere('id', $product->id)->quantity += $quantity;
        return $list;
    }

    private function calculateVolumeForItem($item, $list)
    {
        $maxLength = false;
        if ($this->isLong) {
            $maxLength = $list->reduce(function ($carry, $item) {
                return max($item->packing->dimension_x, $carry);
            });
        }
        return $item->packing->getVolume($maxLength) * $item->quantity * $this->volumeMargin;
    }

    public function getTotalVolume()
    {
        return $this->productList->reduce(function ($carry, $item) {
            return $carry + $this->calculateVolumeForItem($item, $this->productList);
        });
    }

    public function getTotalWeight()
    {
        return $this->productList->reduce(function ($carry, $item) {
            return $carry + $item->weight_trade_unit * $item->quantity;
        });
    }

    public function getProducts()
    {
        return $this->productList;
    }

    public function deepCopy()
    {
        $copy = clone $this;
        $copy->productList = $copy->productList->map(function ($item) {
            return clone $item;
        });
        return $copy;
    }

    public function removeEmpty()
    {
        $this->productList = $this->productList->reject(function ($item) {
            return $item->quantity === 0;
        });
    }

    public function getIsLong()
    {
        return $this->isLong;
    }

    public function setIsLong($isLong): void
    {
        $this->isLong = $isLong;
    }
}

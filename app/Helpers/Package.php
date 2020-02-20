<?php

namespace App\Helpers;

use App\Entities\Product;
use Illuminate\Database\Eloquent\Collection;

class Package
{
    //todo pobierać dane z bazy
    const MAX_WEIGHT = 70;
    const VOLUME_RATIO = 180000;
    const CAN_NOT_ADD_MORE = 'Nie można dodać prodktu do koszyka';
    private $productList;
    private $volumeMargin;
    private $isLong = false;


    public function __construct($margin)
    {
        $this->volumeMargin = $margin;
        $this->productList = collect([]);
    }

    public function addItem($product, $quantity)
    {
        if ($this->canPutNewItem($product, $quantity)) {
            $this->productList = $this->icreaseAmount($this->productList, $product, $quantity);
        } else {
            throw new \Exception(self::CAN_NOT_ADD_MORE);
        }
    }

    public function canPutNewItem($product, $quantity)
    {
        $moreItems = $this->productList->map(function ($item) {
            return clone $item;
        });

        $moreItems = $this->icreaseAmount($moreItems, $product, $quantity);
        $maxLength = false;
        if ($this->isLong) {
            $maxLength = $moreItems->reduce(function ($carry, $item) {
                return $item->packing->dimension_x > $carry ? $item->packing->dimension_x : $carry;
            });
        }
        $total = $moreItems->reduce(function ($carry, $item) use ($maxLength) {
            $carry['weight'] += $item->weight_trade_unit * $item->quantity;
            $carry['volume'] += $item->packing->getVolume($maxLength) * $item->quantity * $this->volumeMargin;
            return $carry;
        }, ['weight' => 0, 'volume' => 0]);
        return $total['weight'] < self::MAX_WEIGHT && $total['volume'] < self::VOLUME_RATIO;
    }

    public function getProducts()
    {
        return $this->productList;
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

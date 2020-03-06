<?php

namespace App\Helpers;

use App\Entities\Product;
use Illuminate\Database\Eloquent\Collection;

class Package
{
    //todo pobierać dane z bazy
    const MAX_WEIGHT = 70;
    const VOLUME_RATIO = 180000;
    const CAN_NOT_ADD_MORE = 'Nie można dodać produktu do koszyka';
    public $productList;
    public $packageName;
    private $volumeMargin;
    private $isLong = false;
    public $price;
    protected $visible = ['packageName', 'productList'];
    const I_K_PRICE = 17;
    const DPD_D_PRICE = 32;
    const DPD_K_PRICE = 18;


    public function __construct($packageName, $margin)
    {
        switch (strtolower($packageName)) {
            case 'dpd_k':
                $this->price = self::DPD_K_PRICE;
                break;
            case 'dpd_d':
                $this->price = self::DPD_D_PRICE;
                break;
            case 'i_k':
                $this->price = self::I_K_PRICE;
                break;
            default:
                throw new \Exception('Nie istnieje dany kurier');
        }
        $this->volumeMargin = $margin;
        $this->packageName = $packageName;
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

    public function getTotalVolume()
    {
        return $this->productList->reduce(function ($carry, $item){
            return $carry + $this->calculateVolumeForItem($item, $this->productList);
        });
    }
    public function getTotalWeight()
    {
        return $this->productList->reduce(function ($carry, $item){
            return $carry + $item->weight_trade_unit * $item->quantity;
        });
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
        return $total['weight'] < self::MAX_WEIGHT && $total['volume'] < self::VOLUME_RATIO;
    }

    private function calculateVolumeForItem($item, $list)
    {
        $maxLength = false;
        if ($this->isLong) {
            $maxLength = $list->reduce(function ($carry, $item) {
                return $item->packing->dimension_x > $carry ? $item->packing->dimension_x : $carry;
            });
        }
        return $item->packing->getVolume($maxLength) * $item->quantity * $this->volumeMargin;
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

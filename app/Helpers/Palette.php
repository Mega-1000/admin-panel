<?php

namespace App\Helpers;
use App\Entities\PackageTemplate;

class Palette
{
    const CAN_NOT_ADD_MORE = 'nie można dodać nowych paczek';
    const PALETTE_100 = 'PALETA_100';
    const PALETTE_80 = 'PALETA_80';
    const PALETTE_100_VOLUME = 2400000;
    const PALETTE_100_WEIGHT = 1000;
    const PALETTE_80_VOLUME = 1920000;
    const PALETTE_80_WEIGHT = 1000;
    const PALETTE_80_PRICE = 150;
    const PALETTE_100_PRICE = 160;

    public $packagesList;
    public $packagesCost;
    public $price;

    protected $visible = ['type', 'packagesList', 'packagesCost'];

    public $type;
    public $displayed_name;

    public function __construct()
    {
        $this->packagesList = collect([]);
        $this->setType(self::PALETTE_100);
    }

    public function addItem($package)
    {
        if ($this->canPutNewItem($package)) {
            $this->packagesList->push($package);
        } else {
            throw new \Exception(self::CAN_NOT_ADD_MORE);
        }
    }

    public function canPutNewItem(Package $package)
    {
        $currentCarry = $this->getCarry();
        return ($currentCarry['volume'] + $package->getTotalVolume()) < self::PALETTE_100_VOLUME
            && ($currentCarry['weight'] + $package->getTotalWeight()) < self::PALETTE_100_WEIGHT;
    }

    public function tryFitInSmallerPalette()
    {
        $carry = $this->getCarry();
        $canFitInSmaller = $carry['volume'] < self::PALETTE_80_VOLUME && $carry['weight'] < self::PALETTE_80_WEIGHT;
        if ($canFitInSmaller) {
            $this->setType(self::PALETTE_80);
        }
    }

    private function setType($type) {
        switch ($type) {
            case self::PALETTE_80:
                $this->type = self::PALETTE_80;
                $this->price = self::PALETTE_80_PRICE;
                break;
            case self::PALETTE_100:
            default:
                $this->type = self::PALETTE_100;
                $this->price = self::PALETTE_100_PRICE;
                break;
        }
        $packageTemplate = PackageTemplate::where('symbol', strtolower($this->type))->firstOrFail();
        $this->displayed_name = $packageTemplate->displayed_name ?: $this->type;

    }
    private function getCarry()
    {
        return $this->packagesList->reduce(function ($carry, $item) {
            $carry['volume'] += $item->getTotalVolume();
            $carry['weight'] += $item->getTotalWeight();
            return $carry;
        }, ['weight' => 0, 'volume' => 0]);
    }

    public function setPackageCost()
    {
        $this->packagesCost =  $this->packagesList->reduce(function ($carry, Package $next) {
           return $carry + $next->price;
        });
    }
}

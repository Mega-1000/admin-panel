<?php

namespace App\Helpers;
class Palette
{
    const CAN_NOT_ADD_MORE = 'nie można dodać nowych paczek';
    public $packagesList;
    const PALETTE_100 = 'PALETA_100';
    const PALETTE_80 = 'PALETA_80';
    const PALETTE_100_VOLUME = 2400000;
    const PALETTE_100_WEIGHT = 1000;
    const PALETTE_80_VOLUME = 1920000;
    const PALETTE_80_WEIGHT = 1000;
    protected $visible = ['type', 'packagesList'];

    public $type = self::PALETTE_100;

    public function __construct()
    {
        $this->packagesList = collect([]);
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
            $this->type = self::PALETTE_80;
        }
    }

    private function getCarry()
    {
        return $this->packagesList->reduce(function ($carry, $item) {
            $carry['volume'] += $item->getTotalVolume();
            $carry['weight'] += $item->getTotalWeight();
            return $carry;
        }, ['weight' => 0, 'volume' => 0]);
    }
}

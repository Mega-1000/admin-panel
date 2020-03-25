<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\PackageTemplate;
use App\Helpers\interfaces\iDividable;

class SelloPackageDivider implements iDividable
{

    private $packageNumber = 1;
    private $deliveryId;
    private $delivererId;

    public function divide($data, Order $order)
    {
        if (empty($this->delivererId) || empty($this->delivererId)) {
            throw new \Exception('Brak powiązanego szablonu z sello');
        }
        $template = PackageTemplate::
        where('sello_delivery_id', $this->deliveryId)
            ->where('sello_deliverer_id', $this->delivererId)
            ->firstOrFail();
        $modulo = $data[0]['amount'] % $this->packageNumber;

        for ($packageNumber = 1; $packageNumber <= $this->packageNumber; $packageNumber++) {
            $pack = BackPackPackageDivider::createPackage($template, $order->id, $packageNumber);
            $quantity = floor($data[0]['amount'] / $this->packageNumber);
            if ($packageNumber <= $modulo) {
                $quantity += 1;
            }
            $pack->packedProducts()->attach($data[0]['id'],
                ['quantity' => $quantity]);
        }
        return false;
    }

    public function setDeliveryId($deliveryId): void
    {
        $this->deliveryId = $deliveryId;
    }

    public function setDelivererId($delivererId): void
    {
        $this->delivererId = $delivererId;
    }

    public function setPackageNumber(int $packageNumber): void
    {
        $this->packageNumber = $packageNumber;
    }
}

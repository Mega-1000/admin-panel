<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\PackageTemplate;
use App\Helpers\interfaces\iDividable;

class SelloPackageDivider implements iDividable
{

    private $maxInPackage = 1;
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
        $modulo = $data[0]['amount'] % $this->maxInPackage;
        $total = round($data[0]['amount'] / $this->maxInPackage);

        for ($packageNumber = 1; $packageNumber <= $total; $packageNumber++) {
            $pack = BackPackPackageDivider::createPackage($template, $order->id, $packageNumber);
            $quantity = floor($data[0]['amount'] / $this->maxInPackage);
            if ($packageNumber <= $modulo) {
                $quantity += 1;
            }
            $pack->packedProducts()->attach($data[0]['id'],
                ['quantity' => $quantity]);
        }
        $order->shipment_date = $pack->shipment_date;
        $order->save();
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

    public function setMaxInPackage(int $maxInPackage): void
    {
        $this->maxInPackage = $maxInPackage > 0 ? $maxInPackage : 1;
    }
}

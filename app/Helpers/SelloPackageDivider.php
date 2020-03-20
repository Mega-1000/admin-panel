<?php

namespace App\Helpers;

use App\Entities\PackageTemplate;

class SelloPackageDivider
{

    private $packageNumber = 1;
    private $deliveryId;
    private $delivererId;

    public function divide($data, $order)
    {
        if (empty($this->delivererId) || empty($this->delivererId)) {
            throw new \Exception('Brak powiązanego szablonu z sello');
        }
        $template = PackageTemplate::
            where('sello_delivery_id', $this->deliveryId)
            ->where('sello_deliverer_id', $this->delivererId)
            ->firstOrFail();
        for ($i = 0; $i < $this->packageNumber; $i++) {
            BackPackPackageDivider::createPackage($template, $order->id);
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
}

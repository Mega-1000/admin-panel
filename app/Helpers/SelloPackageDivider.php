<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\PackageTemplate;
use App\Helpers\interfaces\iDividable;

class SelloPackageDivider implements iDividable
{

    private $transactionList;

    public function divide($data, Order $order)
    {
        $this->transactionList->map(function ($transaction) use ($order, $data) {
            $transaction->maxInPackage = $this->setMaxInPackage($transaction->tw_Pole2 ?? 1);
            $this->divideForTransaction($data, $order, $transaction);
        });
        return false;
    }

    protected function setMaxInPackage(int $maxInPackage)
    {
        return $maxInPackage > 0 ? $maxInPackage : 1;
    }

    /**
     * @param $data
     * @param Order $order
     * @throws \Exception
     */
    private function divideForTransaction($data, Order $order, $transaction): void
    {
        if (empty($transaction->tr_DelivererId) || empty($transaction->tr_DeliveryId)) {
            throw new \Exception('Brak powiązanego szablonu z sello id: ' . $transaction->id);
        }
        $template = PackageTemplate::
        where('sello_delivery_id', $transaction->tr_DeliveryId)
            ->where('sello_deliverer_id', $transaction->tr_DeliveryId)
            ->firstOrFail();
        $modulo = $data['amount'] % $transaction->maxInPackage;
        $total = ceil($data['amount'] / $transaction->maxInPackage);

        for ($packageNumber = 1; $packageNumber <= $total; $packageNumber++) {
            $pack = BackPackPackageDivider::createPackage($template, $order->id, $packageNumber);
            $quantity = floor($data['amount'] / $transaction->maxInPackage);
            if ($packageNumber <= $modulo) {
                $quantity += 1;
            }
            $pack->packedProducts()->attach($data['id'],
                ['quantity' => $quantity]);
        }
        $order->shipment_date = $pack->shipment_date;
        $order->save();
    }

    public function setTransactionList($group)
    {
        $this->transactionList = $group;
    }
}

<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\PackageTemplate;
use App\Entities\ProductPacking;
use App\Helpers\interfaces\iDividable;
use Illuminate\Support\Facades\Log;

class SelloPackageDivider implements iDividable
{

    const TEMPLATE_PACZKOMAT_A = 53;
    const TEMPLATE_PACZKOMAT_B = 54;
    const TEMPLATE_PACZKOMAT_C = 55;
    const TEMPLATE_IDS_FOR_PACZKOMAT = [self::TEMPLATE_PACZKOMAT_A, self::TEMPLATE_PACZKOMAT_B, self::TEMPLATE_PACZKOMAT_C];
    private $transactionList;

    public function divide($data, Order $order)
    {
        $realPackageNumber = 1;
        foreach ($this->transactionList as $transaction) {
            if ($transaction->tr_Group) {
                continue;
            }
            $this->divideForTransaction($data, $order, $transaction, $realPackageNumber);
        }
        return false;
    }

    /**
     * @param $items
     * @param Order $order
     * @param $transaction
     * @param $realPackageNumber
     */
    private function divideForTransaction($items, Order $order, $transaction, &$realPackageNumber)
    {
        $data = $this->findProductInData($items, $transaction);
        $packing = ProductPacking::where('product_id', $data['id'])->first();
        $template = $this->prepareTemplate($transaction);
        $isPaczkomat = in_array($template->id, self::TEMPLATE_IDS_FOR_PACZKOMAT);
        $amountLeft = $data['amount'];
        while ($amountLeft > 0) {
            if ($isPaczkomat) {
                list($quantity, $newTemplate) = $this->getPaczkomatQuantity($amountLeft, $packing);
                if ($newTemplate) {
                    $template = $newTemplate;
                }
            } else {
                $quantity = $this->getQuantity($packing->allegro_courier, $amountLeft);
            }
            $pack = BackPackPackageDivider::createPackage($template, $order->id, $realPackageNumber);
            $pack->packedProducts()->attach($data['id'],
                ['quantity' => $quantity]);
            $amountLeft -= $quantity;
            $realPackageNumber++;
            $shipment_date = $pack->shipment_date;
        }

        $order->shipment_date = $shipment_date;
        $order->save();
    }

    /**
     * @param $items
     * @param $transaction
     */
    private function findProductInData($items, $transaction)
    {
        $data = false;
        foreach ($items as $product) {
            if ($data) {
                continue;
            }
            if ($product['transactionId'] == $transaction->id) {
                $data = $product;
            }
        }
        return $data;
    }

    /**
     * @param $transaction
     * @return mixed
     * @throws \Exception
     */
    private function prepareTemplate($transaction)
    {
        if (empty($transaction->tr_DelivererId) || empty($transaction->tr_DeliveryId)) {
            throw new \Exception('Brak powiązanego szablonu z sello id: ' . $transaction->id);
        }
        try {
            $template = PackageTemplate::where('sello_delivery_id', $transaction->tr_DeliveryId)
                ->where('sello_deliverer_id', $transaction->tr_DelivererId)
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception('Import Sello: Nie znaleziono szablonu sello id:'
                . $transaction->id . ' deliverer id:' . $transaction->tr_DelivererId . ' delivery id:' . $transaction->tr_DeliveryId);
        }
        return $template;
    }

    /**
     * @param int $amountLeft
     * @param $packing
     * @return array
     */
    private function getPaczkomatQuantity(int $amountLeft, $packing): array
    {
        if ($amountLeft >= $packing->paczkomat_size_c) {
            $quantity = $this->getQuantity($packing->paczkomat_size_c, $amountLeft);
            $selectedTemplateId = self::TEMPLATE_PACZKOMAT_C;
            $templateName = 'Paczkomat Allegro C';
        } elseif ($amountLeft >= $packing->paczkomat_size_b) {
            $quantity = $this->getQuantity($packing->paczkomat_size_b, $amountLeft);
            $selectedTemplateId = self::TEMPLATE_PACZKOMAT_B;
            $templateName = 'Paczkomat Allegro B';
        } else {
            $quantity = $this->getQuantity($packing->paczkomat_size_a, $amountLeft);
            $selectedTemplateId = self::TEMPLATE_PACZKOMAT_A;
            $templateName = 'Paczkomat Allegro A';
        }
        $template = PackageTemplate::find($selectedTemplateId);
        if (!$template) {
            Log::error("Brak szablonu paczki lub błędny numer id: $templateName : o ID $selectedTemplateId");
        }
        return array($quantity, $template);
    }

    /**
     * @param $max
     * @param int $amountLeft
     * @return int
     */
    private function getQuantity($max, int $amountLeft): int
    {
        if (empty($max)) {
            return 1;
        }
        return $max < $amountLeft ? $max : $amountLeft;
    }

    public function setTransactionList($group)
    {
        $this->transactionList = $group;
    }

}

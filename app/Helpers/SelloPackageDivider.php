<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\PackageTemplate;
use App\Entities\ProductPacking;
use App\Helpers\interfaces\iDividable;
use Exception;
use Illuminate\Support\Facades\Log;

class SelloPackageDivider implements iDividable
{

    const TEMPLATE_PACZKOMAT_A = 53;
    const TEMPLATE_PACZKOMAT_B = 54;
    const TEMPLATE_PACZKOMAT_C = 55;
    const TEMPLATE_IDS_FOR_PACZKOMAT = [self::TEMPLATE_PACZKOMAT_A, self::TEMPLATE_PACZKOMAT_B, self::TEMPLATE_PACZKOMAT_C];
    private $transactionList;

    /**
     * @throws Exception
     */
    public function divide($data, Order $order): false
    {
        $this->divideForTransaction($data, $order, $this->transactionList[0]);

        return false;
    }

    /**
     * @param $items
     * @param Order $order
     * @param $transaction
     * @throws Exception
     */
    private function divideForTransaction($items, Order $order, $transaction)
    {
        $data = $this->findProductInData($items, $transaction);
        $template = $this->prepareTemplate($transaction);
        for($i = 0; $i < $transaction->tr_CheckoutFormCalculatedNumberOfPackages; $i++) {
            $pack = BackPackPackageDivider::createPackage($template, $order->id, $i+1);
            $pack->packedProducts()->attach($data['id'],
                ['quantity' => 1]);
            $shipment_date = $pack->shipment_date;
        }
        $order->shipment_date = $shipment_date;
        $order->save();
    }

    /**
     * @param $items
     * @param $transaction
     * @return false
     */
    private function findProductInData($items, $transaction): false
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
     * @throws Exception
     */
    private function prepareTemplate($transaction): mixed
    {
        if (empty($transaction->tr_DelivererId) || empty($transaction->tr_DeliveryId)) {
            throw new Exception('Brak powiązanego szablonu z sello id: ' . $transaction->id);
        }

        try {
            $template = PackageTemplate::where('sello_delivery_id', $transaction->tr_DeliveryId)
                ->where('sello_deliverer_id', $transaction->tr_DelivererId)
                ->firstOrFail();
        } catch (Exception $e) {
            throw new Exception('Import Sello: Nie znaleziono szablonu sello id:'
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
        if ($amountLeft <= $packing->paczkomat_size_a) {
            $quantity = min($packing->paczkomat_size_a ?: 1, $amountLeft);
            $selectedTemplateId = self::TEMPLATE_PACZKOMAT_A;
            $templateName = 'Paczkomat Allegro A';
        } elseif ($amountLeft <= $packing->paczkomat_size_b) {
            $quantity = min($packing->paczkomat_size_b ?: 1, $amountLeft);
            $selectedTemplateId = self::TEMPLATE_PACZKOMAT_B;
            $templateName = 'Paczkomat Allegro B';
        } else {
            $quantity = min($packing->paczkomat_size_c ?: 1, $amountLeft);
            $selectedTemplateId = self::TEMPLATE_PACZKOMAT_C;
            $templateName = 'Paczkomat Allegro C';
        }

        $template = PackageTemplate::find($selectedTemplateId);
        if (!$template) {
            Log::error("Brak szablonu paczki lub błędny numer id: $templateName : o ID $selectedTemplateId");
        }

        return array($quantity, $template);
    }

    public function setTransactionList($group)
    {
        $this->transactionList = $group;
    }
}

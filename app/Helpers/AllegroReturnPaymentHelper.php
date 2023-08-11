<?php

namespace App\Helpers;

use App\DTO\AllegroPayment\AllegroReturnItemDTO;
use App\Enums\AllegroReturnItemTypeEnum;

final class AllegroReturnPaymentHelper
{
    public static function createLineItemsFromReturnsByAllegroId(array $returnsByAllegroId): array
    {
        $lineItemsForPaymentRefund = [];
        $lineItemsForCommissionRefund = [];

        foreach ($returnsByAllegroId as $allegroId => $itemReturn) {
            $quantityUndamaged = (int)$itemReturn['quantityUndamaged'];
            $undamagedDeductionCheck = isset($itemReturn['undamagedDeductionCheck']) && $itemReturn['undamagedDeductionCheck'] === "on";
            $undamagedDeduction = $undamagedDeductionCheck ? (float)$itemReturn['undamagedDeduction'] : 0.0;
            $quantityDamaged = (int)$itemReturn['quantityDamaged'];
            $quantityTotal = $quantityUndamaged + $quantityDamaged;
            $returnDamaged = isset($itemReturn['returnDamagedCheck']) && $itemReturn['returnDamagedCheck'] === "on";
            $damagedDeduction = $returnDamaged ? (float)$itemReturn['damagedDeduction'] : 0.0;
            $totalDeduction = $undamagedDeduction + $damagedDeduction;
            $price = (float)$itemReturn['price'];

            if ($quantityTotal === 0) {
                continue;
            }

            if ($returnDamaged) {
                if ($totalDeduction > 0) {
                    $amount = $quantityTotal * $price - $totalDeduction;
                    $lineItemsForPaymentRefund[] = new AllegroReturnItemDTO(
                        id: $allegroId,
                        type: AllegroReturnItemTypeEnum::AMOUNT(),
                        amount: $amount,
                    );
                } else {
                    $lineItemsForPaymentRefund[] = new AllegroReturnItemDTO(
                        id: $allegroId,
                        type: AllegroReturnItemTypeEnum::QUANTITY(),
                        quantity: $quantityTotal,
                    );
                }
            } else {
                if ($undamagedDeduction > 0) {
                    $amount = $quantityUndamaged * $price - $undamagedDeduction;
                    $lineItemsForPaymentRefund[] = new AllegroReturnItemDTO(
                        id: $allegroId,
                        type: AllegroReturnItemTypeEnum::AMOUNT(),
                        amount: $amount,
                    );
                } else {
                    $lineItemsForPaymentRefund[] = new AllegroReturnItemDTO(
                        id: $allegroId,
                        type: AllegroReturnItemTypeEnum::QUANTITY(),
                        quantity: $quantityUndamaged,
                    );
                }
            }

            $lineItemsForCommissionRefund[] = [
                'id' => $allegroId,
                'quantity' => $quantityTotal
            ];
        }

        return [$lineItemsForPaymentRefund, $lineItemsForCommissionRefund];
    }

    public static function createReturnsByAllegroId(array $allegroOrder, array $returns)
    {
        $symbolToAllegroIdPairings = AllegroOrderHelper::createSymbolToAllegroIdPairingsFromLineItems($allegroOrder['lineItems']);

        $returnsByAllegroId = [];

        foreach ($returns as $symbol => $itemReturn) {
            $symbol = explode("-", $symbol)[0];
            $returnsByAllegroId[$symbolToAllegroIdPairings[$symbol]] = $itemReturn;
        }

        return $returnsByAllegroId;
    }
}

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
            $quantityDamaged = (int)$itemReturn['quantityDamaged'];
            $quantityTotal = $quantityUndamaged + $quantityDamaged;

            if ($quantityTotal === 0) {
                continue;
            }
            
            if ($quantityUndamaged > 0) {
                if (array_key_exists('deductionCheck', $itemReturn) && strtolower($itemReturn['deductionCheck']) === "on") {
                    $amount = $quantityUndamaged * (float)$itemReturn['price'] - (float)$itemReturn['deduction'];
                    $lineItemsForPaymentRefund[] = new AllegroReturnItemDTO(
                        id: $allegroId,
                        type: AllegroReturnItemTypeEnum::AMOUNT(),
                        amount: $amount,
                    );
                }
                
                $lineItemsForPaymentRefund[] = new AllegroReturnItemDTO(
                    id: $allegroId,
                    type: AllegroReturnItemTypeEnum::QUANTITY(),
                    quantity: $quantityUndamaged,
                );
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

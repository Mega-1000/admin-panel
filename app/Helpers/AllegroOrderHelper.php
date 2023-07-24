<?php

namespace App\Helpers;

final class AllegroOrderHelper
{
    public static function createSymbolToAllegroIdPairingsFromLineItems(array $lineItems): array {
        $symbolToAllegroIdPairings = [];

        foreach ($lineItems as $lineItem) {
            $symbol = explode("-", $lineItem['offer']['external']['id'])[0];
            $symbolToAllegroIdPairings[$symbol] = $lineItem['id'];
        }

        return $symbolToAllegroIdPairings;
    }
}

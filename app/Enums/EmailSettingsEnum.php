<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class EmailSettingsEnum extends Enum
{
    const NEW = 'Stworzenie oferty w systemie';
    const PRODUCED = 'Zmiana statusu na wyprodukowano';
    const PICKED_UP = 'Zmiana statusu na towar odebrano przez spedycję';
    const PROVIDED = 'Zmiana statusu LP na dostarczono';
    const ADDRESS_CHANGED = 'Zmiana adresu dostawy, faktury';
    const PICKED_UP_2 = 'Towar wydano + 1 dzień roboczy';
    const NEW_ALLEGRO_MSG = 'Nowa wiadomość Allegro';

    const LABEL_PRODUCED = 50;
    const LABEL_PICKED_UP = 107;
    const LABEL_PROVIDED = 203;

    public static function getAllStatuses(): array {

        $allStatuses = array_filter(self::getConstants(), function($key) {
            return strpos($key, 'LABEL_') === false;
        }, ARRAY_FILTER_USE_KEY);
        
        return $allStatuses;
    }
}

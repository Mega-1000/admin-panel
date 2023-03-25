<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class EmailSettingsEnum extends Enum
{
    const STATUS_NEW = 'stworzenie oferty w systemie';
    const STATUS_PRODUCED = 'zmiana statusu na wyprodukowano';
    const STATUS_PICKED_UP = 'zmiana statusu na towar odebrano przez spedycję';
    const STATUS_PROVIDED = 'zmiana statusu LP na dostarczono';

    const STATUS_LABELS = [
        'PRODUCED' => 50,
        'PICKED_UP' => 107,
        'PROVIDED' => 203,
    ];
}

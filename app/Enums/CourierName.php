<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CourierName extends Enum
{
    const INPOST = 'INPOST';
    const ALLEGRO_INPOST = 'ALLEGRO-INPOST';
    const DPD = 'DPD';
    const APACZKA = 'APACZKA';
    const POCZTEX = 'POCZTEX';
    const JAS = 'JAS';
    const GLS = 'GLS';

}

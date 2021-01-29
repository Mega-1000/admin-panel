<?php
declare(strict_types=1);

namespace App\Enums\AllegroExcel;

use BenSampo\Enum\Enum;

final class SheetNames extends Enum
{
    const ORDER_DATA = 'order-data';
    const ALLEGRO_PAYMENTS = 'allegro-payments';
    const CLIENT_PAYMENTS = 'client-payments';
}

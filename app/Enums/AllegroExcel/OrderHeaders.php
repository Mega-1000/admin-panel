<?php
declare(strict_types=1);

namespace App\Enums\AllegroExcel;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class OrderHeaders extends Enum implements LocalizedEnum
{
    const ORDER_ID = 'order_id';
    const PACKAGE_LETTER_NUMBER = 'package_letter_number';
    const ALLEGRO_ORDER_ID = 'allegro_order_id';
    const ORDER_SUM = 'order_sum';
    const CLIENT_PACKAGE_COST = 'client_package_cost';
    const FIRM_PACKAGE_COST = 'firm_package_cost';
}

<?php
declare(strict_types=1);

namespace App\Enums\AllegroExcel;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class AllegroHeaders extends Enum implements LocalizedEnum
{
    const ORDER_ID = 'order_id';
    const ALLEGRO_ORDER_ID = 'allegro_order_id';
    const ALLEGRO_PAYMENT_ID = 'allegro_payment_id';
    const PROMISE_PAYMENTS_SUM = 'promise_payments_sum';
    const REFUND_ID = 'refund_id';
    const REFUNDED = 'refunded';
}

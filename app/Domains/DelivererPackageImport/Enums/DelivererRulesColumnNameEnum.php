<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class DelivererRulesColumnNameEnum extends Enum implements LocalizedEnum
{
    public const ORDER_ALLEGRO_ADDITIONAL_SERVICE = 'allegro_additional_service';

    public const ORDER_ALLEGRO_DEPOSIT_VALUE = 'allegro_deposit_value';

    public const ORDER_ALLEGRO_FORM_ID = 'allegro_form_id';

    public const ORDER_ALLEGRO_OPERATION_DATE = 'allegro_operation_date';

    public const ORDER_ALLEGRO_COMMISSION = 'allegro_commission';

    public const ORDER_PAYMENT_CHANNEL = 'payment_channel';

    public const ORDER_REFUND_ID = 'refund_id';

    public const ORDER_PACKAGES_LETTER_NUMBER = 'letter_number';

    public const ORDER_PACKAGES_SERVICE_COURIER_NAME = 'service_courier_name';

    public const ORDER_PACKAGES_REAL_COST_FOR_COMPANY_COST = 'real_cost_for_company';

    public const SEL_TR_TRANSACTION_SELLO_PAYMENT = 'tr_CheckoutFormPaymentId';

    public const SEL_TR_TRANSACTION_SELLO_FORM = 'tr_CheckoutFormId';
}

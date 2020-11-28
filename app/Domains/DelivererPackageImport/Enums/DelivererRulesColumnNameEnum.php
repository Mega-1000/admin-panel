<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Enums;

use BenSampo\Enum\Enum;

final class DelivererRulesColumnNameEnum extends Enum
{
    public const SELLO_ID = 'sello_id';

    public const ALLEGRO_FORM_ID = 'allegro_form_id';

    public const ALLEGRO_DEPOSIT_VALUE = 'allegro_deposit_value';

    public const ALLEGRO_OPERATION_DATE = 'allegro_operation_date';

    public const ALLEGRO_ADDITIONAL_SERVICE = 'allegro_additional_service';
}

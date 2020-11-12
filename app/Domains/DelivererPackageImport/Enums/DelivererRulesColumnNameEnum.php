<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Enums;

use BenSampo\Enum\Enum;

final class DelivererRulesColumnNameEnum extends Enum
{
    public const ALLEGRO_TRANSACTION_ID = 'allegro_transaction_id';

    public const ALLEGRO_FORM_ID = 'allegro_form_id';

    public const WAREHOUSE_NOTICE = 'warehouse_notice';

}

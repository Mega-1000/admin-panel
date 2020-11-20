<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Enums;

use BenSampo\Enum\Enum;

final class DelivererRulesColumnNameEnum extends Enum
{
    public const ALLEGRO_TRANSACTION_ID = 'allegro_transaction_id';

    public const SELLO_ID = 'sello_id';

    public const CUSTOMER_NOTICES = 'customer_notices';

    public const RATING_MESSAGE = 'rating_message';

    public const FINANCIAL_COMMENT = 'financial_comment';
}

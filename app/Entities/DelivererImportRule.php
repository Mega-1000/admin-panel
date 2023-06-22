<?php

declare(strict_types=1);

namespace App\Entities;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DelivererImportRule extends Model
{
    private const ALLOWED_COLUMN_ACTIONS = [
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID => [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ],
        DelivererRulesColumnNameEnum::ORDER_PAYMENT_CHANNEL => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::ORDER_REFUND_ID => [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_DEPOSIT_VALUE => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_COMMISSION => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY_COST => [
            DelivererRulesActionEnum::SET,
            DelivererRulesActionEnum::GET,
            DelivererRulesActionEnum::GET_AND_REPLACE,
            DelivererRulesActionEnum::GET_WITH_CONDITION,
        ],
        DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_PAYMENT => [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ],
        DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_FORM => [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ],
    ];

    protected $fillable = [
        'deliverer_id',
        'action',
        'db_column_name',
        'import_column_number',
        'value',
        'change_to',
        'order',
        'refund_id',
        'condition_column_number',
        'condition_value',
    ];

    /**
     * @throws InvalidEnumMemberException
     */
    public function getAction(): DelivererRulesActionEnum
    {
        return new DelivererRulesActionEnum($this->action);
    }

    /**
     * @throws InvalidEnumMemberException
     */
    public function getColumnName(): DelivererRulesColumnNameEnum
    {
        return new DelivererRulesColumnNameEnum($this->db_column_name);
    }

    public function deliverer(): HasOne
    {
        return $this->hasOne(Deliverer::class, 'id', 'deliverer_id');
    }

    public static function canActionBePerformedOnColumn(
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererRulesActionEnum $actionEnum
    ): bool {
        return in_array(
            $actionEnum->value,
            self::ALLOWED_COLUMN_ACTIONS[$columnNameEnum->value]
        );
    }
}

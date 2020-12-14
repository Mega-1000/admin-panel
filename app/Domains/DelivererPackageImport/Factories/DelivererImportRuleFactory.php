<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRuleItem;
use App\Entities\DelivererImportRule;

class DelivererImportRuleFactory
{
    private const ALLOWED_COLUMN_ACTIONS = [
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ],
        DelivererRulesColumnNameEnum::ORDER_SELLO_ID => [],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID => [],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_DEPOSIT_VALUE => [],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE => [],
        DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => [],
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME => [],
        DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY => [],
    ];

    private $delivererImportRuleColumnRepositoryFactory;

    public function __construct(
        DelivererImportRuleColumnRepositoryFactory $delivererImportRuleColumnRepositoryFactory
    ) {
        $this->delivererImportRuleColumnRepositoryFactory = $delivererImportRuleColumnRepositoryFactory;
    }

    public function create(DelivererImportRule $delivererImportRuleEntity): DelivererImportRuleItem {
        if (!$this->canActionBePerformedOnColumn(
            $delivererImportRuleEntity->getColumnName(),
            $delivererImportRuleEntity->getAction()
        )) {
            throw new \Exception(
                sprintf(
                    'Action %s is not performed for column %s',
                    $delivererImportRuleEntity->getAction()->value,
                    $delivererImportRuleEntity->getColumnName()->value
                )
            );
        }

        return new DelivererImportRuleItem(
            $delivererImportRuleEntity,

        );
    }

    private function canActionBePerformedOnColumn(
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererRulesActionEnum $actionEnum
    ): bool {
        return in_array(
            $actionEnum->value,
            self::ALLOWED_COLUMN_ACTIONS[$columnNameEnum->value]
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesColumnNumberVO;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesValueVO;
use App\Entities\Deliverer;
use App\Entities\DelivererImportRule;

class DelivererImportRuleFromRequestFactory
{
    public function create(Deliverer $deliverer, array $rule): DelivererImportRule
    {
        if (empty($rule)) {
            throw new \Exception('Empty import rule');
        }

        if (empty($rule['action'])) {
            throw new \Exception('No action for rule');
        }

        switch ($rule['action']) {
            case DelivererRulesActionEnum::SEARCH_COMPARE:
                return DelivererImportRuleEntityFactory::createSearch(
                    $deliverer,
                    new DelivererRulesActionEnum($rule['action']),
                    new DelivererRulesColumnNameEnum($rule['columnName']),
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber'])
                );
            case DelivererRulesActionEnum::SEARCH_REGEX:
                return DelivererImportRuleEntityFactory::createSearchRegex(
                    $deliverer,
                    new DelivererRulesActionEnum($rule['action']),
                    new DelivererRulesColumnNameEnum($rule['columnName']),
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber']),
                    new DelivererImportRulesValueVO($rule['value'])
                );
            case DelivererRulesActionEnum::SET:
                return DelivererImportRuleEntityFactory::createSet(
                    $deliverer,
                    new DelivererRulesActionEnum($rule['action']),
                    new DelivererRulesColumnNameEnum($rule['columnName']),
                    new DelivererImportRulesValueVO($rule['value'])
                );
            case DelivererRulesActionEnum::GET:
                return DelivererImportRuleEntityFactory::createGet(
                    $deliverer,
                    new DelivererRulesActionEnum($rule['action']),
                    new DelivererRulesColumnNameEnum($rule['columnName']),
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber'])
                );
            case DelivererRulesActionEnum::GET_AND_REPLACE:
                return DelivererImportRuleEntityFactory::createGetAndReplace(
                    $deliverer,
                    new DelivererRulesActionEnum($rule['action']),
                    new DelivererRulesColumnNameEnum($rule['columnName']),
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber']),
                    new DelivererImportRulesValueVO($rule['value']),
                    new DelivererImportRulesValueVO($rule['changeTo'])
                );
            default:
                throw new \Exception('Wrong action name for deliverer import rule: ' . $rule['action']);
        }
    }
}

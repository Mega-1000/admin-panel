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
    private $actionEnum;

    private $columnNameEnum;

    public function create(Deliverer $deliverer, array $rule): DelivererImportRule
    {
        $this->validate($rule);

        switch ($rule['action']) {
            case DelivererRulesActionEnum::SEARCH_COMPARE:
                return DelivererImportRuleEntityFactory::createSearch(
                    $deliverer,
                    $this->actionEnum,
                    $this->columnNameEnum,
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber'])
                );
            case DelivererRulesActionEnum::SEARCH_REGEX:
                return DelivererImportRuleEntityFactory::createSearchRegex(
                    $deliverer,
                    $this->actionEnum,
                    $this->columnNameEnum,
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber']),
                    new DelivererImportRulesValueVO($rule['value'])
                );
            case DelivererRulesActionEnum::SET:
                return DelivererImportRuleEntityFactory::createSet(
                    $deliverer,
                    $this->actionEnum,
                    $this->columnNameEnum,
                    new DelivererImportRulesValueVO($rule['value'])
                );
            case DelivererRulesActionEnum::GET:
                return DelivererImportRuleEntityFactory::createGet(
                    $deliverer,
                    $this->actionEnum,
                    $this->columnNameEnum,
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber'])
                );
            case DelivererRulesActionEnum::GET_AND_REPLACE:
                return DelivererImportRuleEntityFactory::createGetAndReplace(
                    $deliverer,
                    $this->actionEnum,
                    $this->columnNameEnum,
                    new DelivererImportRulesColumnNumberVO((int) $rule['columnNumber']),
                    new DelivererImportRulesValueVO($rule['value']),
                    new DelivererImportRulesValueVO($rule['changeTo'])
                );
            default:
                throw new \Exception('Wrong action name for deliverer import rule: ' . $rule['action']);
        }
    }

    private function validate(array $rule): void
    {
        if (empty($rule)) {
            throw new \Exception('Empty import rule');
        }

        if (empty($rule['action'])) {
            throw new \Exception('Empty action of import rule');
        }

        if (empty($rule['columnName'])) {
            throw new \Exception('Empty column name of import rule');
        }

        $this->actionEnum = new DelivererRulesActionEnum($rule['action']);
        $this->columnNameEnum = new DelivererRulesColumnNameEnum($rule['columnName']);

        if (!DelivererImportRule::canActionBePerformedOnColumn(
            $this->columnNameEnum,
            $this->actionEnum
        )) {
            throw new \Exception(
                sprintf(
                    'Action %s is not performed for column %s',
                    $this->actionEnum->value,
                    $this->columnNameEnum->value
                )
            );
        }
    }
}

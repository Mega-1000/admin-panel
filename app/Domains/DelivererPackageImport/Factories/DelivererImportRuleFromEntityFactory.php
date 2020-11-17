<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRuleGet;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRuleGetAndReplace;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRuleSearchCompare;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRuleSearchRegex;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRuleSet;
use App\Entities\DelivererImportRule;

class DelivererImportRuleFromEntityFactory
{
    public function create(DelivererImportRule $rule)
    {
        switch ($rule->action) {
            case DelivererRulesActionEnum::SEARCH_COMPARE:
                return new DelivererImportRuleSearchCompare($rule);
            case DelivererRulesActionEnum::SEARCH_REGEX:
                return new DelivererImportRuleSearchRegex($rule);
            case DelivererRulesActionEnum::SET:
                return new DelivererImportRuleSet($rule);
            case DelivererRulesActionEnum::GET:
                return new DelivererImportRuleGet($rule);
            case DelivererRulesActionEnum::GET_AND_REPLACE:
                return new DelivererImportRuleGetAndReplace($rule);
            default:
                throw new \Exception('Wrong entity action name for deliverer import rule: ' . $rule->action);
        }
    }
}

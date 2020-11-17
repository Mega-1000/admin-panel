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
use App\Repositories\OrderRepositoryEloquent;

class DelivererImportRuleFromEntityFactory
{
    private $orderRepository;

    public function __construct(OrderRepositoryEloquent $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function create(DelivererImportRule $rule)
    {
        switch ($rule->action) {
            case DelivererRulesActionEnum::SEARCH_COMPARE:
                return new DelivererImportRuleSearchCompare(
                    $this->orderRepository,
                    $rule
                );
            case DelivererRulesActionEnum::SEARCH_REGEX:
                return new DelivererImportRuleSearchRegex(
                    $this->orderRepository,
                    $rule
                );
            case DelivererRulesActionEnum::SET:
                return new DelivererImportRuleSet(
                    $this->orderRepository,
                    $rule
                );
            case DelivererRulesActionEnum::GET:
                return new DelivererImportRuleGet(
                    $this->orderRepository,
                    $rule
                );
            case DelivererRulesActionEnum::GET_AND_REPLACE:
                return new DelivererImportRuleGetAndReplace(
                    $this->orderRepository,
                    $rule
                );
            default:
                throw new \Exception('Wrong entity action name for deliverer import rule: ' . $rule->action);
        }
    }
}

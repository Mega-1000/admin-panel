<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Factories\DelivererImportRuleFromEntityFactory;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;
use App\Entities\Order;
use Illuminate\Support\Collection;

class DelivererImportRulesManager
{
    private $delivererImportRulesRepository;

    private $delivererImportRuleFromEntityFactory;

    private $deliverer;

    /* @var $importRules Collection */
    private $importRules;

    /* @var $searchRules Collection */
    private $searchRules;

    private $setRules;

    private $getRules;

    /* @var $getAndReplaceRules Collection */
    private $getAndReplaceRules;

    private $getWithConditionRules;

    public function __construct(
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepositoryEloquent,
        DelivererImportRuleFromEntityFactory $delivererImportRuleFromEntityFactory,
        Deliverer $deliverer
    ) {
        $this->delivererImportRulesRepository = $delivererImportRuleRepositoryEloquent;
        $this->delivererImportRuleFromEntityFactory = $delivererImportRuleFromEntityFactory;
        $this->deliverer = $deliverer;

        $this->prepareRules();
    }

    public function runRules(array $line): void
    {
        if (empty($this->importRules)) {
            throw new \Exception('No import rules for the ' . $this->deliverer->name . ' deliverer');
        }

        $order = $this->findOrderByRules($line, clone $this->searchRules);

        $this->runSetRules($order, $line);
        $this->runGetRules($order, $line);
        $this->runGetAndReplaceRules($order, $line);
        $this->runGetWithConditionRules($order, $line);
    }

    private function prepareRules(): bool
    {
        $rulesEntities = $this->delivererImportRulesRepository->getDelivererImportRules(
            $this->deliverer
        );

        if (empty($rulesEntities)) {
            return false;
        }

        $this->importRules = $rulesEntities->map(function ($importRuleEntity) {
            return $this->delivererImportRuleFromEntityFactory->create($importRuleEntity);
        });

        $this->setSearchRules();
        $this->setSetRules();
        $this->setGetRules();
        $this->setGetAndReplaceRules();
        $this->setGetWithConditionRules();

        return true;
    }

    private function runGetAndReplaceRules(Order $order, $line): void
    {
        if ($this->getAndReplaceRules->isNotEmpty()) {
            $this->getAndReplaceRules->each(function ($rulesGroup) use ($order, $line) {
                foreach ($rulesGroup as $rule) {
                    /* @var $rule DelivererImportRuleAbstract */
                    $rule->setOrder($order);
                    $rule->setData($line);

                    if ($rule->run()) {
                        break;
                    }
                }
            });
        }
    }

    private function runGetWithConditionRules(Order $order, $line): void
    {
        if ($this->getWithConditionRules->isNotEmpty()) {
            $this->getWithConditionRules->each(function ($rulesGroup) use ($order, $line) {
                foreach ($rulesGroup as $rule) {
                    /* @var $rule DelivererImportRuleAbstract */
                    $rule->setOrder($order);
                    $rule->setData($line);

                    if ($rule->run()) {
                        break;
                    }
                }
            });
        }
    }

    private function runGetRules(Order $order, array $line): void
    {
        if ($this->getRules->isNotEmpty()) {
            $this->getRules->each(function ($rule) use ($order, $line) {
                /* @var $rule DelivererImportRuleAbstract */
                $rule->setOrder($order);
                $rule->setData($line);
                $rule->run();
            });
        }
    }

    private function runSetRules(Order $order, array $line): void
    {
        if ($this->setRules->isNotEmpty()) {
            $this->setRules->each(function ($rule) use ($order, $line) {
                /* @var $rule DelivererImportRuleAbstract */
                $rule->setOrder($order);
                $rule->setData($line);
                $rule->run();
            });
        }
    }

    private function findOrderByRules(array $line, Collection $searchRules): ?Order
    {
        if ($searchRules->isEmpty()) {
            return null;
        }

        /* @var $ruleToRun DelivererImportRuleAbstract */
        $ruleToRun = $searchRules->shift();

        $ruleToRun->setData($line);
        $order = $ruleToRun->run();

        return empty($order) ? $this->findOrderByRules($line, $searchRules) : $order;
    }

    private function setSearchRules(): void
    {
        $this->searchRules = $this->importRules->whereInStrict('action', [
            DelivererRulesActionEnum::SEARCH_COMPARE,
            DelivererRulesActionEnum::SEARCH_REGEX,
        ]);
    }

    private function setSetRules(): void
    {
        $this->setRules = $this->importRules->whereStrict('action', DelivererRulesActionEnum::SET);
    }

    private function setGetRules(): void
    {
        $this->getRules = $this->importRules->whereStrict('action', DelivererRulesActionEnum::GET);
    }

    private function setGetAndReplaceRules(): void
    {
        $this->getAndReplaceRules = $this->importRules->whereStrict(
            'action',
            DelivererRulesActionEnum::GET_AND_REPLACE
        )->mapToGroups(function ($rule) {
            return [$rule->getImportRuleEntity()->db_column_name => $rule];
        });
    }

    private function setGetWithConditionRules(): void
    {
        $this->getWithConditionRules = $this->importRules->whereStrict(
            'action',
            DelivererRulesActionEnum::GET_WITH_CONDITION
        )->mapToGroups(function ($rule) {
            return [$rule->getImportRuleEntity()->db_column_name => $rule];
        });
    }
}

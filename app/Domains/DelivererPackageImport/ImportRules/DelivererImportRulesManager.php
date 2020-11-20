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

    public function __construct(
        DelivererImportRuleRepositoryEloquent $delivererImportRuleRepositoryEloquent,
        DelivererImportRuleFromEntityFactory $delivererImportRuleFromEntityFactory
    ) {
        $this->delivererImportRulesRepository = $delivererImportRuleRepositoryEloquent;
        $this->delivererImportRuleFromEntityFactory = $delivererImportRuleFromEntityFactory;
    }

    public function setDeliverer(Deliverer $deliverer): void
    {
        $this->deliverer = $deliverer;
    }

    public function prepareRules(): bool
    {
        $rules = $this->delivererImportRulesRepository->getDelivererImportRules(
            $this->deliverer
        );

        if (!empty($rules)) {
            $this->importRules = $rules->map(function ($item) {
                return $this->delivererImportRuleFromEntityFactory->create($item);
            });

            $this->setSearchRules();
            $this->setSetRules();
            $this->setGetRules();
            $this->setGetAndReplaceRules();
        }

        return !empty($this->importRules);
    }

    public function runRules(array $line): void
    {
        $order = $this->findOrderByRules($line);

        $this->runSetRules($order, $line);
        $this->runGetRules($order, $line);
        $this->runGetAndReplaceRules($order, $line);
    }

    private function runGetAndReplaceRules($order, $line): void
    {
        if ($this->getAndReplaceRules->isNotEmpty()) {
            $this->getAndReplaceRules->each(function ($rulesGroup, $key) use ($order, $line) {
                foreach ($rulesGroup as $rule) {
                    /* @var $rule DelivererImportRuleInterface */
                    $rule->setOrder($order);

                    if ($rule->run($line)) {
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
                /* @var $rule DelivererImportRuleInterface */
                $rule->setOrder($order);
                $rule->run($line);
            });
        }
    }

    private function runSetRules(Order $order, array $line): void
    {
        if ($this->setRules->isNotEmpty()) {
            $this->setRules->each(function ($rule) use ($order, $line) {
                /* @var $rule DelivererImportRuleInterface */
                $rule->setOrder($order);
                $rule->run($line);
            });
        }
    }

    private function findOrderByRules(array $line): ?Order
    {
        if ($this->searchRules->isNotEmpty()) {
            /* @var $ruleToRun DelivererImportRuleInterface */
            $ruleToRun = $this->searchRules->shift();

            $order = $ruleToRun->run($line);

            return empty($order) ? $this->findOrderByRules($line) : $order;
        }

        return null;
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
            /* @var $rule DelivererImportRuleInterface */
            return [$rule->getImportRuleEntity()->db_column_name => $rule];
        });
    }
}

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

    public function runRules(array $line)
    {
        $order = $this->findOrderByRule($line);

        dd($order);
    }

    private function findOrderByRule(array $line): ?Order
    {
        if ($this->searchRules->isNotEmpty()) {
            /* @var $ruleToRun DelivererImportRuleInterface */
            $ruleToRun = $this->searchRules->shift();

            $order = $ruleToRun->run($line);

            return empty($order) ? $this->findOrderByRule($line) : $order;
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
        );
    }
}

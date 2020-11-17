<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Factories\DelivererImportRuleFromEntityFactory;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Entities\Deliverer;
use Illuminate\Support\Collection;

class DelivererImportRulesManager
{
    private $delivererImportRulesRepository;

    private $delivererImportRuleFromEntityFactory;

    private $deliverer;

    /* @var $importRules Collection */
    private $importRules;

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
        }

        return !empty($this->importRules);
    }

    public function findOrder(array $line)
    {
        /*$searchCompareRule = $this->importRules->where(
            'action', '=', DelivererRulesActionEnum::SEARCH_COMPARE
        )->first();*/
    }

    private function findRulesForActions(array $actions): ?Collection
    {
        if (empty($actions)) {
            return null;
        }

        return $this->importRules->whereInStrict('action', $actions);
    }

    private function setSearchRules
}

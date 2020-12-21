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
    private $columnRepositoryFactory;

    public function __construct(DelivererImportRuleColumnRepositoryFactory $columnRepositoryFactory)
    {
        $this->columnRepositoryFactory = $columnRepositoryFactory;
    }

    public function create(DelivererImportRule $delivererImportRuleEntity)
    {
        $this->validate($delivererImportRuleEntity);

        switch ($delivererImportRuleEntity->getAction()->value) {
            case DelivererRulesActionEnum::SEARCH_COMPARE:
                return new DelivererImportRuleSearchCompare(
                    $delivererImportRuleEntity,
                    $this->columnRepositoryFactory->create(
                        $delivererImportRuleEntity->getColumnName()
                    )
                );
            case DelivererRulesActionEnum::SEARCH_REGEX:
                return new DelivererImportRuleSearchRegex(
                    $delivererImportRuleEntity,
                    $this->columnRepositoryFactory->create(
                        $delivererImportRuleEntity->getColumnName()
                    )
                );
            case DelivererRulesActionEnum::SET:
                return new DelivererImportRuleSet(
                    $delivererImportRuleEntity,
                    $this->columnRepositoryFactory->create(
                        $delivererImportRuleEntity->getColumnName()
                    )
                );
            case DelivererRulesActionEnum::GET:
                return new DelivererImportRuleGet(
                    $delivererImportRuleEntity,
                    $this->columnRepositoryFactory->create(
                        $delivererImportRuleEntity->getColumnName()
                    )
                );
            case DelivererRulesActionEnum::GET_AND_REPLACE:
                return new DelivererImportRuleGetAndReplace(
                    $delivererImportRuleEntity,
                    $this->columnRepositoryFactory->create(
                        $delivererImportRuleEntity->getColumnName()
                    )
                );
            default:
                throw new \Exception(sprintf(
                    'No import rule for action %s',
                    $delivererImportRuleEntity->getAction()->value
                ));
        }
    }

    private function validate(DelivererImportRule $delivererImportRuleEntity): void
    {
        if (!DelivererImportRule::canActionBePerformedOnColumn(
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
    }
}

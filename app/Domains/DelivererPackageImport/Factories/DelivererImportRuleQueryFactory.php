<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\DelivererImportRule;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Entities\Deliverer;

class DelivererImportRuleQueryFactory
{
    public function create(Deliverer $deliverer, DelivererImportRule $importRule): array
    {
        switch ($importRule->getAction()) {
            case DelivererRulesActionEnum::SEARCH:
                return [

                ];
        }
    }
}

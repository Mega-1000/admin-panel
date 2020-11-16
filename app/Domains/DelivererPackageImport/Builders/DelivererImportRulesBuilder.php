<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Builders;

use App\Domains\DelivererPackageImport\Factories\DelivererImportRuleFromRequestFactory;
use App\Entities\Deliverer;
use App\Http\DTOs\DelivererCreateImportRulesDTO;

class DelivererImportRulesBuilder
{
    private $delivererImportRuleFromRequestFactory;

    public function __construct(
        DelivererImportRuleFromRequestFactory $delivererImportRuleFromRequestFactory
    ) {
        $this->delivererImportRuleFromRequestFactory = $delivererImportRuleFromRequestFactory;
    }

    public function buildFromRequest(
        Deliverer $deliverer,
        DelivererCreateImportRulesDTO $delivererCreateImportRulesDTO
    ): ?array {
        $rulesFromRequest = $this->prepareRules($delivererCreateImportRulesDTO);

        if (empty($rulesFromRequest)) {
            return null;
        }

        $rulesCollection = [];

        foreach ($rulesFromRequest as $rule) {
            try {
                $rule = $this->delivererImportRuleFromRequestFactory->create($deliverer, $rule);
            } catch (\Exception $exception) {
                dd($exception->getMessage()); //todo log
            }

            $rulesCollection[] = $rule;
        }

        return $rulesCollection;
    }

    private function prepareRules(DelivererCreateImportRulesDTO $delivererCreateImportRulesDTO): array
    {
        $rules = [];

        if ($rulesFromRequest = $delivererCreateImportRulesDTO->getRules()) {
            foreach ($rulesFromRequest as $dataType => $values) {
                foreach($values as $key => $value) {
                    $rules[$key][$dataType] = $value;
                }
            }
        }

        return $rules;
    }
}

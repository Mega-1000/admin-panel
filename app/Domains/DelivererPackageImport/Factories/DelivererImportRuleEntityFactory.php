<?php declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesColumnNumberVO;
use App\Domains\DelivererPackageImport\ValueObjects\DelivererImportRulesValueVO;
use App\Entities\Deliverer;
use App\Entities\DelivererImportRule;

class DelivererImportRuleEntityFactory
{
    public static function createSearch(
        Deliverer $deliverer,
        DelivererRulesActionEnum $actionEnum,
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererImportRulesColumnNumberVO $columnNumberVO
    ): DelivererImportRule {
        return new DelivererImportRule([
            'deliverer_id' => $deliverer->id,
            'action' => $actionEnum->value,
            'db_column_name' => $columnNameEnum->value,
            'import_column_number' => $columnNumberVO->get(),
        ]);
    }

    public static function createSearchRegex(
        Deliverer $deliverer,
        DelivererRulesActionEnum $actionEnum,
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererImportRulesColumnNumberVO $columnNumberVO,
        DelivererImportRulesValueVO $valueVO
    ): DelivererImportRule {
        return new DelivererImportRule([
            'deliverer_id' => $deliverer->id,
            'action' => $actionEnum->value,
            'db_column_name' => $columnNameEnum->value,
            'import_column_number' => $columnNumberVO->get(),
            'value' => $valueVO->get(),
        ]);
    }

    public static function createSet(
        Deliverer $deliverer,
        DelivererRulesActionEnum $actionEnum,
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererImportRulesValueVO $valueVO
    ): DelivererImportRule {
        return new DelivererImportRule([
            'deliverer_id' => $deliverer->id,
            'action' => $actionEnum->value,
            'db_column_name' => $columnNameEnum->value,
            'value' => $valueVO->get(),
        ]);
    }

    public static function createGet(
        Deliverer $deliverer,
        DelivererRulesActionEnum $actionEnum,
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererImportRulesColumnNumberVO $columnNumberVO
    ): DelivererImportRule {
        return new DelivererImportRule([
            'deliverer_id' => $deliverer->id,
            'action' => $actionEnum->value,
            'db_column_name' => $columnNameEnum->value,
            'import_column_number' => $columnNumberVO->get(),
        ]);
    }

    public static function createGetAndReplace(
        Deliverer $deliverer,
        DelivererRulesActionEnum $actionEnum,
        DelivererRulesColumnNameEnum $columnNameEnum,
        DelivererImportRulesColumnNumberVO $columnNumberVO,
        DelivererImportRulesValueVO $searchValue,
        DelivererImportRulesValueVO $replaceValue
    ): DelivererImportRule {
        return new DelivererImportRule([
            'deliverer_id' => $deliverer->id,
            'action' => $actionEnum->value,
            'db_column_name' => $columnNameEnum->value,
            'import_column_number' => $columnNumberVO->get(),
            'value' => $searchValue->get(),
            'changeTo' => $replaceValue->get(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Factories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\Repositories\AllegroAdditionalServiceColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\AllegroCommissionColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\AllegroDepositValueColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\AllegroFormIdColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\AllegroOperationDateColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleColumnRepositoryInterface;
use App\Domains\DelivererPackageImport\Repositories\LetterNumberColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\RealCostForCompanyColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\RefundIdColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\SelloFormColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\SelloPaymentColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\ServiceCourierNameColumnRepositoryEloquent;

class DelivererImportRuleColumnRepositoryFactory
{
    public function create(DelivererRulesColumnNameEnum $columnNameEnum): DelivererImportRuleColumnRepositoryInterface
    {
        switch ($columnNameEnum->value) {
            case DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE:
                return app(AllegroAdditionalServiceColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_ALLEGRO_DEPOSIT_VALUE:
                return app(AllegroDepositValueColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID:
                return app(AllegroFormIdColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE:
                return app(AllegroOperationDateColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_ALLEGRO_COMMISSION:
                return app(AllegroCommissionColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_REFUND_ID:
                return app(RefundIdColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER:
                return app(LetterNumberColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY_COST:
                return app(RealCostForCompanyColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME:
                return app(ServiceCourierNameColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_PAYMENT:
                return app(SelloPaymentColumnRepositoryEloquent::class);
            case DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_FORM:
                return app(SelloFormColumnRepositoryEloquent::class);
            default:
                throw new \Exception(sprintf(
                    'No column repository for column %s',
                    $columnNameEnum->value
                ));
        }
    }
}

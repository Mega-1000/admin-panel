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
use App\Domains\DelivererPackageImport\Repositories\PaymentChannelColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\RealCostForCompanyColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\RefundIdColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\SelloFormColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\SelloPaymentColumnRepositoryEloquent;
use App\Domains\DelivererPackageImport\Repositories\ServiceCourierNameColumnRepositoryEloquent;

class DelivererImportRuleColumnRepositoryFactory
{
    public function create(DelivererRulesColumnNameEnum $columnNameEnum): DelivererImportRuleColumnRepositoryInterface
    {
        return match ($columnNameEnum->value) {
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => app(AllegroAdditionalServiceColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_DEPOSIT_VALUE => app(AllegroDepositValueColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID => app(AllegroFormIdColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE => app(AllegroOperationDateColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_COMMISSION => app(AllegroCommissionColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_PAYMENT_CHANNEL => app(PaymentChannelColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_REFUND_ID => app(RefundIdColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => app(LetterNumberColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY_COST => app(RealCostForCompanyColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME => app(ServiceCourierNameColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_PAYMENT => app(SelloPaymentColumnRepositoryEloquent::class),
            DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_FORM => app(SelloFormColumnRepositoryEloquent::class),
            default => throw new \Exception(sprintf(
                'No column repository for column %s',
                $columnNameEnum->value
            )),
        };
    }
}

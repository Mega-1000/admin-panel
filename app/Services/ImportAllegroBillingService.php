<?php

namespace App\Services;

use App\DTO\AllegroBilling\ImportAllegroBillingDTO;
use App\Entities\AllegroGeneralExpense;
use App\Entities\OrderPackage;
use App\Enums\AllegroImport\AllegroBillingAttachedValueParameterEnum;
use App\Repositories\AllegroGeneralExpenses;
use App\Repositories\OrderPackages;
use App\Helpers\AllegroBillingImportHelper;

class ImportAllegroBillingService
{
    public function __construct(
        protected OrderPackages              $orderPackagesRepository,
        protected AllegroBillingImportHelper $billingHelper,
    ) {}

    /**
     * Import billing entries
     *
     * @param array $data
     * @return void
     */
    public function import(array $data): void
    {
        foreach ($data as $dto) {
            $this->importSingle($dto);
        }
    }

    /**
     * Import single billing entry
     *
     * @param ImportAllegroBillingDTO $data
     * @return void
     */
    private function importSingle(ImportAllegroBillingDTO $data): void
    {
        AllegroGeneralExpenses::deleteAll();
        $billingEntry = AllegroGeneralExpenses::createFromDTO($data);
        $trackingNumber = $this->billingHelper->extractTrackingNumber($data->getOperationDetails());

        if (!$trackingNumber) return;

        $orderPackage = $this->orderPackagesRepository->getByLetterNumber($trackingNumber);

        if (empty($orderPackage)) {
            $this->updateBillingEntryNotAttached($billingEntry);
            return;
        }

        if (!$this->billingHelper->hasCourierMatch($data->getOperationDetails())) {
            $this->handleNoCourierMatch($billingEntry, $data, $orderPackage);
            return;
        }

        $this->updateOrderPackage($orderPackage, $data->getCharges(), 'SOD');
    }

    /**
     * @param AllegroGeneralExpense $billingEntry
     * @return void
     */
    private function updateBillingEntryNotAttached(AllegroGeneralExpense $billingEntry): void
    {
        $billingEntry->update([
            'attached_value_parameter',
            AllegroBillingAttachedValueParameterEnum::NOT_ATTACHED,
        ]);
    }

    /**
     * @param AllegroGeneralExpense $billingEntry
     * @param ImportAllegroBillingDTO $data
     * @param OrderPackage $orderPackage
     * @return void
     */
    private function handleNoCourierMatch(AllegroGeneralExpense $billingEntry, ImportAllegroBillingDTO $data, OrderPackage $orderPackage): void
    {
        if (!$this->billingHelper->hasCourierChargeMatch($data->getOperationDetails())) {
            $this->updateBillingEntryNotAttached($billingEntry);
            return;
        }

        $this->updateOrderPackage($orderPackage, $data->getCharges(), 'SOP');
    }

    /**
     * Update order package with real costs for company
     *
     * @param OrderPackage $orderPackage
     * @param string $charges
     * @param mixed|null $type
     * @return void
     */
    private function updateOrderPackage(OrderPackage $orderPackage, string $charges, mixed $type = null): void
    {
        $orderPackage->realCostsForCompany()->create([
            'cost' => (float)$charges,
            'type' => $type,
        ]);
    }

    /**
     * @param AllegroGeneralExpense $billingEntry
     * @param ImportAllegroBillingDTO $data
     * @return void
     */
    private function handleNoTrackingNumber(AllegroGeneralExpense $billingEntry, ImportAllegroBillingDTO $data): void
    {
        return null;
    }
}

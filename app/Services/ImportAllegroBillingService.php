<?php

namespace App\Services;

use App\DTO\AllegroBilling\ImportAllegroBillingDTO;
use App\Entities\AllegroGeneralExpense;
use App\Entities\Order;
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
     * @param array<ImportAllegroBillingDTO> $data
     * @return void
     */
    public function import(array $data): void
    {
        AllegroGeneralExpenses::deleteAll();

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
        $billingEntry = AllegroGeneralExpenses::createFromDTO($data);
        $operationDetails = $data->getOperationDetails();

        $trackingNumber = $this->getTrackingNumber($operationDetails);
        $order = null;

        if (!$trackingNumber) {
            $order = $this->getOrderFromAllegroId($operationDetails);
            $this->associateOrderToBillingEntry($billingEntry, $order);
        }

        $orderPackage = $this->getOrderPackageByLetterNumber($trackingNumber);

        if (empty($orderPackage)) {
            $this->updateBillingEntryNotAttached($billingEntry);
            return;
        }

        if (!$this->billingHelper->hasCourierMatch($operationDetails)) {
            $this->handleNoCourierMatch($billingEntry, $data, $orderPackage);
            return;
        }

        $this->updateOrderPackage($orderPackage, $data->getCharges(), 'SOD');

        $order = empty($order) ? $orderPackage->order : $order;

        $this->associateOrderToBillingEntry($billingEntry, $order);
    }

    private function getTrackingNumber($operationDetails): ?string
    {
        return $this->billingHelper->extractTrackingNumber($operationDetails);
    }

    private function getOrderFromAllegroId($operationDetails): ?Order
    {
        $allegroId = $this->billingHelper->extractAllegroId($operationDetails);
        return Order::where('allegro_form_id', $allegroId)->first();
    }

    private function associateOrderToBillingEntry($billingEntry, $order): void
    {
        if (empty($order)) {
            return;
        }

        $billingEntry->order()->associate($order);
    }

    private function getOrderPackageByLetterNumber($trackingNumber): ?OrderPackage
    {
        return dd($this->orderPackagesRepository->getByLetterNumber($trackingNumber));
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
        return;
    }
}

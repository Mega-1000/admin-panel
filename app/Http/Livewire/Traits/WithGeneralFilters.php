<?php

namespace App\Http\Livewire\Traits;

use Livewire\Component;

/**
 * Trait WithGeneralFilters
 *
 * @package App\Http\Livewire\Traits
 *
 * Trait for Livewire component to add general filters functionality
 * @link Component
 */
trait WithGeneralFilters
{
    public string $orderPackageFilterNumber = '';
    public bool $isSortingByPreferredInvoiceDate = false;
    public bool $onlyStyro = false;
    public bool $onlyPaidOffers = false;

    /**
     * Initialize general filters trait for Livewire component
     *
     * @return void
     */
    public function initWithGeneralFilters(): void
    {
        $this->orderPackageFilterNumber = json_decode($this->user->grid_settings)->order_package_filter_number ?? '';
        $this->isSortingByPreferredInvoiceDate = json_decode($this->user->grid_settings)->is_sorting_by_preferred_invoice_date ?? false;
        $this->onlyStyro = json_decode($this->user->grid_settings)->only_styro ?? false;
        $this->onlyPaidOffers = json_decode($this->user->grid_settings)->only_paid_offers ?? false;
    }

    /**
     * Update order package filter number
     *
     * @return void
     */
    public function updateOrderPackageFilterNumber(): void
    {
        $this->updateGridSettings('order_package_filter_number', $this->orderPackageFilterNumber);

        $this->reloadDatatable();
    }

    /**
     * Update is sorting by preferred invoice date
     *
     * @return void
     */
    public function updateIsSortingByPreferredInvoiceDate(): void
    {
        $this->updateGridSettings('is_sorting_by_preferred_invoice_date', !$this->isSortingByPreferredInvoiceDate);

        $this->reloadDatatable();
    }

    public function updateOnlyStyroFilter(): void
    {
        $this->updateGridSettings('only_styro', !$this->onlyStyro);

        $this->reloadDatatable();
    }

    public function updateOnlyPaidOffersFilter(): void
    {
        $this->updateGridSettings('only_paid_offers', !$this->onlyPaidOffers);

        $this->reloadDatatable();
    }


    /**
     * Update grid settings in user model based on authenticated user
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private function updateGridSettings(string $key, string $value): void
    {
        $this->user->update(
            ['grid_settings' => json_encode([
                $key => $value,
            ] + json_decode($this->user->grid_settings, true))]
        );
    }
}

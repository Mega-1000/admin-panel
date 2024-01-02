<?php

namespace App\Http\Livewire\Traits;

use App\User;
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

    /**
     * Initialize general filters trait for Livewire component
     *
     * @return void
     */
    public function initWithGeneralFilters(): void
    {
        $this->orderPackageFilterNumber = json_decode($this->user->grid_settings)->order_package_filter_number ?? '';
        $this->isSortingByPreferredInvoiceDate = json_decode($this->user->grid_settings)->is_sorting_by_preferred_invoice_date ?? false;
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

    /**
     * Update grid settings in user model based on authenticated user
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private function updateGridSettings(string $key, string $value): void
    {
        auth()->user()->update(
            ['grid_settings' => json_encode([
                $key => $value,
            ] + json_decode(auth()->user()->grid_settings, true))]
        );
    }
}

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

    /**
     * Initialize general filters trait for Livewire component
     *
     * @return void
     */
    public function initWithGeneralFilters(): void
    {
        $this->orderPackageFilterNumber = json_decode(auth()->user()->grid_settings)->order_package_filter_number ?? '';
    }

    /**
     * Update order package filter number
     *
     * @return void
     */
    public function updateOrderPackageFilterNumber(): void
    {
        auth()->user()->update(
            ['grid_settings' => json_encode([
                'order_package_filter_number' => $this->orderPackageFilterNumber,
            ])]
        );

        $this->reloadDatatable();

    }
}

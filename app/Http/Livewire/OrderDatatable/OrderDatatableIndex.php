<?php

namespace App\Http\Livewire\OrderDatatable;

use App\Helpers\CombinationGeneratorHelper;
use App\Http\Livewire\Traits\WithChecking;
use App\Http\Livewire\Traits\WithGeneralFilters;
use App\Http\Livewire\Traits\WithNonStandardColumnsSorting;
use App\Http\Livewire\Traits\WithFilters;
use App\Http\Livewire\Traits\WithNonstandardColumns;
use App\Http\Livewire\Traits\WithOrderDataMoving;
use App\Services\OrderDatatable\OrderDatatableRetrievingService;
use App\Livewire\Traits\OrderDatatable\WithPageLengthManagement;
use App\User;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Redirector;
use Livewire\WithPagination;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderDatatableIndex extends Component
{
    use
        WithFilters,
        WithPageLengthManagement,
        WithColumnsDragAndDrop,
        WithNonstandardColumns,
        WithNonStandardColumnsSorting,
        WithGeneralFilters,
        WithChecking,
        WithOrderDataMoving,
        WithPagination;

    public array $orders;
    public bool $loading = false;
    public $listeners = ['updateColumnOrderBackend', 'reloadDatatable', 'semiReloadDatatable'];
    public bool $shouldRedirect = false;
    public User $user;

    /**
     * OrderDatatableIndex extends Livewire component and adds datatable functionality to it
     *
     * @return View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render(): View
    {
        /** @var User $user */
        $this->user = User::find(auth()->id());

        $this->orders = (new OrderDatatableRetrievingService())->getOrders(
            $this->getPageLengthProperty(), dd($this->user->grid_settings) ?? '[]'
        );

        $redirectInstance = $this->reRenderFilters();
        if (!is_null($redirectInstance)) {
            $this->shouldRedirect = true;
        }

        $this->initWithNonstandardColumns();
        $this->initWithNonStandardColumnsSorting();
        $this->initWithGeneralFilters();
        $this->initWithChecking();

        return view('livewire.order-datatable.order-datatable-index');
    }

    /**
     * Listener for event from frontend or child components
     *
     * @return Redirector
     */
    public function reloadDatatable(): mixed
    {
        return redirect()->route(
            'orders.index',
            [
                'page' => $this->page,
                'applyFiltersFromQuery' => true
            ]
        );
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function semiReloadDatatable(): void
    {
        $this->render();
    }

    protected function generateCombinations(array $match): array
    {
        $combinations = [[]];
        foreach ($match as $placeholder) {
            CombinationGeneratorHelper::generateCombination(
                $combinations,
                $placeholder
            );
        }

        return $combinations;
    }

    private function getPageLengthProperty(): int
    {
        return session()->get('pageLength', 10);
    }
}

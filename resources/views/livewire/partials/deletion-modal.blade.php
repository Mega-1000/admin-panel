@if ($this->isDeletionConfirmationModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="modal-overlay absolute inset-0 bg-gray-900 opacity-50"></div>
        <div class="modal-container bg-white w-96 mx-auto rounded-lg shadow-lg z-50">
            <div class="modal-content py-4 px-6">
                <h3 class="text-lg font-semibold mb-4">@lang('product_stock_positions.confirmation_modal.title')</h3>
                <p class="text-gray-700 mb-6">@lang('product_stock_positions.confirmation_modal.message')</p>
                <div class="flex justify-end">
                    <button wire:click="{{$submitFunction ?? 'submitForm'}}" class="btn btn-primary">
                        @lang('product_stock_positions.confirmation_modal.confirm')
                    </button>

                    <button wire:click="closeModal" class="btn btn-danger">
                        @lang('product_stock_positions.confirmation_modal.cancel')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

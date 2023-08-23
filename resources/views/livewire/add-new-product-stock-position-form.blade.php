<div>
    <form action="{{ action('ProductStockPositionsController@store', ['id' => $this->id]) }}" method="POST">
        {{ csrf_field() }}
        <div class="product_stock_positions-general" id="general">
            <div class="form-group">
                <label for="lane">@lang('product_stock_positions.form.lane')</label>
                <input wire:model="lane" type="text" class="form-control" id="lane" name="lane"
                       value="{{ old('lane') }}">
            </div>
            <div class="form-group">
                <label for="bookstand">@lang('product_stock_positions.form.bookstand')</label>
                <input wire:model="bookstand" type="text" class="form-control" id="bookstand" name="bookstand"
                       value="{{ old('bookstand') }}">
            </div>
            <div class="form-group">
                <label for="shelf">@lang('product_stock_positions.form.shelf')</label>
                <input wire:model="shelf" type="text" class="form-control" id="shelf" name="shelf"
                       value="{{ old('shelf') }}">
            </div>
            <div class="form-group">
                <label for="position">@lang('product_stock_positions.form.position')</label>
                <input wire:model="position" type="text" class="form-control" id="position" name="position"
                       value="{{ old('position') }}">
            </div>
            <div class="form-group">
                <label for="position_quantity">@lang('product_stock_positions.form.position_quantity')</label>
                <input wire:model="position_quantity" type="number" class="form-control" id="position_quantity" name="position_quantity"
                       value="{{ old('position_quantity') }}">
            </div>
        </div>

        <div class="btn btn-primary" wire:click="submitForm">@lang('voyager.generic.save')</div>
    </form>

    {{ $this->isDeletionConfirmationModalOpen }}
    @if ($this->isDeletionConfirmationModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="modal-overlay absolute inset-0 bg-gray-900 opacity-50"></div>
                <div class="modal-container bg-white w-96 mx-auto rounded-lg shadow-lg z-50">
                    <div class="modal-content py-4 px-6">
                        <h3 class="text-lg font-semibold mb-4">@lang('product_stock_positions.confirmation_modal.title')</h3>
                        <p class="text-gray-700 mb-6">@lang('product_stock_positions.confirmation_modal.message')</p>
                        <div class="flex justify-end">
                            <button wire:click="submitForm" class="btn btn-primary">
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
</div>

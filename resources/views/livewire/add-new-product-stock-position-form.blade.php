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

        @include('livewire.partials.deletion-modal')
    </form>
</div>

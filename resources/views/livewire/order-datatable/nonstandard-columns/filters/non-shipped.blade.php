<th>
    <div><span>@lang('orders.table.packages_not_sent')</span></div>
    <div class="input_div">
        <select class="columnSearchSelect" id="columnSearch-packages_not_sent" wire:model="{{ $updateFilterMethodName }}" wire:input="{{ $updateFilterPropertyName }}">
            <option value="">Wszystkie</option>
            @foreach(\App\Entities\OrderPackage::select('delivery_courier_name')->distinct()->get() as $courier)
                <option
                    value="{{$courier->delivery_courier_name}}">{{$courier->delivery_courier_name}}</option>
            @endforeach
        </select>
    </div>
</th>

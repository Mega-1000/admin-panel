<div class="space-y-2">
    @foreach($columns as $k => $column)
        <label class="flex justify-between w-1/2 mx-auto p-4 ">
            <div>
                <span class="mr-2">{{ $column['label'] }}</span>
            </div>

            <div>
                <input type="checkbox" wire:model="columns.{{ $k }}.hidden" class="form-checkbox h-5 w-5 text-indigo-600">
            </div>
        </label>
        <hr class="my-2">
    @endforeach
</div>

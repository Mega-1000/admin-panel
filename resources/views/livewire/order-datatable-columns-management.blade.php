<div class="space-y-2">
    @foreach($columns as $k => $column)
        <label class="flex justify-between w-1/2 mx-auto p-4 ">
            <div>
                <span class="mr-2">{{ $column['label'] }}</span>
            </div>

            <div>
                Wyświetlaj
                <input type="checkbox" wire:model="columns.{{ $k }}.hidden" class="form-checkbox h-5 w-5 text-indigo-600">

                Szerokość
                <input type="number" wire:model="columns.{{ $k }}.size" class="form-input w-16 text-indigo-600">
            </div>
        </label>
        <hr class="my-2">
    @endforeach
</div>

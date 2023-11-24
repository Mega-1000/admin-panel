<div class="filter-by-labels-in-group">
    <span class="order-label"
          wire:click="toggleContainer"
          style="display: block; margin-top: 5px;"
    >
        Filtruj po etykietach
    </span>

    @if($this->showContainer)
        <div style="background-color: white">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                @foreach($this->labels as $label)
                    <span
                        style="cursor: pointer"
                        wire:click="selectCurrent({{ $label->id }})"
                    >
                        <i class="{{ $label->icon_name }}" style="font-size: 24px; background-color: {{ $label->color }}; padding: 5px"></i>
                    </span>
                    @endforeach
            </div>



            <div class="filter-by-labels-in-group__clear">
                <button class="btn btn-warning" wire:click="clearSelected">wyczyść</button>
            </div>
        </div>
    @endif
</div>

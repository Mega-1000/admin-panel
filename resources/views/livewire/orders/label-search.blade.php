<div class="filter-by-labels-in-group">
    <span class="order-label"
          wire:click="toggleContainer"
          style="display: block; margin-top: 5px;"
    >
        Filtruj po etykietach
    </span>

    @if($this->showContainer)
        <div style="background-color: white">
            <div style="display: flex;">
                @foreach($this->labels as $label)
                    <span
                          class="order-label filter-by-labels-in-group-input-change"
                          wire:click="selectCurrent({{ $label['id'] }})"
                    >
                       {{ $label->name }}
                    </span>
                @endforeach
            </div>

            <div class="filter-by-labels-in-group__clear">
                <button class="btn btn-warning" wire:click="clearSelected">wyczyść</button>
            </div>
        </div>
    @endif
</div>

<div>
    <form wire:submit.prevent="submitForm">
        <div>
            <input type="text" class="form-control" wire:model="quantity">
        </div>

        <div>
            <input type="checkbox" wire:model="saveOnDiffrentPosition">
            Zapisz na innej pozycji
        </div>

        <div>
            @if($this->saveOnDiffrentPosition)
                Alejka
                <input type="text" wire:model="lane" class="form-control">

                Półka
                <input type="text" wire:model="bookstand" class="form-control">

                Regał
                <input type="text" wire:model="shelf" class="form-control">

                Pozycja
                <input type="text" wire:model="position" class="form-control">
            @endif
        </div>

        @if($this->recentSuccess)
            <div class="alert alert-success">
                {{ $this->recentSuccess }}
            </div>
        @endif

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>
</div>

<td>
    <textarea type="text" wire:model="comment" wire:input="saveComments">
    </textarea>

    <button wire:click="savePackage" class="btn btn-primary">
        Stwórz paczkę
    </button>

    @if($this->isModalOpen)
        <br>
        <div>
            Wpisz numer zamówienia:
            <input type="number" wire:model="packageOfferId">
            <button class="btn btn-success" wire:click="savePackage">
                Stwórz paczkę
            </button>
        </div>
    @endif

</td>

<div>
    <form wire:submit.prevent="submitForm" method="post">
        @csrf

        <input type="text" name="title" placeholder="Tytuł" class="form-control" wire:model="title">

        <div>
            znaczniki z csv z kolumny
            13
            ktore bedą brane pod uwage do sumy ilości aczkolwiek kazdy nalezy oddzielic przcinkiem bez spacji
        </div>
        <input type="text" name="item_names" placeholder="Nazwy produktów" class="form-control" wire:model="itemNames">

        <div>
            ilosc sztuk zakupiona w calej ofercie produktow ktore maja znaczniki podane w polu 3
        </div>
        <input type="number" name="min_quantity" placeholder="Minimalna ilość" class="form-control" wire:model="minQuantity">

        <div>
            Kod php do liczenia
        </div>
        <textarea placeholder="Kod php" class="form-control" wire:model="phpCode"></textarea>

        <br>
        <br>

        <button class="btn btn-primary" wire:click.prevent="addMessage">
            Dodaj wiadomość
        </button>

        <br>
        <br>

        @foreach($this->messages as $k => $message)
            <form>
                Tytuł wiadomości
                <input type="text" class="form-control" wire:model="messages.{{ $k }}.title">

                Treść wiadomości
                <textarea class="form-control" wire:model="messages.{{ $k }}.message"></textarea>

                Opóźnienie wysłania wiadomości
                <input type="text" class="form-control" wire:model="messages.{{ $k }}.delay_time">

                <button class="btn btn-danger" wire:click.prevent="deleteMessage({{ $k }})">
                    Usuń wiadomość
                </button>
            </form>
            <hr>
        @endforeach

        <button class="btn btn-primary mt-5" wire:click="submitForm">
            Zapisz
        </button>
    </form>
</div>

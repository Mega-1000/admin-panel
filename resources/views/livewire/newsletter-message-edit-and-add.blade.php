<div>
    <form wire:submit.prevent="submitForm">
        <input type="text" wire:model="labelIds" class="form-control" placeholder="id etykiet (wypisz po przecinku)">

        <input type="text" wire:model="title" class="form-control mt-4" style="margin-top: 20px;" placeholder="Tytuł wiadomości">

        <textarea id="content"  wire:model="content" class="form-control mt-4" style="margin-top: 20px; height: 400px" placeholder="Treść wiadomości"></textarea>

        <input type="checkbox" wire:model="sendToAll">
        Wyślij wiadomości
        <br>

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>

    <div class="mt-4">
        <h3>Podgląd</h3>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ $title }}</h5>
            </div>
            <div class="card-body">
                {!! str_replace('<br />', '\n', $content) !!}
            </div>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Id</th>
            <th>Tytuł</th>
            <th>Treść</th>
            <th>Data wysłania</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>
            @foreach($this->messages as $message)
                <tr>
                    <th>
                        {{ $message->id }}
                    </th>
                    <th>
                        {{ $message->title }}
                    </th>
                    <th>
                        {{ $message->content }}
                    </th>
                    <th>
                        {{ $message->created_at }}
                    </th>
                    <th>
                        <a href="{{ route('newsletter_messages.create', ['message' => $message->id]) }}" class="btn btn-primary">
                            Edytuj
                        </a>

                        <button class="btn btn-danger" wire:click="deleteMessage({{ $message['id'] }})">
                            Usuń
                        </button>
                    </th>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


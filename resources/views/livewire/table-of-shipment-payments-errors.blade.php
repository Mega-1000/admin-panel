<div>
    <form wire:submit.prevent="searchForOrders">
        <input type="text" class="form-control" wire:model="from" placeholder="od">
        <input type="text" class="form-control" wire:model="to" placeholder="do">

        <button class="btn btn-primary">
            Szukaj
        </button>
    </form>

    @if($this->orders->count() >= 0 && $showTable)
        <div class="alert alert-success">
            Znaleziono {{ $this->orders->count() }} zamówień
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Nazwa
                    </th>
                    <th>
                        Wyświetl
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($this->orders as $order)
                    <tr>
                        <td>
                            {{ $order->id }}
                        </td>
                        <td>
                            {{ $order->name }}
                        </td>
                        <td>
                            <button class="btn btn-primary">
                                <a href="{{ route('orders.edit', $order->id) }}">
                                    Wyświetl
                                </a>
                            </button>
                        </td>
                    </tr>
                @endforeach
        </table>
    @endif

</div>

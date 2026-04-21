<form action="" method="POST">
    @csrf

    @php
        $lp = 0;
    @endphp

    @foreach($this->order->items as $item)
        <h3>
            {{ ++$lp }}. |
            {{ $item->product->name }} |
            {{ $item->product->product_name_manufacturer }} |
            {{ $item->product->supplier_product_symbol }} |
            {{ $item->product->packing->unit_commercial }} |
            {{ $item->product->packing->numbers_of_basic_commercial_units_in_pack }} |
            {{ $item->product->packing->unit_of_collective }} |
            {{ $item->product->packing->number_of_sale_units_in_the_pack }} |

            <a target="_blank" href="{{ route('product_stocks.edit', $item->product->stock->id)}}" class="btn btn-primary">
                Pokaż stan magazynowy
            </a>
        </h3>


        <div class="mt-5">
            <hr>
            <table>
                <thead>
                <tr>
                    <th>Wpisz przyjętą ilość</th>
                    <th>Ilość produktów</th>
                    <th>Aleja</th>
                    <th>Regał</th>
                    <th>Półka</th>
                    <th>Pozycja</th>
                    <th>Przyciski</th>
                </tr>
                </thead>
                <tbody>

                <button wire:click.prevent="addPosition({{ $item }})" class="btn btn-primary">
                     Dodaj pozycję
                </button>

                @foreach($item->product->stock->position as $productStockPosition)
                        <tr>
                            <td>
                                <input type="number" id="position[{{ $item->id }}][{{ $productStockPosition->id }}]" name="position[{{ $item->id }}][{{ $productStockPosition->id }}]">
                            </td>
                            <td>{{ $productStockPosition->position_quantity }}</td>
                            <td>{{ $productStockPosition->lane }}</td>
                            <td>{{ $productStockPosition->bookstand }}</td>
                            <td>{{ $productStockPosition->shelf }}</td>
                            <td>{{ $productStockPosition->position }}</td>
                            <td></td>
                        </tr>

                @endforeach
                @php($i = 0)
                @foreach($this->creatingPositions[$item->id] ?? [] as $index => $creatingPosition)
                    <tr>
                        <td>
                            <input
                                type="number"
                                wire:model="creatingPositions.{{ $item->id }}.{{ $index }}.position_quantity"
                            >
                        </td>
                        <td>

                        </td>
                        <td>
                            <input
                                type="number"
                                wire:model="creatingPositions.{{ $item->id }}.{{ $index }}.lane"
                                placeholder="numer"
                            >
                        </td>
                        <td>
                            <input
                                type="number"
                                wire:model="creatingPositions.{{ $item->id }}.{{ $index }}.bookstand"
                                placeholder="aleja"
                            >
                        </td>
                        <td>
                            <input
                                type="number"
                                wire:model="creatingPositions.{{ $item->id }}.{{ $index }}.shelf"
                                placeholder="regał"
                            >
                        </td>
                        <td>
                            <input
                                type="string"
                                wire:model="creatingPositions.{{ $item->id }}.{{ $index }}.position"
                                placeholder="półka"
                            >
                        </td>
                        <td>
                            <button
                                class="btn btn-primary"
                                wire:click.prevent="savePosition({{ $item->id }}, {{ $index }})"
                                placeholder="pozycja"
                            >
                                Zapisz
                            </button>

                            <button class="btn btn-danger" wire:click.prevent="cancelAdding({{ $item->id }}, {{ $index }})">
                                Anulluj dodawanie
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <input disabled class="form-control" type="text" name="quantity[{{ $item->id }}]" value="{{ $item->quantity }}">
        </div>
    @endforeach

    @include('livewire.partials.deletion-modal', [
        'submitFunction' => "confirmDeletion",
    ])

    <button class="mt-5 btn btn-primary">
        Zapisz
    </button>
</form>

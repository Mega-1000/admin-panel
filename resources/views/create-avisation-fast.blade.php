<form action="{{ route('storeAvisation', $order->id) }}" method="POST">
    @csrf

    Kwota wpłaty
    <input type="text" name="declared_sum">
    <br>
    <br>


    Magazyn do awizacji
    <input type="text" value="{{ $order->warehouse->symbol }}" name="warehouse-symbol">
    <br>
    <br>

    Rodzaj transportu
    <select>
        <option value="1">
            Transport Fabryczny
        </option>
        <option value="2">
            Odbiór osobisty
        </option>
    </select>

    <button class="btn btn-primary">
        Zatwierdź
    </button>

</form>

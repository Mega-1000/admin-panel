<a class="btn btn-primary" href="{{ route('orders.edit', $order['id']) }}">
    Edytuj
</a>

<form action="{{ route('orders.destroy', $order['id']) }}" method="POST">
    @method('delete')
    @csrf
    <button class="btn btn-danger" id="delete">Usuń</button>
</form>
<br>


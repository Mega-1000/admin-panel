<a class="btn btn-sm btn-primary w-100 edit" href="{{ route('orders.edit', $order['id']) }}">
    Edytuj
</a>

<form action="/admin/orders/{{$order['id']}}" method="POST">
    @method('delete')
    @csrf
    <button class="btn btn-danger btn-sm edit" id="O">Usuń</button>
</form>

<a href="/admin/orderReturn/{{ $order['id'] }}" class="btn btn-sm btn-danger edit">
    <i class="glyphicon glyphicon-share-alt"></i>
    <span class="hidden-xs hidden-sm">Zwrot</span>
</a>


<a href="/admin/allegro/return-payment/{{ $order['id'] }}" class="btn btn-sm btn-danger edit">
    <i class="glyphicon glyphicon-share-alt"></i>
    <span class="hidden-xs hidden-sm">Zwrot Allegro</span>
</a>

@if((Auth::user()->role_id == 1 || Auth::user()->role_id == 2) && Auth::user()->id === User::ORDER_DELETE_USER)
    <button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord(' + {{ $order['id'] }} + ')">
        <i class="voyager-trash"></i>
        <span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>
    </button>
@endif

<a target="_blank" class="btn btn-sm btn-primary" href="/admin/create-package-product-order/${id}">Stwórz produkt pakowy</a>

@if ($order['is_buying_admin_side'])
    <a class="btn btn-primary" href="/admin/accept-products/{{ $order['id'] }}" target="__blank">Przyjmij na stany magazynowe</a>
@endif


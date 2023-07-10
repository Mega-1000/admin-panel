@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com"></script>

<script defer>
    function showHide() {
        setTimeout(() => {
            let elements = document.getElementsByClassName('hidden');

            elements = Array.from(elements);

            for (let i = 0; i < elements.length; i++) {
                elements[i].classList.remove('hidden');
                console.log(elements[i] )
            }
        }, 1000)
    }

    showHide();
</script>
@section('table')
    <a class="mb-4 btn btn-success" href="{{ route('discounts.create') }}">
        Stwórz
    </a>

    <table class="table table-bordered">
        <tr>
            <th>
                ID
            </th>
            <th>
                Opis
            </th>
            <th>
                Stara cena
            </th>
            <th>
                Nowa cena
            </th>
            <th>
                Nazwa produktu
            </th>
            <th>
                Akcje
            </th>
        </tr>
        @foreach($discounts as $discount)
            <tr>
                <td>
                    {{ $discount->id }}
                </td>
                <td>
                    {{ $discount->description }}
                </td>
                <td>
                    {{ $discount->old_price }}
                </td>
                <td>
                    {{ $discount->new_price }}
                </td>
                <td>
                    {{ $discount->product->name }}
                </td>
                <td class="d-flex">
                    <form
                        class="d-inline"
                        method="post"
                        action="{{ route('discounts.destroy', ['discount' => $discount->id]) }}"
                    >
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger">
                            Usuń
                        </button>
                    </form>

                    <a href="{{ route('discounts.edit', ['discount' => $discount->id]) }}" class="btn btn-primary">
                        Edytuj
                    </a>
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $discounts->links() }}
    </div>

@endsection

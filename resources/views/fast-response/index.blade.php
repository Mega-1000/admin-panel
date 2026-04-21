@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>

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
    <a href="{{ route('fast-response.create') }}" class="my-2 px-4 py-2 bg-blue-500 text-white rounded">
        Stwórz
    </a>

    <table class="table table-bordered">
        <tr>
            <th>
                ID
            </th>
            <th>
                Tytuł
            </th>
            <th>
                opis
            </th>
            <th>
                Akcje
            </th>
        </tr>
        @foreach($fastResponses as $operation)
            <tr>
                <td>
                    {{ $operation->id }}
                </td>
                <td>
                    {{ $operation->title }}
                </td>
                <td>
                    {{ $operation->content }}
                </td>
                <td>
                    <form method="post" action="{{ route('fast-response.destroy', $operation->id) }}">
                        @csrf
                        @method('delete')

                        <button class="btn btn-danger">
                            Usuń
                        </button>
                    </form>

                    <a class="btn btn-primary" href="{{ route('fast-response.edit', $operation->id) }}">
                        Edytuj
                    </a>
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $fastResponses->links() }}
    </div>
@endsection

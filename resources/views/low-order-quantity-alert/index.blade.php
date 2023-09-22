@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>
@section('table')
    <a href="{{ route('low-quantity-alerts.create') }}" class="my-2 px-4 py-2 bg-blue-500 text-white rounded">
        Stwórz
    </a>

    <table class="table table-bordered">
        <tr>
            <th>
                ID
            </th>
            <th>
                wiadomość
            </th>
            <th>
                ilość
            </th>
            <th>
                Akcje
            </th>
        </tr>
        @foreach($messages as $message)
            <tr>
                <td>
                    {{ $message->id }}
                </td>
                <td>
                    {{ $message->message }}
                </td>
                <td>
                    {{ $message->min_quantity }}
                </td>
                <td>
                    <form method="post" action="{{ route('low-quantity-alerts.destroy', $message->id) }}">
                        @csrf
                        @method('delete')

                        <button class="btn btn-danger">
                            Usuń
                        </button>
                    </form>

                    <a class="btn btn-primary" href="{{ route('low-quantity-alerts.edit', $message->id) }}">
                        Edytuj
                    </a>
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $messages->links() }}
    </div>
@endsection

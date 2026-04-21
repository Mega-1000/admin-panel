@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>
@section('table')
    <a href="{{ route('product-packets.create') }}" class="btn btn-success">
        Stwórz nową paczkę
    </a>

    <table class="table table-bordered">
        <tr>
            <th>
                ID
            </th>
            <th>
                Nazwa
            </th>
            <th>
                Stworzono
            </th>
            <th>
                akcje
            </th>
        </tr>
        @foreach($packets as $packet)
            <tr>
                <td>
                    {{ $packet->id }}
                </td>
                <td>
                    {{ $packet->packet_name }}
                </td>
                <td>
                    {{ $packet->created_at }}
                </td>
                <td>
                    <a href="{{ route('product-packets.edit', $packet->id) }}" class="btn btn-primary">Zobacz</a>
                    <form method="post" action="{{ route('product-packets.destroy', $packet->id) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger">
                            Usuń
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $packets->links() }}
    </div>
@endsection

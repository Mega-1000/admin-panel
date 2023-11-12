@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>

@section('table')
    <a href="{{ route('newsletter.create') }}" class="btn btn-primary">
        Dodaj
    </a>

    <form action="{{ route('newsletter.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file">

        <button class="btn btn-primary">
            Importuj plik json
        </button>
    </form>

    <table class="table-bordered table">
        <thead>
            <tr>
                <th>
                    Kategoria
                </th>
                <th>
                    Symbol produktu
                </th>
                <th>
                    Akcje
                </th>
                <th>
                    Url aukcji
                </th>
                <th>
                    Opis
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($newsletters as $newsletter)
                <tr>
                    <td>
                        {{ $newsletter->category }}
                    </td>
                    <td>
                        {{ $newsletter->product }}
                    </td>
                    <td>
                        {{ $newsletter->auction_url }}
                    </td>
                    <td>
                        {{ $newsletter->description }}
                    </td>
                    <td>
                        <a href="{{ route('newsletter.edit', $newsletter->id) }}" class="btn btn-primary">
                            Edytuj
                        </a>

                        <form action="{{ route('newsletter.destroy', $newsletter->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">
                                Usuń
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
    </table>

    {{ $newsletters->links() }}
@endsection

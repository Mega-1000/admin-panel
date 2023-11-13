@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <a class="btn btn-success mb-4" href="{{ route('newsletter-packets.create') }}">
        Dodaj nowy
    </a>

    <br>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>
                    Id
                </th>
                <th>
                    Id wpisów w gazetce (po przecinku)
                </th>
                <th>
                    Akcje
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($newsletterPackets as $newsletterPacket)
                <tr>
                    <td>
                        {{ $newsletterPacket->id }}
                    </td>
                    <td>
                        {{ $newsletterPacket->newsletter_entries_ids }}
                    </td>
                    <td>
                        <a href="{{ route('newsletter-packets.edit', $newsletterPacket->id) }}" class="btn btn-primary">
                            Edytuj
                        </a>

                        <form action="{{ route('newsletter-packets.destroy', $newsletterPacket->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                Usuń
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <form action="{{ route('newsletter-packets.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="newsletter_entries_ids">
                Id wpisów w gazetce (po przecinku)
            </label>
            <input class="form-control" id="newsletter_entries_ids" name="newsletter_entries_ids" type="text" value="{{ old('newsletter_entries_ids') }}">
        </div>

        <button type="submit">
            Zapisz
        </button>
    </form>
@endsection

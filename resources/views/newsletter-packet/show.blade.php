@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <form action="{{ route('newsletter-packets.update', $newsletterPacket->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="newsletter_entries_ids">
                Id wpisów w gazetce (po przecinku)
            </label>
            <input class="form-control" id="newsletter_entries_ids" name="newsletter_entries_ids" type="text" value="{{ $newsletterPacket->newsletter_entries_ids }}">
        </div>

        <button type="submit" class="btn btn-primary">
            Zapisz
        </button>
    </form>
@endsection

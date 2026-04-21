@extends('layouts.datatable')

@section('table')
<form method="post">
    @csrf

    <div>
        Data przypomnienia
        <input type="date" name="from_date" class="form-control">
    </div>

    <div class="mt-4">
        Email prospekta
        <input type="email" name="prospect_email" class="form-control">
    </div>

    <div class="mt-4">
        Notatki dodatkowe
        <textarea name="notes" class="form-control"></textarea>
    </div>

    <button class="btn btn-success">
        Zapisz
    </button>
</form>
@endsection

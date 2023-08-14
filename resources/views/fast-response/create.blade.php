@extends('layouts.datatable')

@section('table')
    <form action="{{ route('fast-response.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="title">Tytuł</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Tytuł">
        </div>
        <div class="form-group">
            <label for="content">Treść</label>
            <textarea name="content" id="content" cols="30" rows="10" class="form-control" placeholder="Treść"></textarea>
        </div>

        <button class="btn btn-primary">
            Stwórz
        </button>
    </form>
@endsection

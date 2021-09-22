@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> Edytuj regulamin allegro
    </h1>
@endsection

@section('table')

    <form action="" method="post">
        {{ csrf_field() }}

        <div class="form-group">
            <textarea name="content" id="" cols="30" rows="30" class="form-control">{{ setting('site.new_allegro_order_msg') }}</textarea>
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Zapisz</button>
        </div>
    </form>
@endsection

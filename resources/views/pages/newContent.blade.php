@extends('layouts.datatable')
@section('app-header')

    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('pages.title')
    </h1>
    <style>
        .tags {
            width: 100%;
        }

        .tag {
            width: 50%;
            float: right;
        }
    </style>
@endsection

@section('table')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('pages.saveContent',['id' => $id]) }}" method="POST">
        {{ csrf_field() }}
        <div id="general" class="statuses-general">
            <label class="form-group">
                Tytuł
                <input class="form-control" id="title" name="title" type="text">
            </label>
            <br>
            <label>
                Treść strony
                <textarea class="content" name="content" id="content"></textarea>
            </label>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('javascript')
    <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
    <script>
        tinymce.init({
            selector: 'textarea.content',
            width: 900,
            height: 300
        });
    </script>
@endsection

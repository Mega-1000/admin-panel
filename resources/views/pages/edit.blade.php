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
    <form action="{{ route('pages.store') }}" method="POST">
        {{ csrf_field() }}
        <div id="general" class="statuses-general">

            <input hidden id="id" name="id" value="{{$page->id}}">
            <label class="form-group">
                Kategoria nadrzędna
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="0" @if($page->parent_id == null) selected @endif>
                        brak
                    </option>
                    @foreach($pages as $category)
                        @if($category->id === $page->id)
                            @continue
                        @endif

                        <option value={{$category->id}}
                        @if($category->id === $page->parent_id)
                            selected
                            @endif>
                            {{$category->name}}
                        </option>
                    @endforeach
                </select>
            </label>
            <br>
            <label class="form-group">
                Nazwa kategorii
                <input class="form-control" id="name" name="name" type="text" value="{{$page->name}}">
            </label>
            <br>
            <label>
                Kolejność
                <input class="form-control" id="order" name="order" type="number" value="{{$page->order}}">
            </label>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
    <br>
@endsection


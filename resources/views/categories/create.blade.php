@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-plus"></i> Nowa kategoria
        <a href="{{ route('categories.index') }}" class="btn btn-info pull-right">
            <i class="fa fa-list"></i> Lista kategorii
        </a>
    </h1>
@endsection

@section('app-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('categories.store') }}" method="POST">
                            @csrf

                            @include('categories._form', ['youtube' => []])

                            <div class="form-group" style="margin-top:20px;">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Utwórz kategorię
                                </button>
                                <a href="{{ route('categories.index') }}" class="btn btn-default">Anuluj</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('categories._youtube-scripts')

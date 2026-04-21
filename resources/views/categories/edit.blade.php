@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="fa fa-edit"></i> Edycja kategorii: <strong>{{ $category->name }}</strong>
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

                        @if(session('message'))
                            <div class="alert alert-{{ session('alert-type', 'info') }}">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('categories.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @include('categories._form', ['category' => $category, 'youtube' => $youtube])

                            <div class="form-group" style="margin-top:20px;">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Zapisz zmiany
                                </button>
                                <a href="{{ route('categories.index') }}" class="btn btn-default">Anuluj</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-heading"><h3 class="panel-title">Informacje</h3></div>
                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            <dt>ID</dt><dd>{{ $category->id }}</dd>
                            <dt>Slug (rewrite)</dt><dd><code>{{ $category->rewrite }}</code></dd>
                            <dt>Poziom</dt>
                            <dd>
                                @if(is_null($category->parent_id))
                                    <span class="badge badge-primary">Główna (1)</span>
                                @elseif(is_null($category->parentCategory?->parent_id))
                                    <span class="badge badge-info">Podkategoria (2)</span>
                                @else
                                    <span class="badge badge-default">Sub-podkategoria (3)</span>
                                @endif
                            </dd>
                            <dt>Produkty</dt><dd>{{ $category->products()->count() }}</dd>
                            <dt>Podkategorie</dt><dd>{{ $category->children()->count() }}</dd>
                            <dt>Utworzona</dt><dd>{{ $category->created_at?->format('d.m.Y') }}</dd>
                        </dl>

                        @if($category->img)
                            <div style="margin-top:10px;">
                                <label>Aktualne zdjęcie</label><br>
                                <img src="{{ $category->img }}" alt="Zdjęcie kategorii"
                                     style="max-width:100%; max-height:150px; border:1px solid #ddd; padding:4px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('categories._youtube-scripts')

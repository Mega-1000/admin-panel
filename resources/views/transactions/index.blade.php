@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-plus"></i> Transakcje

    </h1>
@endsection

@section('app-content')
    <div class="panel">
        <div class="vue-components panel-body">
            <transactions/>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-plus"></i> Rejestr pracy
    </h1>
@endsection

@section('app-content')
    <div class="panel">
        <div class="vue-components panel-body">
            <working-events-presentation></working-events-presentation>
        </div>
    </div>
@endsection


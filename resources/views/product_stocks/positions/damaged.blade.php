@extends('layouts.datatable')
@section('app-header')
    @livewireStyles
@endsection

@section('table')
    <livewire:product-stock-position-damaged :positionId="$position->id" />

    @livewireScripts
@endsection

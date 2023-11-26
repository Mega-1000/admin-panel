@extends('layouts.datatable')

@section('app-header')
    @livewireStyles
@endsection

@section('table')
    <livewire:order-datatable.order-datatable-index />

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2"></script>
@endsection

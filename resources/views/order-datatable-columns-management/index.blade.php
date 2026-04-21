@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
@livewireStyles
@section('table')
    <livewire:order-datatable-columns-management />

    @livewireScripts
@endsection

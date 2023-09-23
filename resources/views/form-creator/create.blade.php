@extends('layouts.datatable')

<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>
<!-- Include the sortable.js library -->
@livewireStyles
@section('table')
    <livewire:form-creator />
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
@endsection

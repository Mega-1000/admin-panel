@php use App\Enums\CourierName; @endphp
@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_packages.create')
    </h1>
    @livewireStyles
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

    <livewire:newsletter-message-edit-and-add :messageId="$message?->id" />

    @livewireScripts
@endsection

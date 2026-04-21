@extends('layouts.datatable')
@section('app-header')
    <link rel="stylesheet" href="{{ URL::asset('css/views/orders/edit.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/views/orders/return.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h1 class="page-title" style="margin-right: 0px;">
        <i class="glyphicon glyphicon-share-alt"></i> Podgląd Zdjęcia
    </h1>
@endsection

@section('table')
<img src="{{$orderReturn->getImageUrl()}}" style="width: 100%;">
@endsection
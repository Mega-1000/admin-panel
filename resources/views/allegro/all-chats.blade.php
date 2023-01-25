@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> Wszystkie czaty
    </h1>
@endsection

@section('table')
    <div class="allegro-threads-chunks">
        @foreach ($threadsList as $thread)
            <div class="allegro-thread">
                <span class="allegro-thread-id">{{ $thread['allegro_thread_id'] }}</span> | <strong>{{ $thread['allegro_user_login'] }}: </strong>
                <div class="allegro-thread-content">{{ Illuminate\Support\Str::substr($thread['content'], 0, 50) }}...</div>
            </div>
        @endforeach
    </div>
    <div>
        Aktualna Strona: <strong class="number-of-pages">{{ $currentPage }} / {{ $numberOfPages }}</strong>
    </div>
    @if ($currentPage != 1)
        <a href="{{ $prevPageUrl }}" class="btn navigation-prev">Poprzednia</a>
    @endif
    @if ($currentPage < $numberOfPages)
        <a href="{{ $nextPageUrl }}" class="btn navigation-next">Następna</a>
    @endif
@endsection

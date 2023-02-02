@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-character"></i> Dyskusja z {{ $dispute->buyer_login }}: {{ $dispute->subject }}
    </h1>
@endsection

@section('table')

    <ul style="list-style-type: none; padding: 0;">
        @foreach(array_reverse($messages) as $message)
            <li style="padding: 10px; border-radius: 5px; margin-bottom: 10px; border:1px solid #ccc;
                background: {{ $message['author']['role'] == 'BUYER' ? '#eee' : '#efe' }}
                ">
                <b>{{ array_key_exists('login', $message['author']) ? $message['author']['login'] : 'SYSTEM ALLEGRO' }}</b>
                <small>{{ $message['author']['role'] == 'BUYER' ? '(kupujący)' : '' }}</small>:
                <span class="pull-right"><small>{{ (new \Carbon\Carbon($message['createdAt'])) }}</small></span>
                <br>
                {{ array_key_exists('text', $message) ? $message['text'] : '' }}
                <br>
                @if(array_key_exists('attachment', $message))
                    <a target="_blank" href="/admin/disputes/attachment/{{ base64_encode($message['attachment']['url']) }}">{{ $message['attachment']['fileName'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
    @if($dispute->status == 'ONGOING')
    <form method="post" action="/admin/disputes/send/{{ $dispute->id }}">
        {{ csrf_field() }}
        <textarea name="text" class='form-control' name="" id="" cols="30" rows="3"></textarea>
        <button class="btn btn-primary">Wyślij</button>
    </form>
    @endif
    @if ($dispute->is_pending === 1 && auth()->user()->id === $dispute->user_id)
        <a id="exit_dispute" class="btn" href="javascript:;">Wyjdź z dyskusji</a>
    @endif

@endsection


@section('datatable-scripts')
<script>
    $(function() {
        $('#exit_dispute').on('click', async e => {
            $(e.target).addClass('loader-2');

            const url = '/admin/allegro/exitDispute';

            const res = await ajaxPost({}, url);

            if(res.error) toastr.error('Nie udało się wyjść z dyskusji, spróbuj ponownie.');
            if(res.isSuccess) window.location = '/admin/orders';
        });
    });
</script>
@endsection



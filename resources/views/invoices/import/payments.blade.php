@extends('layouts.datatable')

@section('app-header')

@endsection

@section('table')

    <form action="{{ route('invoices.storePaymentsPdf') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="payments-file">
            <div class="form-group">
                <label for="created_at">Data wpłaty</label><br/>
                <input type="date" id="created_at" name="created_at" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
            </div>
            <input type="file" class="payments-file-input" id="payments" name="payments">
        </div>
        <button type="submit" class="btn btn-primary">Wyślij</button>
    </form>
    @if(session()->get('payments') != null)
        <div>
            <h1>Zaimportowano płatności:</h1>
        </div>
        @foreach(session()->get('payments') as $payment)
            @if(array_key_exists('orderId', $payment))
                <p>Zlecenie <span style="color: green;">{{ $payment['orderId'] }}</span> Kwota <b>{{ $payment['amount'] }}</b> Status: @if (array_key_exists('error', $payment)){{ $payment['error']}} @elseif (array_key_exists('info', $payment)) {{ $payment['info'] }} @endif</p>
            @endif
        @endforeach
    @endif

@endsection

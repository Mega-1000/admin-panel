<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
          integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">

</head>
<body>
<div id="app">
    <div class="container" id="flex-container">
        <h1>Potwierdzenie płatności dla zamówienia <span class="order__id">{{ $orderPayment->order->id }}</span></h1>
        <h3>Kwota do zatwierdzenia: <span class="payment__amount">{{ $orderPayment->amount }}</span></h3>
        <form action="{{ route('ordersPayment.warehousePaymentConfirmationStore', ['token' => $token]) }}" method="POST">
            {{ csrf_field() }}
            <input type="submit" class="btn btn-success" value="Potwierdzam płatność" />
            <input type="hidden" value="{{ $orderPayment->id }}" name="orderPaymentId">
        </form>
    </div>
</div>
<style>
    #flex-container {
        text-align: center;
    }
    .order__id {
        color: green;
    }
    .payment__amount {
        color: deepskyblue;
    }
</style>
</body>
</html>

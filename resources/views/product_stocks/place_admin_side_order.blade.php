@extends('layouts.datatable')

@section('head')
    <style>
        .mt-5 {
            margin-top: 1em;
        }
    </style>
@endsection

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.edit')
        <a style="margin-left: 15px;" href="{{ action('ProductStocksController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('product_stocks.list')</span>
        </a>
    </h1>
@endsection

@section('table')
    <a class="btn btn-primary" href="{{ route('product_stocks.edit', ['id' => $productStock->product_id]) }}">
        Powrót
    </a>
    <div class="form-group-default">
        <div>
            <label for="product-name">Ilość dni do tyłu</label>
            <input id="days-back" class="form-control" placeholder="Ilość dni do tyłu">
        </div>
        <div class="mt-5">
            <label for="days-to-future">Ilość dni do przodu</label>
            <input id="days-to-future" class="form-control" placeholder="Ilość dni do przodu">
        </div>
        <div class="mt-5">
            <label for="actual-quantity">Aktualnie w magazynie</label>
            <input value="{{ $productStock->quantity }}" id="actual-quantity" class="form-control" placeholder="Aktualnie w magazynie" disabled>
        </div>
        <div class="mt-5">
            <label for="client-email">Email kliena</label>
            <input value="cenniki@ephpolska.pl" id="client-email" class="form-control" placeholder="Email klienta">
        </div>

        <div id="result-velue"></div>

        <button class="btn btn-primary" id="submit-button">
            Wyślij zamówienie
        </button>
    </div>

@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const submit = async () => {
            const postData = {
                daysBack: document.getElementById('days-back').value,
                daysToFuture: document.getElementById('days-to-future').value,
                clientEmail: document.getElementById('client-email').value
            };

            const product_id = "{{ $productStock->product_id }}";

            try {
                const {data: repsponse} = await axios.post(`/admin/products/stocks/${product_id}/place-admin-order/confirm`, postData);

                Swal.fire({
                    title: 'Sukces',
                    text: 'Zamówienie zostało wysłane',
                    type: 'success',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } catch (e) {
                Swal.fire({
                    title: 'Błąd',
                    text: 'Wystąpił błąd podczas wysyłania zamówienia',
                    type: 'error',
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
            }
        }

        const getData = async () => {
            const requestData = {
                daysBack: document.getElementById('days-back').value,
                daysToFuture: document.getElementById('days-to-future').value
            };

            const product_id = "{{ $productStock->product_id }}";

            const getParams = Object.keys(requestData).map(key => `${key}=${requestData[key]}`).join('&');


            try {
                const {data: repsponse} = await axios.get(`/admin/products/stocks/${product_id}/place-admin-order/calculate?${getParams}`);

                document.getElementById('result-velue').innerHTML = `<div class="mt-5">
                    <label for="result-velue">Wartość zamówienia</label>
                    <input value="${repsponse.orderQuantity}" id="result-velue" class="form-control" placeholder="Wartość zamówienia" disabled>
                </div>`;
            } catch (e) {
                console.log(e);
            }

        }

        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('keyup', getData);
        });

        document.querySelector('#submit-button').addEventListener('click', submit);

    </script>
@endsection

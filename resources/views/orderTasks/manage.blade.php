@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_tasks.manage')
    </h1>
    <style>
        .tags {
            width: 100%;
        }

        .tag {
            width: 50%;
            float: right;
        }
        .wrong-difference {
            color: orange;
        }
        .valid-difference {
            color: black;
        }
        .order__header--id {
            color: red;
            font-weight: bold;
        }
    </style>
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
    <h2>Zlecenie: <span class="order__header--id">{{ $task->order->id }}</span></h2>
    <h4>Waga zlecenia: {{ $task->order->weight }} kg</h4>
    <h4>Przygotowane paczki: </h4>
    @foreach($task->order->packages as $package)
        @php
            $color = '';
            switch($package->status) {
                case 'DELIVERED':
                    $color = '#87D11B';
                 break;
                case 'SENDING':
                    $color = '#4DCFFF';
                break;
                case 'WAITING_FOR_SENDING':
                    $color = '#5537f0';
                break;
            }
        @endphp
    <div style="border: solid green 4px; width: 30%; padding: 10px;" >
        <div style="display: flex; flex-direction: column; align-items: center;" >
            <div style="display: flex; align-items: stretch;">
                <p style="margin: 8px 0px 0px 0px;">{{ $task->order->id }} / {{ $package->id }}</p>
                @if($package->symbol)
                    <p style="margin: 8px 8px 0px 8px;">{{ $package->symbol }}</p> </div>
                @endif
                @switch($package->status)
                    @case('WAITING_FOR_CANCELLED')
                        <p>WYSŁANO DO ANULACJI</p>
                    @break

                    @case('REJECT_CANCELLED')
                        <p style="color:red;">ANULACJA ODRZUCONA</p>
                    @break
                @endswitch
                @if($package->letter_number === null &&
                    $package->tatus !== 'CANCELLED' &&
                    $package->status !== 'WAITING_FOR_CANCELLED' &&
                    $package->delivery_courier_name !== 'GIELDA' &&
                    $package->service_courier_name !== 'GIELDA' &&
                    $package->delivery_courier_name !== 'ODBIOR_OSOBISTY' &&
                    $package->service_courier_name !== 'ODBIOR_OSOBISTY'
                )
                    <div style="display: flex;">
                        <button class="btn btn-success" id="package-{{$package->id}}" onclick="sendPackage('{{$package->id}}','{{ $package->order_id }}')">Wyślij</button>
                        <button class="btn btn-danger" onclick="deletePackage('{{$package->id}}', '{{$package->order_id}}')">Usuń</button>
                        <button class="btn btn-info" onclick="createSimilar('{{$package->id}}', '{{$package->order_id}}')">Podobna</button>
                    </div>
                @else
                    @php
                        if($package->cash_on_delivery !== null && $package->cash_on_delivery > 0) {
                            $cashOnDelivery = '<span>' . $package->cash_on_delivery . 'zł</span>';
                        }
                    @endphp
                    @if($package->service_courier_name === 'INPOST' || $package->service_courier_name === 'ALLEGRO-INPOST')
                            <a target="_blank" href="/storage/inpost/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold; color: #FFFFFF; display: inline-block; margin-top: 5px; margin-left: 5px; padding: 5px;  background-color: {{ $color }}" href="https://inpost.pl/sledzenie-przesylek?number={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                    @endif
                    @switch($package->delivery_courier_name)
                        @case('DPD')
                            <a target="_blank" href="/storage/dpd/protocols/protocol{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                            <a target="_blank" href="/storage/dpd/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px;  background-color: {{ $color }}" href="https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                        @break
                        @case('POCZTEX')
                            <a target="_blank" href="/storage/pocztex/protocols/protocol{{$package->sending_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px;  background-color: {{ $color }}" href="http://www.pocztex.pl/sledzenie-przesylek/?numer={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                        @break
                        @case('JAS')
                            <a target="_blank" href="/storage/jas/protocols/protocol{{$package->sending_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                            <a target="_blank" href="/storage/jas/labels/label{{$package->sending_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        @break
                        @case('GIELDA')
                            <a target="_blank" href="/storage/gielda/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        @break
                        @case('ODBIOR_OSOBISTY')
                            <a target="_blank" href="/storage/odbior_osobisty/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        @break
                        @case('GLS')
                            @php
                              $url = "{{ route('orders.package.getSticker', ['id' => $package->id])}}"
                            @endphp
                            <a target="_blank" href="{{ $url }}">
                                <p>
                                    @if($package->letter_number)
                                        {{ $package->letter_number }}
                                    @else
                                        wygeneruj naklejkę
                                    @endif
                                </p>
                            </a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; padding: 5px; margin-top: 5px;margin-left: 5px; background-color: {{ $color }}" href="https://gls-group.eu/PL/pl/sledzenie-paczek?match={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                        @break
                    @endswitch
                    <div style="display: flex;">
                    <button class="btn btn-danger" onclick="cancelPackage('{{$package->id}}', '{{$package->order_id}}')">Anuluj</button>
                    <button class="btn btn-info" onclick="createSimilar('{{$package->id}}', '{{$package->order_id}}')">Podobna</button>
                    </div>
                @endif
        </div>
    </div>
    @endforeach
    <h4>Wysłane paczki: </h4>
    @foreach($task->order->packages as $package)
        @php
            $isProblem = abs(($package->real_cost_for_company ?? 0) - ($package->cost_for_company ?? 0)) > 2;
        @endphp
        @if($isProblem)
            <div style="border: solid red 4px">
        @endif
        @if($package->status == 'SENDING' || $package->status == 'DELIVERED')
            <div style="display: flex; align-items: center; flex-direction: column;" >
                <div style="display: flex; align-items: center;">
                    <p style="margin: 8px 0px 0px 0px;">{{ $package->order_id }} / {{ $package->number }}</p>
                    @if($package->symbol)
                        <p style="margin: 8px 8px 0px 8px;">{{ $package->symbol }}</p> </div>
                    @else
                        <p style="margin: 8px 8px 0px 8px;">{{ $package->container_type }}</p> </div>
                    @endif
                @if($package->letter_number === null)
                    <a href="javascript:void()">Brak listu przewozowego</a>
                @else
                    @php
                        if($package->cash_on_delivery !== null && $package->cash_on_delivery > 0) {
                            $cashOnDelivery = '<span>' . $package->cash_on_delivery . 'zł</span>';
                        }
                    @endphp
                    @if($package->service_courier_name === 'INPOST' || $package->service_courier_name === 'ALLEGRO-INPOST')
                        <a target="_blank" href="/storage/inpost/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold; color: #FFFFFF; display: inline-block; margin-top: 5px; margin-left: 5px; padding: 5px;" href="https://inpost.pl/sledzenie-przesylek?number={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                    @endif
                    @switch($package->delivery_courier_name)
                        @case('DPD')
                        <a target="_blank" href="/storage/dpd/protocols/protocol{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <a target="_blank" href="/storage/dpd/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px;" href="https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                        @break
                        @case('POCZTEX')
                        <a target="_blank" href="/storage/pocztex/protocols/protocol{{$package->sending_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px;" href="http://www.pocztex.pl/sledzenie-przesylek/?numer={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                        @break
                        @case('JAS')
                        <a target="_blank" href="/storage/jas/protocols/protocol{{$package->sending_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        <a target="_blank" href="/storage/jas/labels/label{{$package->sending_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        @break
                        @case('GIELDA')
                        <a target="_blank" href="/storage/gielda/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        @break
                        @case('ODBIOR_OSOBISTY')
                        <a target="_blank" href="/storage/odbior_osobisty/stickers/sticker{{$package->letter_number}}.pdf"><p>{{$package->letter_number}}</p></a>
                        @break
                        @case('GLS')
                        @php
                            $url = "{{ route('orders.package.getSticker', ['id' => $package->id])}}"
                        @endphp
                        <a target="_blank" href="{{ $url }}">
                            <p>
                                @if($package->letter_number)
                                    {{ $package->letter_number }}
                                @else
                                    wygeneruj naklejkę
                                @endif
                            </p>
                        </a>
                        <div>
                            @if(isset($cashOnDelivery))
                                {{ $cashOnDelivery }}
                            @endif
                            <a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; padding: 5px; margin-top: 5px;margin-left: 5px;" href="https://gls-group.eu/PL/pl/sledzenie-paczek?match={{$package->letter_number}}"><i class="fas fa-shipping-fast"></i></a>
                        </div>
                        @break
                    @endswitch
                @endif
        @endif
            </div>
    @endforeach
    <h4>Przydziel towar do paczek:</h4>
    <draggable-packages></draggable-packages>
    @component('orderTasks.modals.modals', ['templateData' => $templateData])
    @endcomponent
@endsection
@section('scripts')
    <script>
        function createSimilar(id, orderId) {
            let action = "{{ route('order_packages.duplicate',['packageId' => '%id']) }}"
            action = action.replace('%id', id)
            $('#createSimilarPackForm').attr('action', action)
            $('#createSimilarPackage').modal()
        }

        function cancelPackage(id, orderId) {
            if (confirm('Potwierdź anulację paczki')) {
                url = '{{route('order_packages.sendRequestForCancelled', ['id' => '%id'])}}';
                $.ajax({
                    url: url.replace('%id', id),
                }).done(function (data) {
                    urlRefresh = '{{route('orders.index', ['order_id' => 'replace'])}}'
                    window.location.href = urlRefresh.replace('replace', orderId)
                }).fail(function () {
                    alert('Coś poszło nie tak')
                });
            }
        }

        function deletePackage(id, orderId) {
            if (confirm('Potwierdź usunięcię paczki')) {
                url = '{{route('order_packages.destroy', ['id' => '%id'])}}';
                $.ajax({
                    url: url.replace('%id', id),
                    type: 'delete',
                    data: {
                        'redirect': false
                    }
                }).done(function (data) {
                    urlRefresh = '{{route('orders.index', ['order_id' => 'replace'])}}'
                    window.location.href = urlRefresh.replace('replace', orderId)
                }).fail(function () {
                    alert('Coś poszło nie tak')
                });
            }
        }

        function sendPackage(id, orderId) {
            $('#package-' + id).attr("disabled", true);
            $('#order_courier > div > div > div.modal-header > h4 > span').remove();
            $('#order_courier > div > div > div.modal-header > span').remove();
            $.ajax({
                url: '/admin/orders/' + orderId + '/package/' + id + '/send',
            }).done(function (data) {
                $('#order_courier').modal('show');
                if (data.message == 'Kurier zostanie zamówiony w przeciągu kilku minut' || data.message == null) {
                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Kurier zostanie zamówiony w przeciągu kilku minut</span>');
                } else {
                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Jedno z wymaganych pól nie zostało zdefiniowane:</span>');
                    $('#order_courier > div > div > div.modal-header').append('<span style="color:red;">' + data.message.message + '</span><br>');
                }
                $('#package-' + id).attr("disabled", false);
                $('#success-ok').on('click', function () {
                    setTimeout(() => {
                        table.ajax.reload(null, false);
                        window.location.href = '/admin/orders?order_id=' + orderId;
                    }, 500);
                });
            }).fail(function () {
                $('#package-' + id).attr("disabled", false);
                $('#order_courier_problem').modal('show');
                $('#problem-ok').on('click', function () {
                    window.location.href = '/admin/orders?order_id=' + orderId;
                });
            });
        }
    </script>
@endsection

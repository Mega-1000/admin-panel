@php use App\Enums\UserRole; @endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
          integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <link href="{{ asset('css/views/chat/style.css') }}" rel="stylesheet">

</head>
<body>
<div>
    <div class="container" id="flex-container">
        <div id="chat-container">
            <div class="text-center alert alert-info">{!! $title !!}</div>
            @if (!empty($notices))
                <div class="alert-info alert">Uwagi konsultanta: <b>{{ $notices  }}</b></div>
            @endif
            @if (!empty($faq))
                <div class="alert-info alert"><b>FAQ:</b> <br>{!! implode('<br/>',$faq) !!}</div>
            @endif
            @if ($product_list->count() > 0)
                <div class="alert alert-warning"><b>Lista produktów:</b>
                    @foreach($product_list as $product)
                        @include('chat/single_product', ['product' => $product, 'user_type' => $user_type])
                    @endforeach
                </div>
            @endif
            <div class="vue-components">
                @if(!empty($order))
                    <order-dates order-id="{{ $order->id }}" user-type="{{ $user_type }}" chat-id="{{ empty($chat) ? null : $chat->id }}"></order-dates>
                @endif
            </div>
            @if ($chat)
                @include('chat.chat_body')
            @endif
            <form id="new-message" action="{{ $route }}">
                <div class="row">
                    <div class="col-sm-9">
                        <select name="msg_area" id="msg_area">
                            <option value="{{ UserRole::Main }}">Główny</option>
                            <option value="{{ UserRole::Storekeeper }}">Magazyn</option>
                            <option value="{{ UserRole::SuperAdministrator }}">Administrator</option>
                            <option value="{{ UserRole::Consultant }}">Konsultant</option>
                            <option value="{{ UserRole::Accountant }}">Księgowość</option>
                        </select>
                        <textarea required class="form-control" id="message"
                                  style="resize: none; width: 100%; height: 46px;"
                                  placeholder="Tutaj wpisz wiadomość"></textarea>
                    </div>
                    <div class="col-sm-3">
                        <input type="submit" value="Wyślij" class="btn btn-success btn-lg btn-block">
                    </div>
                </div>
            </form>
            @if (is_a(Auth::user(), \App\User::class))
                <button id="call-worker" class="btn bg-primary call-button">Wyślij maila pracownikom</button>
            @else
                <button id="call-mod" class="btn bg-primary call-button">Wezwij moderatora</button>
            @endif
        </div>
        <table id="chat-users">
            <tr>
                <th colspan="2">Uczestnicy:</th>
            </tr>
            @foreach($users as $chatUser)
                <tr>
                    {!! $chatUser->getUserNicknameForChat($user_type) !!}
                    @if( empty($chatUser->user))
                        <th>
                            <button class="btn btn-danger remove-user" value="{{ $chatUser->id }}"
                                    name="{{ get_class($chatUser) }}">Usuń
                            </button>
                        </th>
                    @endif
                </tr>
            @endforeach
            <tr>
                <th colspan="2">Powiązane osoby:</th>
            </tr>
            @foreach($possible_users as $user)
                <tr>
                    @if(is_a($user, \App\Entities\Customer::class))
                        <th class="alert-warning alert">
                    @else
                        <th class="alert-info alert">
                            @endif
                            {!! ChatHelper::formatChatUser($user, $user_type) !!}
                        </th>
                        <th>
                            <button name="{{ get_class($user) }}" class="btn btn-success add-user"
                                    value="{{ $user->id }}">Dodaj
                            </button>
                        </th>
                </tr>
            @endforeach
        </table>

    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ"
        crossorigin="anonymous"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd"
        crossorigin="anonymous"></script>
<script src="{{ asset('js/vue-chunk.js') }}"></script>
<script src="{{ asset('js/vue-scripts.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/helpers/dynamic-calculator.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.panel-default').animate({scrollTop: $('.panel-body').height()});

        $('#message').focus();

        $('#new-message').submit(function (e) {
            e.preventDefault();
            var message = $('#message').val();
            $('#message').val('');
            const area = $('#msg_area').val();

            $.post(
                $(this).attr('action'),
                {
                    area,
                    message,
                },
                function (data) {
                    refreshRate = 1;
                    nextRefresh = 0;
                }
            );
        });

        var nextRefresh = $.now() + 3000;
        var refreshRate = 1;
        var running = false;

        setInterval(getMessages, 500);

        function getMessages() {
            if (running || $.now() < nextRefresh) {
                return;
            }
            running = true;
            refreshRate = refreshRate < 60 ? refreshRate + 1 : 60;

            $.getJSON(
                '{{ $routeRefresh }}',
                {lastId: $('.message-row:last-child').data('messageid')},
                function (data) {
                    if (data.messages.length > 0) {
                        refreshRate = 1;
                        $('.panel-body').append(data.messages);
                        $('.panel-default').animate({scrollTop: $('.panel-body').height()});
                    }
                    nextRefresh = $.now() + refreshRate * 1000;
                    running = false;
                }
            );
        }

        $('.add-user').click((event) => {
            $.ajax({
                method: "POST",
                url: "{{ $routeAddUser }}",
                data: {'user_id': event.target.value, 'type': event.target.name}
            })
                .done(() => location.reload());
        })
        $('.remove-user').click((event) => {
            $.ajax({
                method: "POST",
                url: "{{ $routeRemoveUser }}",
                data: {'user_id': event.target.value, 'type': event.target.name}
            })
                .done(() => location.reload());
        })
        $('#call-mod').click((event) => {
            alert('Moderator został poinformowany')
            $.ajax({
                method: "POST",
                url: "{{ $routeAskForIntervention }}",
                data: {'user_id': event.target.value}
            })
        })
        $('#call-worker').click((event) => {
            alert('Pracownicy zostali poinformowani')
            $.ajax({
                method: "POST",
                url: "{{ $routeAskForIntervention }}",
                data: {'user_id': event.target.value}
            })
        })
    });
</script>
</body>
</html>

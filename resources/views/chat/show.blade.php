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
    <script type="text/javascript" src="{{ URL::asset('js/helpers/helpers.js') }}"></script>
</head>

<body>
    <div>
        <div class="container" id="flex-container">
            <div id="chat-container">
                <div class="text-center alert alert-info">{!! $title !!}</div>
                @if (!empty($notices))
                    <div class="alert-info alert">Uwagi konsultanta: <b>{{ $notices }}</b></div>
                @endif
                @if (!empty($faq))
                    <div class="alert-info alert"><b>FAQ:</b> <br>{!! implode('<br/>', $faq) !!}</div>
                @endif
                @if ($product_list->count() > 0)
                    <div class="alert alert-warning"><b>Lista produktów:</b>
                        @foreach ($product_list as $product)
                            @include('chat/single_product', [
                                'product' => $product,
                                'userType' => $userType,
                            ])
                        @endforeach
                    </div>
                @endif
                <div class="vue-components">
                    @if (!empty($order))
                        <order-dates order-id="{{ $order->id }}" user-type="{{ $userType }}"
                            chat-id="{{ empty($chat) ? null : $chat->id }}"></order-dates>
                    @endif
                </div>
                @if ($chat)
                    @include('chat.chat_body')
                @endif
                <div id="new-message">
                    <div class="row">
                        <div class="col-sm-9">
                            <textarea required class="form-control" id="message"
                                style="width: 100%; max-width: 650px; min-width: 200px; height: 100px;"
                                placeholder="Tutaj wpisz wiadomość"></textarea>
                            <input id="attachment" name="attachment" type="file" style="margin-top: 10px;" />
                        </div>
                        <div class="col-sm-3">
                            <input type="submit" value="Wyślij" class="btn btn-success btn-lg btn-block send-btn" data-action="{{ $route }}">
                        </div>
                    </div>
                </div>
                @if (is_a(Auth::user(), \App\User::class))
                    <button id="call-worker" class="btn bg-primary call-button">Wyślij maila pracownikom</button>
                @else
                    <button id="call-mod" class="btn bg-primary call-button">Wezwij moderatora</button>
                @endif
            </div>
            <div class="chat-right-column" style="padding-left: 10px;">
                <h3>Użytkownicy:</h3>
                <div class="chat-users-wrapper" style="overflow: auto; max-height: 100vh;">
                    <table id="chat-users">
                        @include('chat/users', [
                            'title'            => 'Klienci:',
                            'isEmptyMsg'       => 'Aktualnie w rozmowie nie biorą udziału żadni klienci',
                            'users'            => $chatCustomers,
                            'userType'         => MessagesHelper::TYPE_CUSTOMER,
                            'currentUserType'  => $userType,
                            'arePossibleUsers' => false,
                            'class'            => 'bg-warning alert alert-warning',
                        ])
                        @include('chat/users', [
                            'title'            => 'Pracownicy firm:',
                            'isEmptyMsg'       => 'Aktualnie w rozmowie nie biorą udziału żadni pracownicy firm',
                            'users'            => $chatEmployees,
                            'userType'         => MessagesHelper::TYPE_EMPLOYEE,
                            'currentUserType'  => $userType,
                            'arePossibleUsers' => false,
                            'class'            => 'bg-info alert alert-info',
                        ])
                        @include('chat/users', [
                            'title'            => 'Konsultanci:',
                            'isEmptyMsg'       => 'Aktualnie w rozmowie nie biorą udziału żadni konsultanci',
                            'users'            => $chatConsultants,
                            'userType'         => MessagesHelper::TYPE_USER,
                            'currentUserType'  => $userType,
                            'arePossibleUsers' => false,
                            'class'            => 'bg-primary alert',
                        ])
                        @include('chat/users', [
                            'title'            => 'Powiązani klienci:',
                            'isEmptyMsg'       => 'Brak powiązanych klientów',
                            'users'            => $possibleCustomers,
                            'userType'         => MessagesHelper::TYPE_CUSTOMER,
                            'currentUserType'  => $userType,
                            'arePossibleUsers' => true,
                            'class'            => 'bg-warning alert alert-warning',
                        ])
                        @include('chat/users', [
                            'title'            => 'Powiązani pracownicy firm:',
                            'isEmptyMsg'       => 'Brak powiązanych pracowników firm',
                            'users'            => $possibleEmployees,
                            'userType'         => MessagesHelper::TYPE_EMPLOYEE,
                            'currentUserType'  => $userType,
                            'arePossibleUsers' => true,
                            'class'            => 'bg-info alert alert-info',
                        ])
                    </table>
                </div>
                @if ($userType == MessagesHelper::TYPE_USER)
                    <div class="filters-wrapper">
                        <h3>Filtry:</h3>
                        <label>
                            Obszar:
                            @include('chat/msg_area', ['msgAreaId' => 'area'])
                        </label>
                        @if(!empty($usersHistory))
                            <h3>Pokaż:</h3>
                            @include('chat/history')
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
    </script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/vue-chunk.js') }}"></script>
    <script src="{{ asset('js/vue-scripts.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/helpers/dynamic-calculator.js') }}"></script>
    <script>
        $(document).ready(function() {

            const isConsultant = '{{ $userType == MessagesHelper::TYPE_USER }}';
            
            let usersHistoryFilter = new Set();
            let selectedArea = 0;

            const scrollBottom = () => {
                $('.panel-default').animate({
                    scrollTop: $('.panel-body').height()
                });
            }

            const filterMessages = () => {
                
                if(!isConsultant) return false;

                $('.message-row').each(function() {
                    const chatUserId = String($(this).data('user-id'));
                    const area = String($(this).data('area'));

                    usersHistoryFilter.has(chatUserId) && selectedArea == area ? $(this).show() : $(this).hide();
                });
                scrollBottom();
            }

            const getAllUsers = () => {
                if(!isConsultant) return false;

                $('.filter-users-history').each(function() {
                    usersHistoryFilter.add($(this).val());
                    $(this).prop('checked', true);
                });
            }

            getAllUsers();
            filterMessages();
            
            $('#area').change(function() {
                selectedArea = $(this).val();
                filterMessages();
            });

            $('.show-all').on('click', () => {
                getAllUsers();
                filterMessages();
            });

            $('.filter-users-history').change( function() {
                if($(this).prop('checked') == true) {
                    usersHistoryFilter.add($(this).val());
                } else {
                    usersHistoryFilter.delete($(this).val());
                }
                filterMessages();
            });

            $('#message').focus();

            $('.send-btn').click(async e => {
                e.preventDefault();
                var message = $('#message').val();
                $('#message').val('');

                const url = $(e.target).data('action');
                
                const area = $('#area').val() || 0;
                const attachmentInput = $('#attachment')[0];
                const formData = new FormData();

                if(attachmentInput.files.length > 0) {
                    const file = attachmentInput.files[0];
                    const filename = file.name;

                    formData.append('file', file);
                }

                formData.append('area', area);
                formData.append('message', message);
                console.log(formData.has('file'));
                await ajaxFormData(formData, url);

                refreshRate = 1;
                nextRefresh = 0;
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
                    '{{ $routeRefresh }}', {
                        lastId: $('.message-row:last-child').data('messageid'),
                        area: $('#area').val()
                    },
                    function(data) {
                        if (data.messages.length > 0) {
                            refreshRate = 1;
                            $('.panel-body').append(data.messages);
                            filterMessages();
                            scrollBottom();
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
                        data: {
                            'user_id': event.target.value,
                            'type': event.target.name
                        }
                    })
                    .done(() => location.reload());
            })
            $('.remove-user').click((event) => {
                $.ajax({
                        method: "POST",
                        url: "{{ $routeRemoveUser }}",
                        data: {
                            'user_id': event.target.value,
                            'type': event.target.name
                        }
                    })
                    .done(() => location.reload());
            })
            $('#call-mod').click((event) => {
                alert('Moderator został poinformowany')
                $.ajax({
                    method: "POST",
                    url: "{{ $routeAskForIntervention }}",
                    data: {
                        'user_id': event.target.value
                    }
                })
            })
            $('#call-worker').click((event) => {
                alert('Pracownicy zostali poinformowani')
                $.ajax({
                    method: "POST",
                    url: "{{ $routeAskForIntervention }}",
                    data: {
                        'user_id': event.target.value
                    }
                })
            })
        });
    </script>
</body>

</html>

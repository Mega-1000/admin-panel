<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.chat_name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
        integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <link href="{{ asset('css/views/chat/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ URL::asset('js/helpers/helpers.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
        integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
        .darken-page {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1; /* Ensure this is below your button and instruction z-index */
        }

        .highlight-element {
            position: relative;
            z-index: 2;
        }
    </style>
    <!-- Select2 JS -->
</head>

<body>
    <div>
        <div class="container" id="flex-container">
            <div id="chat-container">
                <div class="text-center alert alert-info">{!! $title !!}</div>
                @if($chat->questions_tree && $userType === MessagesHelper::TYPE_USER)
                    <div class="text-center alert alert-info">
                        Ścieżka FAQ użytkownika:<br>
                        @foreach(json_decode($chat->questions_tree) as $questionData)
                            -> {{ $questionData->question }}<br>
                        @endforeach
                    </div>
                @endif
                @if (!empty($notices))
                    <div class="alert-info alert">Uwagi konsultanta: <b>{{ $notices }}</b></div>
                @endif
                @if (!empty($faq))
                    <div class="alert-info alert"><b>FAQ:</b> <br>{!! implode('<br/>', $faq) !!}</div>
                @endif
                @if ($userType == MessagesHelper::TYPE_EMPLOYEE)
                    <h1>
                        Jeśli chcesz zaaktualizować swoją ofertę dotyczącą przetargu kliknij
                        @php
                            $firm = App\Entities\Firm::whereHas('employees', function ($q) use ($userId) {
                                $q->where('id', $userId);
                            })->first();
                        @endphp
                        <a class="btn btn-primary" target="_blank" href="/auctions/offer/create/{{ $chat->auctions->first()?->firms()->where('firm_id', $firm->id)->first()?->token }}">
                            Kliknij tutaj aby zmienić ceny
                        </a>
                    </h1>
                @endif
                @if($isStyropian)
                    <div class="mb-4 alert alert-warning">
                        @if($chat->auctions->count() === 0)
                            <a href="{{ route('auctions.create', ['chat' => $chat->id]) }}" class="btn btn-primary" target="_blank">
                                Rozpocznij przetarg
                            </a>
                            <div id="auction-instructions" style="display: none; color: white; font-weight: bold; font-size: large; border-radius: 15px; padding: 20px; background-color: #0c0c0c">
                            <p>Poniżej tabela cen brutto produktów z tej oferty których istnieje możliwość dostarczenia na wskazany kod pocztowy.</p>
                                <iframe src="{{ route('displayPreDataPricesTableForOrder', $chat->id) }}" height="600px; width: 100%" width="600"></iframe>
                            <p>Jeśli chcesz poprosić firmy o indywidualną wycenę twojego zapytania naciśnij przycisk rozpocznij przetarg.
                                <br>
                                !!! Jeśli chcesz wykonać inną czynnoś naciśnij przycisk zamknij tą tabelę</p>
                                <button class="btn btn-primary" id="dimiss-info">Zamknij tą tabelę</button>
                                <br>
                                <br>
                                <a href="{{ route('auctions.create', ['chat' => $chat->id]) }}" class="btn btn-primary" target="_blank">
                                    Rozpocznij przetarg
                                </a>
                            </div>
                        @else
                            <!-- if auction->end_of_auction is in past show message  -->
                            <form method="post" action="{{ route('auctions.edit', ['auction' => $chat->auctions()->first()->id]) }}">
                                @csrf
                                @method('PUT')
                                Zakończenie przetargu
                                <input class="form-control" name="end_of_auction" type="datetime-local" value="{{ $chat->auctions()->first()->end_of_auction }}">

                                <div class="mt-4">
                                    Data wysłania przesyłki
                                    <input class="form-control" name="date_of_delivery" type="datetime-local" value="{{ $chat->auctions()->first()->date_of_delivery }}">
                                </div>


                                <button class="btn btn-primary">
                                    Zaaktualizuj daty dotyczące przetargu
                                </button>
                            </form>


                            @if(\Carbon\Carbon::parse(\Carbon\Carbon::now())->gt(\Carbon\Carbon::parse($chat->auctions->first()->end_of_auction)))
                                <h3>
                                Przetarg zakończony
                                </h3>
                                <br>
                            @else
                                <h3>
                                    Aktywny przretarg
                                </h3>
                                <br>
                                Koniec: {{ $chat->auctions->first()->end_of_auction }}
                                <br>
                                Wstępna data wysyłki: {{ $chat->auctions->first()->date_of_delivery }}
                                <br>
                                Cena: {{ $chat->auctions->first()->price }} %
                                <br>
                                Jakość: {{ $chat->auctions->first()->quality }} %
                                <br>
                                Aktywny: {{ $chat->auctions->first()->confirmed ? 'Tak' : 'Nie' }}
                                <br>
                                Uwagi: {{ $chat->auctions->first()->notes }}
                                <br>
                                <form action="{{ route('end-auction.store', $chat->auctions->first()->id) }}" method="post">
                                    @csrf
                                    <button class="btn btn-secondary">
                                        Zakończ przetarg przedwcześnie
                                    </button>
                                </form>
                            @endif
                        @endif
                        @if(!empty($chat->auctions->first()) && !$order->auction_order_placed)
                            <br>
                            <a class="btn btn-primary" href="{{ route('auctions.end', ['auction' => $chat->auctions->first()->id]) }}">
                                Zobacz wyniki przetargu
                            </a>
                            <br>
                        @endif
                        @if($order->auction_order_placed)
                            <h1>
                                Zamówienie zostało złożone i wysłane do fabryki
                            </h1>
                        @endif
                        @if($userType === MessagesHelper::TYPE_USER && $chat->auctions->count() > 0 && $chat->auctions->first()?->confirmed === 0)
                            <form method="post" action="{{ route('auctions.confirm', ['auction' => $chat->auctions->first()->id]) }}">
                                @csrf
                                <button class="btn btn-success">
                                    Rozpocznij przetarg
                                </button>
                            </form>
                        @endif
                    </div>
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
                    @php($order = $chat->order)
                    @include('dates')
                </div>
                @if ($chat)
                    <h2>
                        Chat magazyn-konsultant-klient
                    </h2>
                    @include('chat.chat_body')
                @endif
                <div id="new-message" class="loader-2" style="position: relative;">
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
                @if ($userType == MessagesHelper::TYPE_USER)
                    <button id="call-worker" class="btn bg-primary call-button">Wyślij maila pracownikom</button>
                @else
                    <button id="call-mod" class="btn bg-primary call-button">Wezwij moderatora</button>
                @endif
            </div>
            <div class="chat-right-column" style="padding-left: 10px;">
                <img id="bell-icon" onclick="askForPermision" src="/svg/bell-icon.svg" alt="" style="width: 35px; cursor: pointer">
                <form action="{{ route('addUsersFromCompanyToAuction', $chat->id) }}" method="POST">
                    @csrf
                    <h2>
                        Dodaj firmę do przetargu
                    </h2>

                    <input type="text" class="form-control" name="firm_symbol" placeholder="Wpisz symbol firmy" list="suggestions">

                    <button type="submit" class="btn btn-primary">
                        Dodaj firm
                    </button>
                </form>
                @if($chat->complaint_form)
                    <button id="show_complaint_form" data-complaint-form="{{ $chat->complaint_form }}" class="btn bg-primary call-button">Pokaż formularz reklamacyjny</button>
                @endif
                <h3>Zarządzanie użytkownikami chatu:</h3>
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
                        @if($userType == MessagesHelper::TYPE_USER)
                            @include('chat/users', [
                                'title'            => 'Pracownicy firm uczestniczących w przetargu:',
                                'isEmptyMsg'       => 'Brak powiązanych pracowników firm',
                                'users'            => $allEmployeesFromRelatedOrders ?? new \Illuminate\Support\Collection(),
                                'userType'         => MessagesHelper::TYPE_EMPLOYEE,
                                'currentUserType'  => $userType,
                                'arePossibleUsers' => true,
                                'class'            => 'bg-info alert alert-info',
                            ])
                        @endif

                        @if($userType == MessagesHelper::TYPE_USER)
                            <form action="{{ route('addUsersFromCompanyToChat', $chat->id) }}" method="POST">
                                @csrf

                                <h2>
                                    Dodaj firmę do chata
                                </h2>

                                <input type="text" class="form-control" name="firm_symbol" placeholder="Wpisz symbol firmy" list="suggestions">
                                <datalist id="suggestions">
                                    @foreach(\App\Entities\Firm::all() as $firm)
                                        <option value="{{ $firm->symbol }}">{{ $firm->symbol }}</option>
                                    @endforeach
                                </datalist>

                                <button type="submit" class="btn btn-primary"> <!-- Use type="submit" to submit the form -->
                                    Dodaj użytkowników
                                </button>
                            </form>
    {{--                        @endif--}}
                        @endif
                    </table>
                </div>
                @if ($userType == MessagesHelper::TYPE_USER)
                    <div class="filters-wrapper">
                        <h3>Filtry:</h3>
                        <label>
                            Obszar:
                            @include('chat/msg_area')
                        </label>
                        @if(!empty($usersHistory))
                            <h3>Filtruj pokazywanie wiadomości po użytkownikach:</h3>
                            @include('chat/history')
                        @endif
                        @if( $chat->complaint_form !== '' && $firmWithComplaintEmails->isNotEmpty() )
                            <div>
                                <br>
                                <h4>Reklamacje:</h4>
                                <select id="complaint_email" style="padding: 5px; margin: 10px 0;">
                                    @foreach ($firmWithComplaintEmails as $firm)
                                        <option value="{{ $firm->complaint_email }}">{{ $firm->name }}</option>
                                    @endforeach
                                </select>
                                <button id="call_complaint" class="btn bg-primary">Napisz do firmy z reklamacją</button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".select2").select2();
        });
    </script>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
    </script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script src="{{ asset('js/vue-chunk.js') }}"></script>
    <script src="{{ asset('js/vue-scripts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/libs/blink-title.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/helpers/dynamic-calculator.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
        integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    ></script>
    <script>
        // const askForPermision = () => {
        //     Notification.requestPermission().then((permission) => {
        //         if (permission !== "granted") {
        //             const browser = navigator.userAgent.toLowerCase();
        //
        //             if (browser.indexOf('chrome') > -1) {
        //                 window.location.href = 'https://support.google.com/chrome/answer/3220216?co=GENIE.Platform%3DDesktop&hl=pl';
        //             } else if (browser.indexOf('firefox') > -1) {
        //                 window.location.href = 'https://support.mozilla.org/pl/kb/powiadomienia-web-push-firefox';
        //             } else if (browser.indexOf('safari') > -1) {
        //                 window.location.href = 'https://support.apple.com/pl-pl/guide/safari/sfri40734/mac';
        //             } else if (browser.indexOf('opera') > -1) {
        //                 window.location.href = 'https://help.opera.com/pl/latest/web-preferences/';
        //             } else if (browser.indexOf('edge') > -1) {
        //                 window.location.href = 'https://support.microsoft.com/pl-pl/microsoft-edge/zarz%C4%85dzanie-powiadomieniami-witryn-internetowych-w-przegl%C4%85darce-microsoft-edge-0c555609-5bf2-479d-a59d-fb30a0b80b2b';
        //             } else {
        //                 alert('Nie udało się wykryć przeglądarki');
        //             }
        //
        //             return false;
        //         }
        //
        //         document.querySelector('#bell-icon').src = '/svg/bell-icon.svg';
        //         document.querySelector('#bell-icon').addEventListener('click', () => {
        //             alert('Powiadomienia są włączone');
        //         });
        //     });
        // }

        // Notification.requestPermission().then((permission) => {
        //     if (permission !== "granted") {
        //         const bellIcon = document.getElementById('bell-icon');
        //         bellIcon.addEventListener('click', () => {
        //             askForPermision();
        //         });
        //
        //         bellIcon.src = '/svg/bell-red-icon.svg';
        //
        //         alert('Prosimy o włączenie powiadomień w przeglądarce');
        //         alert('Kliknij w inkonę dzwonka, aby dowiedzieć się więcej');
        //
        //         return false;
        //     }
        //
        //     document.querySelector('#bell-icon').addEventListener('click', () => {
        //         alert('Powiadomienia są włączone');
        //     });
        // });

            $(document).ready(function() {
                $(function () {
                    // console.log($("select").selectize());
                });

            $('#new-message').removeClass('loader-2');

            const isConsultant = '{{ $userType == MessagesHelper::TYPE_USER }}';

            const storagePath = '{{ asset("storage") }}';
            const documentTitle = document.title;

            let usersHistoryFilter = new Set();
            let selectedArea = 0;

            const scrollBottom = () => {
                $('.panel-default').animate({
                    scrollTop: $('.chat-panel').height()
                });
            }

            const filterMessages = () => {

                if(!isConsultant) return false;

                $('.message-row').each(function() {
                    const chatUserId = String($(this).data('user-id'));
                    const area = String($(this).data('area'));
                    const selectedArea = $('#area').val();

                    if((chatUserId && chatUserId != '{{ $chatBlankUser?->id }}') || selectedArea != 0) {
                        usersHistoryFilter.has(chatUserId) && selectedArea == area ? $(this).show() : $(this).hide();
                    }
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

            var nextRefresh = $.now() + 3000;
            var refreshRate = 1;
            var running = false;

            $('#message').focus();

            $('.send-btn').click(async e => {
                e.preventDefault();
                running = true;
                $('#new-message').addClass('loader-2');
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

                const res = await ajaxFormData(formData, url);

                if(res) {
                    $('.chat-panel').append(res);
                    scrollBottom();
                }
                $('#new-message').removeClass('loader-2');
                $('#attachment').val('');
                refreshRate = 1;
                nextRefresh = 0;
                running = false;
            });

            setInterval(getMessages, 500);
            scrollBottom();

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
                        if (data?.messages?.length > 0) {
                            refreshRate = 1;
                            if(data.messages != '' && document.hidden) {
                                blinkTitle({
                                    title: documentTitle,
                                    message: "!!! NOWA WIADOMOŚĆ !!!",
                                    delay: 900,
                                    notifyOffPage: true
                                });

                                const notification = new Notification("!!! NOWA WIADOMOŚĆ !!!", {
                                    body: documentTitle,
                                    icon: "{{ asset('images/logo.png') }}"
                                });
                            }
                            $('.chat-panel').append(data.messages);
                            filterMessages();
                            scrollBottom();
                        }
                        nextRefresh = $.now() + refreshRate * 1000;
                        running = false;
                    }
                );
            }

            $(window).on('focus', () => {
                blinkTitleStop();
                document.title = documentTitle;
            } );


            window.onunload = function () {
                $.ajax({
                    method: "POST",
                    url: "{{ $routeCloseChat }}",
                })
            };

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
            $('#call_complaint').click((event) => {
                alert('Reklamacja zostanie wysłana na podany adres email');
                $.ajax({
                    method: "POST",
                    url: "{{ $routeCallComplaint }}",
                    data: {
                        'email': $('#complaint_email').val()
                    }
                }).done(() => location.reload());
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
            $('#show_complaint_form').click((e) => {
                const complaintForm = $(e.target).data('complaint-form');
                const complaintFormTemplate = `
                    <div>Imię: ${complaintForm.firstname}</div>
                    <div>Nazwisko: ${complaintForm.surname}</div>
                    <div>Telefon: ${complaintForm.phone}</div>
                    <div>Email: ${complaintForm.email}</div>
                    <div>Przyczyna: ${complaintForm.reason}</div>
                    <div>Opis: ${complaintForm.description}</div>
                    <div>Wartość produktu: ${complaintForm?.productValue}</div>
                    <div>Wartość uszkodzonych produktów: ${complaintForm?.damagedProductsValue}</div>
                    <div>Numer konta: ${complaintForm?.accountNumber}</div>
                    <div>Data: ${complaintForm.date}</div>
                    <div>Numer kontaktowy do kierowcy: ${complaintForm?.driverPhone}</div>
                    <div>Numer śledzenia: ${complaintForm?.trackingNumber}</div>
                    <div>Imię osoby obsługującej reklamacje ${complaintForm?.nameOfPersonHandlingTheComplaint}</div>
                    <div>Nazwisko osoby obsługującej reklamacje ${complaintForm?.surnameOfPersonHandlingTheComplaint}</div>
                    <div>Telefon osoby obsługującej reklamacje ${complaintForm?.phoneOfPersonHandlingTheComplaint}</div>
                    <div>Email osoby obsługującej reklamacje ${complaintForm?.emailOfPersonHandlingTheComplaint}</div>
                    <div>Propozycja rozwiązania ${complaintForm?.proposalOfTheClientsClaimOrSolutionToTheTopic}</div>
                    <div>Załącznik:
                        <a class="attachment-path" style="display: block; margin-top: 10px; color: #000;"
                            href="${storagePath}/${complaintForm.image}" target="_blank" download="${complaintForm.image_name}">
                            załącznik: ${complaintForm.image_name}
                        </a>
                    </div>
                `;
                let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
                width=700,height=450,left=100,top=100`;

                const complaintWindow = window.open('about:blank', '', params);
                complaintWindow.document.body.innerHTML = complaintFormTemplate;
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

    <script>
        $(document).ready(function() {
            const params = new URLSearchParams(window.location.search);
            const showAuctionInstructions = params.get('showAuctionInstructions') === 'true' && {{ $order->chat->auctions()->first() ? 'false' : 'true' }};

            if (showAuctionInstructions) {
                // Show the instructions
                $('#auction-instructions').show();

                // Highlight the start auction button
                $('#start-auction').addClass('highlight-element');

                // Darken the rest of the page
                $('body').append('<div class="darken-page"></div>');

                // Ensure the button and instructions are above the darkened background
                $('#auction-instructions, #start-auction').addClass('highlight-element');

                $('#dimiss-info').on('click', function() {
                    $('#auction-instructions').hide();
                    $('.darken-page').remove();
                });
            }
        });
    </script>
    <script>
        loadOrderDates();

        const updateDates = async () => {
            const orderId = $('#orderId').val();
            const dateType = $('#dateType').val(); // 'shipment' or 'delivery'
            const dateFrom = dateType === 'shipment' ? $('#dateFrom').val() : null;
            const dateTo = dateType === 'shipment' ? $('#dateTo').val() : null;
            const deliveryDateFrom = dateType === 'delivery' ? $('#dateFrom').val() : null;
            const deliveryDateTo = dateType === 'delivery' ? $('#dateTo').val() : null;

            try {
                const result = await updateDatesSend({
                    orderId: {{ $order->id }},
                    type: window.type11,
                    shipmentDateFrom: dateFrom,
                    shipmentDateTo: dateTo,
                    deliveryDateFrom: deliveryDateFrom,
                    deliveryDateTo: deliveryDateTo,
                });

                $('#modifyDateModal').modal('hide');
                showAlert('success', 'PomyPomyślnie zaaktualizowano daty.');
                loadOrderDates(); // Refresh dates table
            } catch (error) {
                console.error('Failed to modify the date:', error);
                showAlert('danger', 'Failed to modify the date.');
            }
        };
        const updateDatesSend = (params) => {
            return fetch('/api/orders/' + params.orderId + '/updateDates', {
                method: 'PUT',
                credentials: 'same-origin',
                headers: new Headers({
                    'Content-Type': 'application/json; charset=utf-8',
                    'X-Requested-Width': 'XMLHttpRequest'
                }),
                body: JSON.stringify({
                    type: params.type,
                    shipmentDateFrom: params.shipmentDateFrom,
                    shipmentDateTo: params.shipmentDateTo,
                    deliveryDateFrom: params.deliveryDateFrom,
                    deliveryDateTo: params.deliveryDateTo
                })
            }).then((response) => {
                return response.json()
            })
        }

        function showAlert(type, message) {
            const alertHtml = '<div class="alert alert-' + type + '">' + message + '</div>';
            $('#alerts').html(alertHtml);
            setTimeout(function() {
                $('#alerts').html('');
            }, 3000);
        }

        function loadOrderDates() {
            $.ajax({
                url: '/api/orders/{{ $order->id }}/getDates', // Adjust this URL to your API endpoint
                type: 'GET',
                credentials: 'same-origin',
                success: function(data) {
                    if (data) {
                        populateDatesTable(data);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Failed to load order dates.');
                }
            });
        }

        function modifyOrderDate(orderId, dateType, dateFrom, dateTo, type) {
            $.ajax({
                url: '/api/orders/' + orderId + '/dates/modify', // Adjust this URL to your API endpoint
                type: 'POST',
                credentials: 'same-origin',
                data: {
                    dateType: dateType,
                    dateFrom: dateFrom,
                    dateTo: dateTo
                },
                success: function(data) {
                    $('#modifyDateModal').modal('hide');
                    showAlert('success', 'PomyPomyślnie zaaktualizowano daty.');
                    loadOrderDates(); // Refresh dates table
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Failed to modify the date.');
                }
            });
        }

        function populateDatesTable(dates) {
            let html = '';
            Object.keys(dates).forEach(function(key) {
                const date = dates[key]; // Get the date object for the current key

                if (key === 'acceptance') {
                    return;
                }

                const isConsultant = '{{ $userType == MessagesHelper::TYPE_USER }}'; // For consultant
                const isCustomer = '{{ $userType == MessagesHelper::TYPE_CUSTOMER }}'; // For customer
                const isWarehouse = '{{ $userType == MessagesHelper::TYPE_EMPLOYEE }}'; // For warehouse
                const isAccepted = {{ $order?->date_accepted ?? 'false' }};
                window.userType = '{{ $userType }}';

                // get full name of userType in Polish
                if (window.userType === 'c') {
                    window.userType = 'klient';
                } else if (window.userType === 'u') {
                    window.userType = 'konsultant';
                } else if (window.userType === 'e') {
                    window.userType = 'magazyn';
                }

                // Determine if the user can modify the date
                let canModify = false;
                if (isCustomer && key === 'customer') {
                    canModify = true;
                }

                if (isConsultant && key === 'consultant') {
                    canModify = true;
                }

                if (isWarehouse && key === 'warehouse') {
                    canModify = true;
                }
                // Determine if the user can accept the date (new functionality)
                let canAccept = false;
                if ((isCustomer && key === 'warehouse') || (isWarehouse && key === 'customer')) {
                    canAccept = true;
                }

                // there have to be at least one date to accept
                if (!date.delivery_date_from && !date.shipment_date_from && !date.delivery_date_to && !date.shipment_date_to) {
                    canAccept = false;
                }

                if (isAccepted) {
                    canAccept = false;
                    // canModify = false;
                    // $('#dates-table').before('<div class="alert alert-info">Daty zostały finalnie zatwierdzone i nie ma możliwości ich modyfikacji</div>');
                }
                let displayKey = '';

                if (key === 'consultant') {
                    displayKey = 'Konsultant'
                }

                if (key === 'customer') {
                    displayKey = 'Klient'
                }

                if (key === 'warehouse') {
                    displayKey = 'Magazyn'
                }

                html += '<tr>' +
                    '<td>Proponowana data wysyłki (dla styropianów dostawy) (' + displayKey + ')</td>' +
                    '<td>' + (date.shipment_date_from || 'N/A') + '</td>' +
                    '<td>' + (date.shipment_date_to || 'N/A') + '</td>' +
                    (canModify ? '<td><div class="btn btn-primary btn-sm" onclick="showModifyDateModal(\'\', \'shipment\', \'' + (date.shipment_date_from || '') + '\', \'' + (date.shipment_date_to || '') + '\', \'' + key + '\')">Modyfikuj</div></td>' : '') +
                    (canAccept ? '<td><div class="btn btn-success btn-sm" onclick="acceptDate(\'shipment\', \'' + key + '\')">Akceptuj</div></td>' : '') +
                    '</tr>';
            });
            $('#datesTable tbody').html(html);
        }

        // Add a new function for accepting dates
        window.acceptDate = function(dateType, key) {
            return fetch('/api/orders/' + {{ $order->id }} + '/acceptDates', {
                method: 'PUT',
                credentials: 'same-origin',
                headers: new Headers({
                    'Content-Type': 'application/json; charset=utf-8',
                    'X-Requested-Width': 'XMLHttpRequest'
                }),
                body: JSON.stringify({
                    type: key,
                    userType: window.userType
                })
            }).then((response) => {
                window.location.reload();
            })
        }

        window.showModifyDateModal = function(orderId, type, from, to, type11) {
            $('#orderId').val(orderId);
            $('#dateType').val(type);
            $('#dateFrom').val(from);
            $('#dateTo').val(to);
            window.type11 = type11;
            $('#modifyDateModal').modal('show');
        }

        function showAlert(type, message) {
            const alertHtml = '<div class="alert alert-' + type + '">' + message + '</div>';
            $('#alerts').html(alertHtml);
            setTimeout(function() {
                $('#alerts').html('');
            }, 3000);
        }
    </script>
</body>

</html>
``

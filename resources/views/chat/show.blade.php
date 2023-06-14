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
                @if($isStyropian)
                    <div class="mb-4 alert alert-warning">
                        @if($chat->auctions->count() === 0)
                            <a href="{{ route('auctions.create', ['chat' => $chat->id]) }}" class="btn btn-primary" target="_blank">
                                Rozpocznij przetarg
                            </a>
                        @else
                            <!-- if auction->end_of_auction is in past show message  -->
                            <form method="post" action="{{ route('auctions.edit', ['auction' => $chat->auctions()->first()->id]) }}">
                                @csrf
                                @method('PUT')
                                <input class="form-control" name="end_of_auction" type="date" value="{{ $chat->auctions()->first()->end_of_auction }}">

                                <button class="btn btn-primary">
                                    Zmień datę zakończenia przetargu
                                </button>
                            </form>


                            @if(\Carbon\Carbon::parse(\Carbon\Carbon::now())->gt(\Carbon\Carbon::parse($chat->auctions->first()->end_of_auction)))
                                <h3>
                                Przetarg zakończony
                                </h3>
                                <br>
                                <a class="btn btn-primary" href="{{ route('auctions.end', ['auction' => $chat->auctions->first()->id]) }}">
                                    Zobacz wyniki przetargu
                                </a>
                            @else
                                <h3>
                                    Aktywny przretarg
                                </h3>
                                <br>
                                Koniec: {{ $chat->auctions->first()->end_of_auction }}
                                <br>
                                data do wysyłki: {{ $chat->auctions->first()->date_of_delivery }}
                                <br>
                                Cena: {{ $chat->auctions->first()->price }} %
                                <br>
                                Jakość: {{ $chat->auctions->first()->quality }} %
                                <br>
                                Aktywny: {{ $chat->auctions->first()->confirmed ? 'Tak' : 'Nie' }}
                            @endif
                        @endif
                        @if(!empty($chat->auctions->first()))
                            <a class="btn btn-primary" href="{{ route('auctions.end', ['auction' => $chat->auctions->first()->id]) }}">
                                Zobacz wyniki przetargu
                            </a>
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
                    @if (!empty($order))
                        <order-dates order-id="{{ $order->id }}" user-type="{{ $userType }}"
                            chat-id="{{ empty($chat) ? null : $chat->id }}"></order-dates>
                    @endif
                </div>
                @if ($chat)
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
                @if($chat->complaint_form)
                    <button id="show_complaint_form" data-complaint-form="{{ $chat->complaint_form }}" class="btn bg-primary call-button">Pokaż formularz reklamacyjny</button>
                @endif
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
                        @if($chat->auctions()->count() > 0)
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
                            <h3>Pokaż:</h3>
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
    <script type="text/javascript" src="{{ asset('js/libs/blink-title.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/helpers/dynamic-calculator.js') }}"></script>
    <script>
        const askForPermision = () => {
            Notification.requestPermission().then((permission) => {
                if (permission !== "granted") {
                    const browser = navigator.userAgent.toLowerCase();

                    if (browser.indexOf('chrome') > -1) {
                        window.location.href = 'https://support.google.com/chrome/answer/3220216?co=GENIE.Platform%3DDesktop&hl=pl';
                    } else if (browser.indexOf('firefox') > -1) {
                        window.location.href = 'https://support.mozilla.org/pl/kb/powiadomienia-web-push-firefox';
                    } else if (browser.indexOf('safari') > -1) {
                        window.location.href = 'https://support.apple.com/pl-pl/guide/safari/sfri40734/mac';
                    } else if (browser.indexOf('opera') > -1) {
                        window.location.href = 'https://help.opera.com/pl/latest/web-preferences/';
                    } else if (browser.indexOf('edge') > -1) {
                        window.location.href = 'https://support.microsoft.com/pl-pl/microsoft-edge/zarz%C4%85dzanie-powiadomieniami-witryn-internetowych-w-przegl%C4%85darce-microsoft-edge-0c555609-5bf2-479d-a59d-fb30a0b80b2b';
                    } else {
                        alert('Nie udało się wykryć przeglądarki');
                    }

                    return false;
                }

                document.querySelector('#bell-icon').src = '/svg/bell-icon.svg';
                document.querySelector('#bell-icon').addEventListener('click', () => {
                    alert('Powiadomienia są włączone');
                });
            });
        }

        Notification.requestPermission().then((permission) => {
            if (permission !== "granted") {
                const bellIcon = document.getElementById('bell-icon');
                bellIcon.addEventListener('click', () => {
                    askForPermision();
                });

                bellIcon.src = '/svg/bell-red-icon.svg';

                alert('Prosimy o włączenie powiadomień w przeglądarce');
                alert('Kliknij w inkonę dzwonka, aby dowiedzieć się więcej');

                return false;
            }

            document.querySelector('#bell-icon').addEventListener('click', () => {
                alert('Powiadomienia są włączone');
            });
        });

        $(document).ready(function() {
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
</body>

</html>

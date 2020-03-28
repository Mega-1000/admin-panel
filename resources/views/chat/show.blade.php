<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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

</head>
<body>
    <div id="app">
        <div class="container" style="margin-top: 20px">
            <div class="text-center alert alert-info">{{ $title }}</div>
            <div class="panel panel-default" style="max-height: calc(100vh - 200px); overflow-y: scroll">
                <div class="panel-body">
                    @if ($chat)
                        @for ($i = 0; $i < 3; $i++)
                        @foreach ($chat->messages as $message)
                            @include ('chat/single_message', ['message' => $message])
                        @endforeach
                        @endfor
                    @endif
                </div>
            </div>
            <form action="{{ $route }}">
                <div class="row">
                    <div class="col-sm-9">
                        <textarea class="form-control" id="message" style="resize: none; width: 100%; height: 46px;"></textarea>
                    </div>
                    <div class="col-sm-3">
                        <input type="submit" value="Wyślij" class="btn btn-success btn-lg btn-block">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('.panel-default').animate({ scrollTop: $('.panel-body').height() });

            $('form').submit(function(e) {
                e.preventDefault();
                var message = $('#message').val();
                $('#message').val('');

                $.post(
                    $(this).attr('action'),
                    { message: message },
                    function(data) { refreshRate = 1; nextRefresh = 0; }
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
                    { lastId: $('.message-row:last-child').data('messageid') },
                    function (data) {
                        if (data.messages.length > 0) {
                            refreshRate = 1;
                            $('.panel-body').append(data.messages);
                            $('.panel-default').animate({ scrollTop: $('.panel-body').height() });
                        }
                        nextRefresh = $.now() + refreshRate * 1000;
                        running = false;
                    }
                );
            }
        });
    </script>
</body>
</html>

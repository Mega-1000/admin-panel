<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive iframes with Fullscreen</title>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
          integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        .iframe-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 10px;
            height: 100vh;
            box-sizing: border-box;
        }
        .iframe-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            border: 2px solid #ccc;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .iframe-wrapper::before {
            content: 'Double-click to expand';
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 5px;
            z-index: 10;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .iframe-wrapper:fullscreen {
            padding: 0;
            width: 100vw;
            height: 100vh;
        }
        .iframe-wrapper:fullscreen::before {
            content: 'Press Esc to exit fullscreen';
        }
    </style>
</head>
<body>


<div class="iframe-container">
    <div class="iframe-wrapper">
        <iframe src="{{ route('orders.edit', $order->id) }}"></iframe>
    </div>
    @if($order->chat->auctions()->first())
        <div class="iframe-wrapper">
            <iframe src="/auctions/{{ $order->chat->auctions()->first()->id }}/end"></iframe>
        </div>
    @endif
    <div class="iframe-wrapper">
        <iframe src="/admin/orders/{{ $order->id }}/get-basket"></iframe>
    </div>
    @php
        $messagesHelper = new App\Helpers\MessagesHelper();
        $order['id'] = $order['id'] ?? 0;
        $messagesHelper->chatId = \App\Entities\Order::find($order['id'])?->chat?->id;
        $token = $messagesHelper->getChatToken($order['id'], auth()->id());
    @endphp
    <div class="iframe-wrapper">
        <iframe src="/chat/{{ $token }}"></iframe>
    </div>

    <div>
        <hr>
        @foreach($order->customer->orders as $o)
            <div style="display: flex; gap: 10px">
                <a href="{{ route('orders.edit', $o->id) }}" style="font-size: 20px">{{ $o->id }}</a>

                @foreach($o->labels as $label)
                    <span
                        onclick="removeLabel({{ $o->id}}, {{ $label->id }}, {{ $label->manual_label_selection_to_add_after_removal ?? 'null' }}, 'null', {{$label->timed ? 'true' : 'false'}})"
                        class="label-wrapper"
                        style="cursor: pointer"
                    >
                        <i class="{{ $label->icon_name }}" style="font-size: 30px; background-color: {{ $label->color }}; color: #ffffff; padding: 10px;"></i>
                        <div class="label-popup"></div>
                    </span>
                @endforeach
            </div>
            <hr>
        @endforeach
    </div>

    <div style="font-size: larger">
        Wykonaj telefon do klienta na numer: {{ $order->customer->standardAddress()->phone }}
        <br>
        <form action="/admin/add-additional-info/{{ $order->id }}" method="POST">
            @csrf
            Dodatkowe informacje
            <input type="text" name="notices" class="form-control">
            <br>
            Następny kontakt
            <input type="datetime-local" name="next_contact_date" class="form-control">
            <br>
            <input type="checkbox" name="sendEmail">
            Wyślij informacje na e-mail
            <br>
            <input type="submit" name="normal" value="Klient porzebuje jeszcze czasu" class="btn btn-primary">
            <input type="submit" name="notAnswered" value="Nie odebrano telefonu" class="btn btn-danger">
            <input type="submit" name="clientClosed" value="Klient zdecydowany" class="btn btn-success">
            <input type="submit" name="notAbleToProcess" value="Nie jestem w stanie obsłużyć tego zamówienia" class="btn btn-danger">
        </form>
        <hr>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const iframeWrappers = document.querySelectorAll('.iframe-wrapper');

        iframeWrappers.forEach((wrapper) => {
            wrapper.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    if (wrapper.requestFullscreen) {
                        wrapper.requestFullscreen();
                    } else if (wrapper.mozRequestFullScreen) { // Firefox
                        wrapper.mozRequestFullScreen();
                    } else if (wrapper.webkitRequestFullscreen) { // Chrome, Safari and Opera
                        wrapper.webkitRequestFullscreen();
                    } else if (wrapper.msRequestFullscreen) { // IE/Edge
                        wrapper.msRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) { // Firefox
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) { // IE/Edge
                        document.msExitFullscreen();
                    }
                }
            });
        });
    });
</script>
</body>
</html>

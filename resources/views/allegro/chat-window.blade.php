<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Allegro Chat App</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            body {
                overflow: hidden;
            }
            * {
                box-sizing: border-box;
            }
        </style>
        <link href="{{ asset('css/chat-styles.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/main.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('admin/voyager-assets?path=js%2Fapp.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/modules/AllegroChat.js') }}"></script>
        <script>
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const ajaxPath = this.location.pathname == '/admin' ? '/admin/' : '/admin/';

                let chatWindowParams = window.localStorage.getItem('preview_allegro_chat_storage');

                if(chatWindowParams) {
                    chatWindowParams = JSON.parse(chatWindowParams);
                    
                    const allegroChatInitializer = new AllegroChat(
                        ajaxPath,
                        chatWindowParams.threadId,
                        chatWindowParams.messages,
                        chatWindowParams.nickname,
                        chatWindowParams.isPreview
                    );
                }
            });
        </script>
    </head>
    <body>
        
    </body>
</html>

<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
</head>
<div class="container">
    <div class="col-8 offset-2">
        <div class="order-messages" id="order-messages">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    @foreach($messages as $message)
                        @switch($message->type)
                            @case('GENERAL')
                                @if($message->user_id == null)
                                    <div class="alert alert-info ml-5">
                                @else
                                    <div class="alert alert-info mr-5">
                                @endif
                            @break

                            @case('SHIPPING')
                                @if($message->user_id == null)
                                    <div class="alert alert-success ml-5">
                                @else
                                    <div class="alert alert-success mr-5">
                            @endif
                            @break

                            @case('WAREHOUSE')
                                @if($message->user_id == null)
                                    <div class="alert alert-warning ml-5">
                                @else
                                    <div class="alert alert-warning mr-5">
                                @endif
                            @break

                            @case('COMPLAINT')
                                @if($message->user_id == null)
                                    <div class="alert alert-danger ml-5">
                                @else
                                    <div class="alert alert-danger mr-5">
                                @endif
                            @break
                        @endswitch
                            <h4>
                                @if($message->source == "MAIL")
                                    [MAIL] -
                                @endif
                                @if($message->user_id == null)
                                    [KLIENT]
                                @else
                                    [KONSULTANT]
                                @endif
                                <b style="font-size: 10px; float: right;">{{ $message->created_at }}</b><br/>
                                <b style="font-size: 10px; float: right;">@lang('order_messages.types.'.$message->type)</b>
                            </h4>
                            @if(count($message->attachments))
                                <p><span class="icon voyager-images" style="margin-right: 5px;"></span>
                                    <style type="text/css">
                                        span.order-message-attachment-link:not(:last-child):after {
                                            content: " | ";
                                        }
                                    </style>
                                    Załączniki:
                                    @foreach($message->attachments as $attachment)
                                        <span class="order-message-attachment-link"><a
                                                    style="color: white;"
                                                    href="{{asset('storage/attachments/' . $attachment->message->order_id . '/' . $attachment->order_message_id . '/' . $attachment->file)}}"
                                                    target="_blank">{{$attachment->file}}</a></span>
                                    @endforeach
                                </p>
                            @endif
                            <h4 style="margin-top: 10px;">{{ $message->title }}</h4>
                            <p>@if($message->additional_description) <span style="font-weight: bolder;">Opis reklamacji:</span>  @endif {{ $message->message }}
                            </p>
                            @if($message->additional_description)
                                <p>
                                    <span style="font-weight: bolder;">Opis roszczenia reklamacyjnego:</span> {{ $message->additional_description }}
                                </p>
                            @endif
                        </div>
                    @endforeach


                                        <form method="POST" action="{{ route('storeWarehouseMessage') }}"
                                              enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <label for="title">Tytuł</label>
                                                <input type="text" name="title" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="type">Typ</label>
                                                <select name="type" id="type" class="form-control">
                                                    <option value="GENERAL">Ogólne</option>
                                                    <option value="SHIPPING">Spedycja</option>
                                                    <option value="WAREHOUSE">Magazyn</option>
                                                    <option value="COMPLAINT">Reklamacja</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="employee_id" value="{{ Auth::id() }}">
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <div class="form-group">
                                                <label for="message">Wiadomość</label>
                                                <textarea name="message" id="message" class="form-control"
                                                          rows="10"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="attachment">Załącznik</label>
                                                <input type="file" name="attachment" id="attachment">
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Wyślij</button>
                                            </div>
                                            <input type="hidden" name="isUser" value="1">
                                        </form>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('javascript')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ URL::asset('js/ckeditor.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('#start_date').datepicker({dateFormat: 'yy-mm-dd'});
            $('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
            $('.default-date-time-picker-now').datetimepicker({
                sideBySide: true,
                format: "YYYY-MM-DD H:mm"
            });
            $('.default-date-picker-now').datetimepicker({
                format: "YYYY-MM-DD",
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/locale/pl.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>
    @yield('scripts')
@endsection
</html>
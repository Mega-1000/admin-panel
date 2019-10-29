<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>

    <script>
        $( document ).ready(function() {
            $('input#crm-data').on('click', function () {
                console.log('test');
                $('#whichData').val(0);
            });

            $('input#subiekt-data').on('click', function () {
                console.log('test2');
                $('#whichData').val(1);
            });
        });
    </script>
</head>
<form action="{{ route('confirmation') }}" method="POST">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="row">
            <div class="col-4 mt-4">
                <div class="order-messages" id="order-messages">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h2>Dane w zleceniu</h2>
                            <div class="form-group">
                                <label for="firstname">Imię</label>
                                <input type="text" class="form-control" name="crm-firstname" value="{{ $order->getInvoiceAddress()->firstname }}">
                            </div>
                            <div class="form-group">
                                <label for="lastname">Nazwisko</label>
                                <input type="text" class="form-control" name="crm-lastname" value="{{ $order->getInvoiceAddress()->lastname }}">
                            </div>
                            <div class="form-group">
                                <label for="firmname">Nazwa firmy</label>
                                <input type="text" class="form-control" name="crm-firmname" value="{{ $order->getInvoiceAddress()->firmname }}">
                            </div>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control" name="crm-nip" value="{{ $order->getInvoiceAddress()->nip }}">
                            </div>
                            <div class="form-group">
                                <label for="phone">Numer telefonu</label>
                                <input type="text" class="form-control" name="crm-phone" value="{{ $order->getInvoiceAddress()->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Ulica</label>
                                <input type="text" class="form-control" name="crm-address" value="{{ $order->getInvoiceAddress()->address }}">
                            </div>
                            <div class="form-group">
                                <label for="flat_number">Numer domu/mieszkania</label>
                                <input type="text" class="form-control" name="crm-flat-number" value="{{ $order->getInvoiceAddress()->flat_number }}">
                            </div>
                            <div class="form-group">
                                <label for="city">Miasto</label>
                                <input type="text" class="form-control" name="crm-city" value="{{ $order->getInvoiceAddress()->city }}">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Kod pocztowy</label>
                                <input type="text" class="form-control" name="crm-postal-code" value="{{ $order->getInvoiceAddress()->postal_code }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" name="crm-email" value="{{ $order->getInvoiceAddress()->email }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 mt-4">
                <div class="order-messages" id="order-messages">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h2>Dane w systemie księgowym</h2>
                            <div class="form-group">
                                <label for="firstname">Imię</label>
                                <input type="text" class="form-control" name="subiekt-firstname" value="{{ $subiektAddress->firstname }}">
                            </div>
                            <div class="form-group">
                                <label for="lastname">Nazwisko</label>
                                <input type="text" class="form-control" name="subiekt-lastname" value="{{ $subiektAddress->lastname }}">
                            </div>
                            <div class="form-group">
                                <label for="firmname">Nazwa firmy</label>
                                <input type="text" class="form-control" name="subiekt-firmname" value="{{ $subiektAddress->firmname }}">
                            </div>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control" name="subiekt-nip" value="{{ $subiektAddress->nip }}">
                            </div>
                            <div class="form-group">
                                <label for="phone">Numer telefonu</label>
                                <input type="text" class="form-control" name="subiekt-phone" value="{{ $subiektAddress->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Ulica</label>
                                <input type="text" class="form-control" name="subiekt-address" value="{{ $subiektAddress->address }}">
                            </div>
                            <div class="form-group">
                                <label for="flat_number">Numer domu/mieszkania</label>
                                <input type="text" class="form-control" name="subiekt-flat-number" value="{{ $subiektAddress->flat_number }}">
                            </div>
                            <div class="form-group">
                                <label for="city">Miasto</label>
                                <input type="text" class="form-control" name="subiekt-city" value="{{ $subiektAddress->city }}">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Kod pocztowy</label>
                                <input type="text" class="form-control" name="subiekt-postal-code" value="{{ $subiektAddress->postal_code }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" name="subiekt-email" value="{{ $subiektAddress->email }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 mt-4">
                <div class="order-messages" id="order-messages">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h2>Potwierdzenie danych</h2>
                            <h4>Prosimy o wybranie danych, na które ma zostać wystawiona faktura. Dane można własnoręcznie poprawić, a następnie proszę wybrać kategorię z poprawionymi danymi.</h4>
                            <p>Jednocześnie informujemy, iż po zatwierdzeniu danych, będą one niezmienne, a dokonanie zmiany będzie się wiązało z opłatą 50 zł.</p>
                            <input type="submit" class="btn btn-success" value="Wybierz dane ze zlecenia" id="crm-data">
                            <input type="submit" class="btn btn-info" value="Wybierz dane z systemu księgowego" id="subiekt-data">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="same-data" name="same-data">
                                <label class="form-check-label" for="same-data">Zaznacz jeśli chcesz aby nowe zlecenia zostały wystawione na wybrane dane.</label>
                            </div>
                            <input type="hidden" name="whichData" id="whichData" value="0">
                            <input type="hidden" name="orderId" value="{{ $order->id }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
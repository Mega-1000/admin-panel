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
            if($('input[name="crm-firmname"]').val() != '') {
                $('input[name="crm-firstname"]').hide();
                $('input[name="crm-lastname"]').hide();
            }
            $('input#subiekt-data').on('click', function () {
                console.log('test2');
                $('#whichData').val(0);
                $('input[name="crm-firstname"]').val($('input[name="subiekt-firstname"]').val())
                $('input[name="crm-lastname"]').val($('input[name="subiekt-lastname"]').val())
                $('input[name="crm-firmname"]').val($('input[name="subiekt-firmname"]').val())
                $('input[name="crm-nip"]').val($('input[name="subiekt-nip"]').val())
                $('input[name="crm-phone"]').val($('input[name="subiekt-phone"]').val())
                $('input[name="crm-address"]').val($('input[name="subiekt-address"]').val())
                $('input[name="crm-flat-number"]').val($('input[name="subiekt-flat-number"]').val())
                $('input[name="crm-city"]').val($('input[name="subiekt-city"]').val())
                $('input[name="crm-postal-code"]').val($('input[name="subiekt-postal-code"]').val())
                $('input[name="crm-email"]').val($('input[name="subiekt-email"]').val())

            });
            $('input[name="crm-firmname"]').on('change', function() {
                console.log($('input[name="crm-firmname"]').val());
		if($('input[name="crm-firmname"]').val() == '') {
		    
                    $('input[name="crm-firstname"]').prop('required',true);
                    $('input[name="crm-lastname"]').prop('required',true);
                    $('input[name="crm-nip"]').prop('required',false);
		    $('.firstname-div').show();
                    $('.lastname-div').show();
                } else {
                    $('input[name="crm-firstname"]').val('');
                    $('input[name="crm-lastname"]').val('');
                    $('.firstname-div').hide();
                    $('.lastname-div').hide();
                }
            });
            $('#autocomplete').on('click', function() {
                let nip = $('input[name="crm-nip"]').val().replace(/-/g, '');
                $.get("/api/company-info/by-nip/" + nip, function(data) {
                    if (data.error === undefined) {
                        if(data.type == 'JDG') {
                            console.log(data);
                            if(data.fiz_nazwa != '') {
                                $('input[name="crm-firstname"]').val('');
                                $('input[name="crm-lastname"]').val('');
                                $('.firstname-div').hide();
                                $('.lastname-div').hide();
                            }
                            $('input[name="crm-firmname"]').val(data.fiz_nazwa);
                            $('input[name="crm-address"]').val(data.fiz_adSiedzUlica_Nazwa);
                            $('input[name="crm-flat-number"]').val(data.fiz_adSiedzNumerNieruchomosci + '/' +data.fiz_adSiedzNumerLokalu);
                            $('input[name="crm-postal-code"]').val(data.fiz_adSiedzKodPocztowy.slice(0, 2) + "-" + data.fiz_adSiedzKodPocztowy.slice(2));
                            $('input[name="crm-city"]').val(data.fiz_adSiedzMiejscowosc_Nazwa);
                        } else {
                            if(data.praw_nazwa != '') {
                                $('input[name="crm-firstname"]').val('');
                                $('input[name="crm-lastname"]').val('');
                                $('.firstname-div').hide();
                                $('.lastname-div').hide();
                            }
                            $('input[name="crm-firmname"]').val(data.praw_nazwa);
                            $('input[name="crm-address"]').val(data.praw_adSiedzUlica_Nazwa);
                            $('input[name="crm-flat-number"]').val(data.praw_adSiedzNumerNieruchomosci + '/' + data.praw_adSiedzNumerLokalu);
                            $('input[name="crm-postal-code"]').val(data.praw_adSiedzKodPocztowy.slice(0, 2) + "-" + data.praw_adSiedzKodPocztowy.slice(2));
                            $('input[name="crm-city"]').val(data.praw_adSiedzMiejscowoscPoczty_Nazwa);
                        }
                    }
                });

            });
        });
    </script>
</head>
<form action="{{ route('confirmation') }}" method="POST">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mt-4">
                <div class="order-messages" id="order-messages">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h2>Potwierdzenie danych</h2>
                            <p>Na ten moment system chce wystawic panstwa fakture na dane ktore sa zawarte w kolumnie o nazwie "dane na ktore system wystawi fakture"</p>
                            <p>Dane te mozna samodzielenie zmienic dokonujac korekty w odpowiednich polach. </p>
                            <p>Jednoczesnie obok w kolumnie macie dane ktore widnieja w naszym systemie ksiegowym i jezeli chcecie aby na nie wystawic fakture prosimy wcisnac przycisk WYSTAW FAKTURE NA DANE Z SYSTEMU KSIEGOWEGO</p>
                            <p>Jezeli system ma pobrac autmoatycznie dane firmowe z GUS-u to prosimy wypelnic NIP i wcisnac "AUTOUZUPELNIJ"</p>
                            <input type="hidden" name="whichData" id="whichData" value="0">
                            <input type="hidden" name="orderId" value="{{ $order->id }}">
                        </div>
                    </div>
                </div>
            </div>
	    <hr/>
            <div class="col-12 col-md-6 mt-4">
                <div class="order-messages" id="order-messages">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h4>Dane, na które zostanie wystawiona faktura</h4>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="get-firm-data-from-gus">Pobierz dane z GUS</label>
                                    <button
                                            type="button"
                                            class="btn btn-success"
                                            id="autocomplete"
                                    >
                                        Autouzupełnij
                                    </button>
                                </div>
                                <div class="form-group col-md-8">
                                    <label for="crm-nip">NIP</label>
                                    <input type="text" class="form-control" name="crm-nip" value="{{ $order->getInvoiceAddress()->nip }}">
                                </div>
                            </div>
                            <div class="form-group firstname-div">
                                <label for="firstname">Imię</label>
                                <input type="text" class="form-control" name="crm-firstname" value="{{ $order->getInvoiceAddress()->firstname }}">
                            </div>
                            <div class="form-group lastname-div">
                                <label for="lastname">Nazwisko</label>
                                <input type="text" class="form-control" name="crm-lastname" value="{{ $order->getInvoiceAddress()->lastname }}">
                            </div>
                            <div class="form-group">
                                <label for="firmname">Nazwa firmy</label>
                                <input type="text" class="form-control" name="crm-firmname" value="{{ $order->getInvoiceAddress()->firmname }}">
                            </div>
                            <div class="form-group">
                                <label for="phone">Numer telefonu</label>
                                <input type="text" class="form-control" required name="crm-phone" value="{{ $order->getInvoiceAddress()->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Ulica</label>
                                <input type="text" class="form-control" required name="crm-address" value="{{ $order->getInvoiceAddress()->address }}">
                            </div>
                            <div class="form-group">
                                <label for="flat_number">Numer domu/mieszkania</label>
                                <input type="text" class="form-control" required name="crm-flat-number" value="{{ $order->getInvoiceAddress()->flat_number }}">
                            </div>
                            <div class="form-group">
                                <label for="city">Miasto</label>
                                <input type="text" class="form-control" required name="crm-city" value="{{ $order->getInvoiceAddress()->city }}">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Kod pocztowy</label>
                                <input type="text" class="form-control" required name="crm-postal-code" value="{{ $order->getInvoiceAddress()->postal_code }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" required name="crm-email" value="{{ $order->getInvoiceAddress()->email }}">
                            </div>
                            <input type="submit" value="Wyślij" class="btn btn-success">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-4">
                <div class="order-messages" id="order-messages">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <input type="button" class="btn btn-info" value="Wystaw fakturę na dane z systemu księgowego" id="subiekt-data" style="margin-top: -40px;">
                            <h4>Dane w systemie księgowym</h4>
                            <div class="form-group">
                                <label for="firstname">Imię</label>
                                <input type="text" class="form-control" name="subiekt-firstname" value="{{ $subiektAddress->firstname }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="lastname">Nazwisko</label>
                                <input type="text" class="form-control" name="subiekt-lastname" value="{{ $subiektAddress->lastname }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="firmname">Nazwa firmy</label>
                                <input type="text" class="form-control" name="subiekt-firmname" value="{{ $subiektAddress->firmname }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control" name="subiekt-nip" value="{{ $subiektAddress->nip }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="phone">Numer telefonu</label>
                                <input type="text" class="form-control" name="subiekt-phone" value="{{ $subiektAddress->phone }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="address">Ulica</label>
                                <input type="text" class="form-control" name="subiekt-address" value="{{ $subiektAddress->address }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="flat_number">Numer domu/mieszkania</label>
                                <input type="text" class="form-control" name="subiekt-flat-number" value="{{ $subiektAddress->flat_number }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="city">Miasto</label>
                                <input type="text" class="form-control" name="subiekt-city" value="{{ $subiektAddress->city }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Kod pocztowy</label>
                                <input type="text" class="form-control" name="subiekt-postal-code" value="{{ $subiektAddress->postal_code }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" name="subiekt-email" value="{{ $subiektAddress->email }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" value="Wyślij" class="btn btn-success">
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

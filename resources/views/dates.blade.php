<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
<link href="{{ asset('css/views/chat/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/main.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ URL::asset('js/helpers/helpers.js') }}"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css" integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<div class="mt-5" >
    <h2>Zarządzanie datami do zamówienia</h2>
    <div id="alerts"></div>
    <table class="table" id="datesTable">
        <thead>
        <tr>
            <th>Typ daty</th>
            <th>Od</th>
            <th>Do</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>
        <!-- Rows will be populated by JavaScript -->
        </tbody>
    </table>
</div>
<!-- Modify Date Modal -->
<div class="modal fade" id="modifyDateModal" tabindex="-1" role="dialog" aria-labelledby="modifyDateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifyDateModalLabel">Modyfikuj daty</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modifyDateForm">
                    <div class="form-group hidden">
                        <label for="dateType">Typ daty</label>
                        <select form="modifyDateForm" class="form-control" id="dateType">
                            <option value="shipment">Wysyłka</option>
                            <option value="delivery">Dostawa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateFrom">Od</label>
                        <input form="modifyDateForm" type="datetime-local" value="{{ now()->setTime(00, 00) }}" class="form-control" id="dateFrom" required>
                    </div>
                    <div class="form-group">
                        <label for="dateTo">Do</label>
                        <input form="modifyDateForm" type="datetime-local" value="{{ now()->setTime(23, 59) }}" class="form-control" id="dateTo" required>
                    </div>
                    <input form="modifyDateForm" type="hidden" id="orderId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <div type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</div>
                <div type="button" class="btn btn-primary" id="saveDateChanges" onclick="updateDates(); event.preventDefault();">Zapisz zmiany</div>
            </div>
        </div>
    </div>
</div>

<div id="loadingScreen" style="display:none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; justify-content: center; align-items: center;">
    <div style="padding: 20px; background: white; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.5);">
        Zapisywanie dat, proszę czekać...
    </div>
</div>


<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

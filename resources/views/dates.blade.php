<<div class="container mt-5 p-4 border rounded bg-light shadow-sm">
    <h2 class="mb-4 text-center">Zarządzanie datami do zamówienia</h2>
    <div id="alerts"></div>
    <table class="table table-bordered" id="datesTable">
        <thead class="thead-dark">
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
    <div class="text-right mt-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modifyDateModal">Dodaj nową datę</button>
    </div>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                <button type="button" class="btn btn-primary" id="saveDateChanges" onclick="updateDates(); event.preventDefault();">Zapisz zmiany</button>
            </div>
        </div>
    </div>
</div>

<div id="loadingScreen" style="display:none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; display: flex; justify-content: center; align-items: center;">
    <div style="padding: 20px; background: white; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.5);">
        Zapisywanie dat, proszę czekać...
    </div>
</div>

<style>
    .container {
        max-width: 900px;
        margin: auto;
    }
    .modal-header, .modal-footer {
        border: none;
    }
    .modal-body {
        padding: 2rem;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s, border-color 0.3s;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
    .modal-title {
        font-weight: bold;
    }
</style>


<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

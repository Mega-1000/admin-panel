<div class="container mt-5" >
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
                    <div class="form-group">
                        <label for="dateType">Typ daty</label>
                        <select form="modifyDateForm" class="form-control" id="dateType">
                            <option value="shipment">Wysyłka</option>
                            <option value="delivery">Dostawa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateFrom">Od</label>
                        <input form="modifyDateForm" type="date" class="form-control" id="dateFrom" required>
                    </div>
                    <div class="form-group">
                        <label for="dateTo">Do</label>
                        <input form="modifyDateForm" type="date" class="form-control" id="dateTo" required>
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

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

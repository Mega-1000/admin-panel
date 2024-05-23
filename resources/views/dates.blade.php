<div class="container mt-5">
    <h1 class="text-center mb-4">Harmonogram dostawy</h1>
    <div id="alerts"></div>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#shipmentTab">Wysyłka</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#deliveryTab">Dostawa</a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="shipmentTab" class="tab-pane fade show active">
            <div class="table-responsive">
                <table class="table table-striped" id="shipmentTable">
                    <thead>
                    <tr>
                        <th>Od</th>
                        <th>Do</th>
                        <th class="text-center">Akcje</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <div id="deliveryTab" class="tab-pane fade">
            <div class="table-responsive">
                <table class="table table-striped" id="deliveryTable">
                    <thead>
                    <tr>
                        <th>Od</th>
                        <th>Do</th>
                        <th class="text-center">Akcje</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
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
                    <div class="form-group">
                        <label for="dateFrom">Od</label>
                        <div class="input-group date" id="datepickerFrom" data-target-input="nearest">
                            <input form="modifyDateForm" type="text" value="{{ now()->setTime(00, 00)->format('Y-m-d H:i') }}" class="form-control datetimepicker-input" id="dateFrom" data-target="#datepickerFrom" required>
                            <div class="input-group-append" data-target="#datepickerFrom" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dateTo">Do</label>
                        <div class="input-group date" id="datepickerTo" data-target-input="nearest">
                            <input form="modifyDateForm" type="text" value="{{ now()->setTime(23, 59)->format('Y-m-d H:i') }}" class="form-control datetimepicker-input" id="dateTo" data-target="#datepickerTo" required>
                            <div class="input-group-append" data-target="#datepickerTo" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <input form="modifyDateForm" type="hidden" id="orderId" value="">
                    <input form="modifyDateForm" type="hidden" id="dateType" value="">
                </form>
            </div>
            <div class="modal-footer">
                <div type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</div>
                <div type="button" class="btn btn-primary" id="saveDateChanges" onclick="updateDates(); event.preventDefault();">Zapisz zmiany</div>
            </div>
        </div>
    </div>
</div>

<div id="loadingScreen" class="d-flex justify-content-center align-items-center" style="display:none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050;">
    <div class="p-4 bg-white rounded shadow">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="sr-only">Ładowanie...</span>
        </div>
        <h4>Zapisywanie dat, proszę czekać...</h4>
    </div>
</div>


<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

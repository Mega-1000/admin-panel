
<!-- Styles -->
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

<script src="{{ asset('js/app.js') }}"></script>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
</script>
<script src="/js/jquery-ui.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script src="{{ asset('js/vue-chunk.js') }}"></script>
<script src="{{ asset('js/vue-scripts.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/libs/blink-title.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/helpers/dynamic-calculator.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
    integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

<script>
    const showAlert = (type, message) => {
        const alertHtml = '<div class="alert alert-' + type + '">' + message + '</div>';
        $('#alerts').html(alertHtml);
        setTimeout(function() {
            $('#alerts').html('');
        }, 3000);
    };

    window.showModifyDateModal = function(orderId, type, from, to, type11) {
        $('#orderId').val(orderId);
        $('#dateType').val(type);

        const today = new Date();
        const defaultTo = new Date(today);
        defaultTo.setDate(defaultTo.getDate() + 2);

        if (!to) {
            to = defaultTo.toISOString().slice(0, 16);
        }
        if (!from) {
            from = today.toISOString().slice(0, 16);
        }

        $('#dateFrom').val(from);
        $('#dateTo').val(to);

        const dateTo = new Date(to);
        if (dateTo <= defaultTo) {
            showAlert('danger', 'Domyślna data końcowa jest mniej niż dwa dni od teraz.');
        }

        window.type11 = type11;
        $('#modifyDateModal').modal('show');
    };

    const updateDates = async () => {
        const orderId = $('#orderId').val();
        const dateType = $('#dateType').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        $('#loadingScreen').show();

        try {
            const dateFromObj = new Date(dateFrom);
            const dateToObj = new Date(dateTo);

            const tenHoursInMillis = 10 * 60 * 60 * 1000;
            if ((dateToObj - dateFromObj) < tenHoursInMillis) {
                showAlert('danger', 'Data początkowa musi być co najmniej 10 godzin wcześniejsza niż data końcowa.');
                $('#modifyDateModal').modal('hide');
                return;
            }

            const result = await updateDatesSend({
                orderId: {{ $order->id }},
                type: window.type11,
                shipmentDateFrom: dateFrom,
                shipmentDateTo: dateTo,
                deliveryDateFrom: null,
                deliveryDateTo: null,
            });

            $('#modifyDateModal').modal('hide');
            showAlert('success', 'Pomyślnie zapisano daty!');
            loadOrderDates();
        } catch (error) {
            console.error('Failed to modify the date:', error);
            showAlert('danger', 'Failed to modify the date.');
        } finally {
            $('#loadingScreen').hide();
        }
    };

    const updateDatesSend = (params) => {
        return fetch('/api/orders/' + params.orderId + '/updateDates', {
            method: 'PUT',
            credentials: 'same-origin',
            headers: new Headers({
                'Content-Type': 'application/json; charset=utf-8',
                'X-Requested-Width': 'XMLHttpRequest'
            }),
            body: JSON.stringify({
                type: params.type,
                shipmentDateFrom: params.shipmentDateFrom,
                shipmentDateTo: params.shipmentDateTo,
                deliveryDateFrom: params.deliveryDateFrom,
                deliveryDateTo: params.deliveryDateTo
            })
        }).then((response) => {
            return response.json();
        });
    };

    function loadOrderDates() {
        $.ajax({
            url: '/api/orders/{{ $order->id }}/getDates',
            type: 'GET',
            credentials: 'same-origin',
            success: function(data) {
                if (data) {
                    populateDatesTable(data);
                }
            },
            error: function(xhr, status, error) {
                showAlert('danger', 'Failed to load order dates.');
            }
        });
    }

    function populateDatesTable(dates) {
        let html = '';
        Object.keys(dates).forEach(function(key) {
            const date = dates[key];
            let displayKey = '';

            if (key === 'consultant') {
                displayKey = 'Konsultant';
            } else if (key === 'customer') {
                displayKey = 'Klient';
            } else if (key === 'warehouse') {
                displayKey = 'Magazyn';
            }

            html += '<tr>' +
                '<td>Proponowana data dostawy (' + displayKey + ')</td>' +
                '<td>' + (date.shipment_date_from || 'N/A') + '</td>' +
                '<td>' + (date.shipment_date_to || 'N/A') + '</td>' +
                '<td><div class="btn btn-primary btn-sm" onclick="showModifyDateModal(\'\', \'shipment\', \'' + (date.shipment_date_from || '') + '\', \'' + (date.shipment_date_to || '') + '\', \'' + key + '\')">Modyfikuj</div></td>' +
                '</tr>';
        });
        $('#datesTable tbody').html(html);
    }
</script>

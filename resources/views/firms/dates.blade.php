<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

<script>
    loadOrderDates();

    const updateDates = async () => {
        const orderId = $('#orderId').val();
        const dateType = $('#dateType').val();
        const dateFrom = dateType === 'shipment' ? $('#dateFrom').val() : null;
        const dateTo = dateType === 'shipment' ? $('#dateTo').val() : null;
        const deliveryDateFrom = dateType === 'delivery' ? $('#dateFrom').val() : null;
        const deliveryDateTo = dateType === 'delivery' ? $('#dateTo').val() : null;

// Show loading screen
        $('#loadingScreen').show();

        try {
// Convert date strings to Date objects for comparison
            const dateFromObj = new Date(dateFrom);
            const dateToObj = new Date(dateTo);

// Check if dateFrom is at least 10 hours less than dateTo
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
                deliveryDateFrom: deliveryDateFrom,
                deliveryDateTo: deliveryDateTo,
            });

            $('#modifyDateModal').modal('hide');
            showAlert('success', 'Pomyślnie zapisano daty!');
            loadOrderDates(); // Refresh dates table
        } catch (error) {
            console.error('Failed to modify the date:', error);
            showAlert('danger', 'Failed to modify the date.');
        } finally {
// Hide loading screen
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
            return response.json()
        })
    }

    function showAlert(type, message) {
        const alertHtml = '<div class="alert alert-' + type + '">' + message + '</div>';
        $('#alerts').html(alertHtml);
        setTimeout(function() {
            $('#alerts').html('');
        }, 3000);
    }

    function loadOrderDates() {
        $.ajax({
            url: '/api/orders/{{ $order->id }}/getDates', // Adjust this URL to your API endpoint
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

    function modifyOrderDate(orderId, dateType, dateFrom, dateTo, type) {
        $.ajax({
            url: '/api/orders/' + orderId + '/dates/modify', // Adjust this URL to your API endpoint
            type: 'POST',
            credentials: 'same-origin',
            data: {
                dateType: dateType,
                dateFrom: dateFrom,
                dateTo: dateTo
            },
            success: function(data) {
                $('#modifyDateModal').modal('hide');
                showAlert('success', 'Pomyślnie zaktualizowano daty.');
                loadOrderDates(); // Refresh dates table
            },
            error: function(xhr, status, error) {
                showAlert('danger', 'Nie udało się zmodyfikować daty.');
            }
        });
    }


    function populateDatesTable(dates) {
        let html = '';
        Object.keys(dates).forEach(function(key) {
            const date = dates[key]; // Get the date object for the current key

            if (key === 'acceptance') {
                return;
            }

            @php
                $userType = 'c';
                $isStyropian = true;
            @endphp

            const isConsultant = '{{ $userType == MessagesHelper::TYPE_USER }}'; // For consultant
            const isCustomer = '{{ $userType == MessagesHelper::TYPE_CUSTOMER }}'; // For customer
            const isWarehouse = '{{ $userType == MessagesHelper::TYPE_EMPLOYEE }}'; // For warehouse
            const isAccepted = {{ $order?->date_accepted ?? 'false' }};
            window.userType = '{{ $userType }}';

// get full name of userType in Polish
            if (window.userType === 'c') {
                window.userType = 'klient';
            } else if (window.userType === 'u') {
                window.userType = 'konsultant';
            } else if (window.userType === 'e') {
                window.userType = 'magazyn';
            }

// Determine if the user can modify the date
            let canModify = false;
            if (isCustomer && key === 'customer') {
                canModify = true;
            }

            if (isConsultant) {
                canModify = true;
            }

            if (isWarehouse && key === 'warehouse') {
                canModify = true;
            }
// Determine if the user can accept the date (new functionality)
            let canAccept = false;
            if ((isCustomer && key === 'warehouse') || (isWarehouse && key === 'customer')) {
                canAccept = true;
            }

// there have to be at least one date to accept
            if (!date.delivery_date_from && !date.shipment_date_from && !date.delivery_date_to && !date.shipment_date_to) {
                canAccept = false;
            }

            if (isAccepted) {
                canAccept = false;
// canModify = false;
// $('#dates-table').before('<div class="alert alert-info">Daty zostały finalnie zatwierdzone i nie ma możliwości ich modyfikacji</div>');
            }
            let displayKey = '';

            if (key === 'consultant') {
                displayKey = 'Konsultant'
            }

            if (key === 'customer') {
                displayKey = 'Klient'
            }

            if (key === 'warehouse') {
                displayKey = 'Magazyn'
            }

            @if ($isStyropian)
                html += '<tr>' +
                '<td>Proponowana data dostawy (' + displayKey + ')</td>' +
                '<td>' + (date.shipment_date_from || 'N/A') + '</td>' +
                '<td>' + (date.shipment_date_to || 'N/A') + '</td>' +
                (canModify ? '<td><div class="btn btn-primary btn-sm" onclick="showModifyDateModal(\'\', \'shipment\', \'' + (date.shipment_date_from || '') + '\', \'' + (date.shipment_date_to || '') + '\', \'' + key + '\')">Modyfikuj</div></td>' : '') +
                (canAccept ? '<td><div class="btn btn-success btn-sm" onclick="acceptDate(\'shipment\', \'' + key + '\')">Akceptuj</div></td>' : '') +
                '</tr>';
            @else
                html += '<tr>' +
                '<td>Proponowana data wysyłki (' + displayKey + ')</td>' +
                '<td>' + (date.shipment_date_from || 'N/A') + '</td>' +
                '<td>' + (date.shipment_date_to || 'N/A') + '</td>' +
                (canModify ? '<td><div class="btn btn-primary btn-sm" onclick="showModifyDateModal(\'\', \'shipment\', \'' + (date.shipment_date_from || '') + '\', \'' + (date.shipment_date_to || '') + '\', \'' + key + '\')">Modyfikuj</div></td>' : '') +
                (canAccept ? '<td><div class="btn btn-success btn-sm" onclick="acceptDate(\'shipment\', \'' + key + '\')">Akceptuj</div></td>' : '') +
                '</tr>';
            @endif
        });
        $('#datesTable tbody').html(html);
    }

    // Add a new function for accepting dates
    window.acceptDate = function(dateType, key) {
// Show loading screen
        $('#loadingScreen').show();

        return fetch('/api/orders/' + {{ $order->id }} + '/acceptDates', {
            method: 'PUT',
            headers: new Headers({
                'Content-Type': 'application/json; charset=utf-8',
                'X-Requested-Width': 'XMLHttpRequest'
            }),
            body: JSON.stringify({
                type: key,
                userType: window.userType
            })
        }).then((response) => {
            window.location.reload();
        }).catch((error) => {
            console.error('Error accepting the date:', error);
            showAlert('danger', 'Failed to accept the date.');
        }).finally(() => {
// Hide loading screen
            $('#loadingScreen').hide();
        });
    }

    window.showModifyDateModal = function(orderId, type, from, to, type11) {
        $('#orderId').val(orderId);
        $('#dateType').val(type);

        const today = new Date();
        if (!to) {
            to = today.toISOString().slice(0, 11) + "23:59"
        }
        if (!from) {
            from = today.toISOString().slice(0, 11) + "00:00"
        }

        $('#dateFrom').val(from);
        $('#dateTo').val(to);

        window.type11 = type11;
        $('#modifyDateModal').modal('show');
    }

    function showAlert(type, message) {
        const alertHtml = '<div class="alert alert-' + type + '">' + message + '</div>';
        $('#alerts').html(alertHtml);
        setTimeout(function() {
            $('#alerts').html('');
        }, 3000);
    }

    const fileInput = document.getElementById('attachment');
    const fileNameSpan = document.getElementById('file-name');

    fileInput.addEventListener('change', function() {
        const fileName = this.files[0].name;
        fileNameSpan.textContent = fileName;
    });
</script>

<div class="container mt-5">
    <h2>Order Dates Management</h2>
    <div id="alerts"></div>
    <table class="table" id="datesTable">
        <thead>
        <tr>
            <th>Date Type</th>
            <th>From</th>
            <th>To</th>
            <th>Action</th>
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
                <h5 class="modal-title" id="modifyDateModalLabel">Modify Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modifyDateForm">
                    <div class="form-group">
                        <label for="dateType">Date Type</label>
                        <select class="form-control" id="dateType">
                            <option value="shipment">Shipment</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateFrom">From</label>
                        <input type="date" class="form-control" id="dateFrom" required>
                    </div>
                    <div class="form-group">
                        <label for="dateTo">To</label>
                        <input type="date" class="form-control" id="dateTo" required>
                    </div>
                    <input type="hidden" id="orderId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveDateChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        loadOrderDates();

        $('#saveDateChanges').click(function() {
            const orderId = $('#orderId').val();
            const dateType = $('#dateType').val();
            const dateFrom = $('#dateFrom').val();
            const dateTo = $('#dateTo').val();
            modifyOrderDate(orderId, dateType, dateFrom, dateTo);
        });

        function loadOrderDates() {
            $.ajax({
                url: '/api/orders/dates', // Adjust this URL to your API endpoint
                type: 'GET',
                success: function(data) {
                    if (data && data.dates) {
                        populateDatesTable(data.dates);
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Failed to load order dates.');
                }
            });
        }

        function modifyOrderDate(orderId, dateType, dateFrom, dateTo) {
            $.ajax({
                url: '/api/orders/' + orderId + '/dates/modify', // Adjust this URL to your API endpoint
                type: 'POST',
                data: {
                    dateType: dateType,
                    dateFrom: dateFrom,
                    dateTo: dateTo
                },
                success: function(data) {
                    $('#modifyDateModal').modal('hide');
                    showAlert('success', 'Date successfully modified.');
                    loadOrderDates(); // Refresh dates table
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Failed to modify the date.');
                }
            });
        }

        function populateDatesTable(dates) {
            let html = '';
            dates.forEach(function(date) {
                html += '<tr>' +
                    '<td>' + date.type + '</td>' +
                    '<td>' + date.from + '</td>' +
                    '<td>' + date.to + '</td>' +
                    '<td><button class="btn btn-primary btn-sm" onclick="showModifyDateModal(' + date.orderId + ', \'' + date.type + '\', \'' + date.from + '\', \'' + date.to + '\')">Modify</button></td>' +
                    '</tr>';
            });
            $('#datesTable tbody').html(html);
        }

        window.showModifyDateModal = function(orderId, type, from, to) {
            $('#orderId').val(orderId);
            $('#dateType').val(type);
            $('#dateFrom').val(from);
            $('#dateTo').val(to);
            $('#modifyDateModal').modal('show');
        }

        function showAlert(type, message) {
            const alertHtml = '<div class="alert alert-' + type + '">' + message + '</div>';
            $('#alerts').html(alertHtml);
            setTimeout(function() {
                $('#alerts').html('');
            }, 3000);
        }
    });
</script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

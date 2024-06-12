<div class="mt-5">
    <h2 class="text-2xl font-bold mb-4">Zarządzanie datami do zamówienia</h2>
    <div id="alerts"></div>
    <table class="table table-auto border-collapse">
        <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 border">Typ daty</th>
            <th class="px-4 py-2 border">Od</th>
            <th class="px-4 py-2 border">Do</th>
            <th class="px-4 py-2 border">Akcje</th>
        </tr>
        </thead>
        <tbody>
        <!-- Rows will be populated by JavaScript -->
        </tbody>
    </table>
</div>

<!-- Modify Date Modal -->
<div class="modal fade fixed inset-0 z-50 overflow-auto hidden" id="modifyDateModal" tabindex="-1" role="dialog" aria-labelledby="modifyDateModalLabel" aria-hidden="true">
    <div class="modal-dialog relative w-auto max-w-lg mx-auto my-8">
        <div class="modal-content bg-white rounded-lg shadow-lg">
            <div class="modal-header flex justify-between items-center p-4 border-b">
                <h5 class="modal-title text-lg font-bold" id="modifyDateModalLabel">Modyfikuj daty</h5>
                <button type="button" class="close text-2xl font-bold" data-dismiss="modal" aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="modifyDateForm">
                    <div class="form-group hidden">
                        <label for="dateType" class="block mb-2 font-bold">Typ daty</label>
                        <select form="modifyDateForm" class="form-control w-full px-3 py-2 mb-2 border border-gray-300 rounded-md" id="dateType">
                            <option value="shipment">Wysyłka</option>
                            <option value="delivery">Dostawa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateFrom" class="block mb-2 font-bold">Od</label>
                        <input form="modifyDateForm" type="datetime-local" value="{{ now()->setTime(00, 00) }}" class="form-control w-full px-3 py-2 mb-2 border border-gray-300 rounded-md" id="dateFrom" required>
                    </div>
                    <div class="form-group">
                        <label for="dateTo" class="block mb-2 font-bold">Do</label>
                        <input form="modifyDateForm" type="datetime-local" value="{{ now()->setTime(23, 59) }}" class="form-control w-full px-3 py-2 mb-2 border border-gray-300 rounded-md" id="dateTo" required>
                    </div>
                    <input form="modifyDateForm" type="hidden" id="orderId" value="">
                </form>
            </div>
            <div class="modal-footer flex justify-end items-center p-4 border-t">
                <button type="button" class="btn btn-secondary mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300" data-dismiss="modal">Zamknij</button>
                <button type="button" class="btn btn-primary px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" id="saveDateChanges" onclick="updateDates(); event.preventDefault();">Zapisz zmiany</button>
            </div>
        </div>
    </div>
</div>

<div id="loadingScreen" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="p-6 bg-white rounded-lg shadow-lg">
        Zapisywanie dat, proszę czekać...
    </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

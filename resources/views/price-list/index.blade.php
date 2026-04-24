@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="fa fa-tag"></i> Edycja cennika firmy
    </h1>
@endsection

@section('app-content')
<style>
    .pl-panel .panel-heading { background: #f5f7fa; border-bottom: 1px solid #e0e4ea; padding: 10px 16px; }
    .pl-panel .panel-title   { font-size: 14px; font-weight: 600; color: #444; }
    .pl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .pl-table thead th {
        background: #eceff4; color: #555; font-size: 12px; font-weight: 600;
        padding: 8px 10px; border-bottom: 2px solid #d0d5de; white-space: nowrap;
        vertical-align: bottom;
    }
    .pl-table tbody tr:nth-child(even) { background: #fafbfc; }
    .pl-table tbody tr:hover           { background: #f0f4ff; }
    .pl-table td { padding: 7px 10px; vertical-align: middle; border-bottom: 1px solid #eaecf0; }
    .pl-table .td-product { min-width: 200px; }
    .pl-table .product-main  { font-weight: 600; color: #2c3e50; font-size: 13px; }
    .pl-table .product-alias { font-size: 11px; color: #999; display: block; margin-bottom: 2px; }
    .pl-table code { font-size: 12px; background: #f0f0f0; padding: 1px 5px; border-radius: 3px; color: #c0392b; }
    .pl-table .date-input { width: 138px; font-size: 12px; }
    .pl-table .price-wrap { min-width: 110px; }
    .pl-table .price-was  { font-size: 10px; color: #bbb; display: block; margin-bottom: 2px; }
    .pl-table .price-input { width: 100px !important; font-size: 13px; font-weight: 600; }
    .pl-table .readonly-val { font-size: 13px; color: #555; white-space: nowrap; }
    .pl-table .pattern-val  { font-size: 11px; color: #888; font-family: monospace; white-space: nowrap; }

    /* Variant (child) product rows */
    .tr-variant { background: #f7f8fa !important; }
    .tr-variant:hover { background: #eef0f7 !important; }
    .tr-variant .td-product { padding-left: 28px; }
    .tr-variant .product-main { font-size: 12px; }
    .variant-arrow { color: #bbb; margin-right: 5px; font-size: 11px; }
    .badge-variant {
        display: inline-block; font-size: 10px; font-weight: 600; line-height: 1;
        padding: 2px 5px; border-radius: 3px; margin-right: 5px;
        background: #95a5a6; color: #fff; vertical-align: middle;
    }

    #firm-search { font-size: 14px; }
    .firm-option { padding: 8px 14px; cursor: pointer; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    .firm-option:hover { background: #f0f4ff; }

    .global-dates-row { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; }
    .global-dates-row .gd-group { display: flex; align-items: center; gap: 8px; }
    .global-dates-row label { margin: 0; font-size: 12px; font-weight: 600; color: #555; white-space: nowrap; }
    .global-dates-row .form-control { width: 148px; font-size: 13px; }
</style>

<div class="container-fluid">

    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }}">{{ session('message') }}</div>
    @endif

    {{-- Firm selector --}}
    <div class="panel panel-bordered pl-panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="firm-search">
                            <strong>Firma / dostawca</strong>
                            <small class="text-muted">&nbsp;{{ $firms->count() }} firm z produktami</small>
                        </label>
                        <div style="position:relative;">
                            <input type="text" id="firm-search" class="form-control"
                                   placeholder="Wpisz nazwę lub symbol firmy…" autocomplete="off">
                            <div id="firm-dropdown" style="
                                display:none; position:absolute; top:100%; left:0; right:0;
                                background:#fff; border:1px solid #ccc; border-top:none;
                                max-height:340px; overflow-y:auto; z-index:1000;
                                box-shadow:0 4px 12px rgba(0,0,0,.12);">
                            </div>
                        </div>
                        <div id="firm-selected-info" style="display:none; margin-top:7px;">
                            <i class="fa fa-building-o text-muted"></i>
                            <strong id="firm-selected-label" style="margin-left:5px;"></strong>
                            <a href="#" id="firm-clear" style="margin-left:12px; font-size:12px; color:#e74c3c;">
                                <i class="fa fa-times"></i> zmień
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="padding-top:26px;">
                    <span id="loading-indicator" style="display:none; color:#888;">
                        <i class="fa fa-spinner fa-spin"></i> Wczytuję produkty…
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert area --}}
    <div id="alert-area"></div>

    {{-- Product groups area --}}
    <div id="products-area" style="display:none;">

        {{-- Global date fill --}}
        <div class="panel panel-bordered pl-panel">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar-o"></i> Wypełnij daty dla wszystkich produktów</h3>
            </div>
            <div class="panel-body" style="padding:12px 16px;">
                <div class="global-dates-row">
                    <div class="gd-group">
                        <label for="global-date-new">Obowiązuje od <small class="text-muted">(nowa cena aktywna od)</small></label>
                        <input type="date" id="global-date-new" class="form-control">
                        <button class="btn btn-default btn-sm" id="apply-date-new">
                            <i class="fa fa-arrow-down"></i> Ustaw wszystkim
                        </button>
                    </div>
                    <div class="gd-group">
                        <label for="global-date-change">Następna zmiana ceny <small class="text-muted">(do kiedy cena obowiązuje)</small></label>
                        <input type="date" id="global-date-change" class="form-control">
                        <button class="btn btn-default btn-sm" id="apply-date-change">
                            <i class="fa fa-arrow-down"></i> Ustaw wszystkim
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="groups-container"></div>

        <div class="panel panel-bordered">
            <div class="panel-body">
                <button id="save-btn" class="btn btn-success btn-lg">
                    <i class="fa fa-save"></i> Zapisz wszystkie ceny
                </button>
                <span id="save-indicator" style="display:none; margin-left:15px; color:#888;">
                    <i class="fa fa-spinner fa-spin"></i> Zapisuję…
                </span>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    var CSRF_TOKEN    = '{{ csrf_token() }}';
    var FIRMS_DATA    = @json($firms);
    var currentFirmId = null;
    var currentPage   = 1;
    var lastPage      = 1;
    var productRows   = {};

    var firmSearchInput  = document.getElementById('firm-search');
    var firmDropdown     = document.getElementById('firm-dropdown');
    var firmSelectedInfo = document.getElementById('firm-selected-info');
    var firmSelectedLabel= document.getElementById('firm-selected-label');
    var firmClear        = document.getElementById('firm-clear');
    var loadingIndicator = document.getElementById('loading-indicator');
    var productsArea     = document.getElementById('products-area');
    var groupsContainer  = document.getElementById('groups-container');
    var alertArea        = document.getElementById('alert-area');
    var saveBtn          = document.getElementById('save-btn');
    var saveIndicator    = document.getElementById('save-indicator');
    var globalDateChange = document.getElementById('global-date-change');
    var globalDateNew    = document.getElementById('global-date-new');
    var applyDateChange  = document.getElementById('apply-date-change');
    var applyDateNew     = document.getElementById('apply-date-new');

    // ── Global date fill ───────────────────────────────────────────
    applyDateChange.addEventListener('click', function () {
        var val = globalDateChange.value;
        if (!val) return;
        document.querySelectorAll('.date-change').forEach(function (inp) { inp.value = val; });
    });

    applyDateNew.addEventListener('click', function () {
        var val = globalDateNew.value;
        if (!val) return;
        document.querySelectorAll('.date-new').forEach(function (inp) { inp.value = val; });
    });

    // ── Firm search ────────────────────────────────────────────────
    firmSearchInput.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        if (!q) { firmDropdown.style.display = 'none'; return; }

        var matches = FIRMS_DATA.filter(function (f) {
            return f.name.toLowerCase().indexOf(q) !== -1 ||
                   (f.symbol && f.symbol.toLowerCase().indexOf(q) !== -1);
        });

        if (!matches.length) { firmDropdown.style.display = 'none'; return; }

        firmDropdown.innerHTML = matches.map(function (f) {
            return '<div class="firm-option" data-id="' + f.id + '">' +
                '<strong>' + escHtml(f.name) + '</strong>' +
                (f.symbol ? ' <span style="color:#999;font-size:12px;">(' + escHtml(f.symbol) + ')</span>' : '') +
                '</div>';
        }).join('');
        firmDropdown.style.display = '';
    });

    firmDropdown.addEventListener('click', function (e) {
        var opt = e.target.closest('.firm-option');
        if (!opt) return;
        selectFirm(parseInt(opt.dataset.id, 10));
    });

    firmClear.addEventListener('click', function (e) {
        e.preventDefault();
        currentFirmId = null;
        currentPage   = 1;
        lastPage      = 1;
        firmSearchInput.value          = '';
        firmSelectedInfo.style.display = 'none';
        firmSearchInput.style.display  = '';
        productsArea.style.display     = 'none';
        groupsContainer.innerHTML      = '';
        productRows = {};
        var pb = document.getElementById('pagination-bar');
        if (pb) pb.remove();
        showAlert('');
    });

    document.addEventListener('click', function (e) {
        if (!firmDropdown.contains(e.target) && e.target !== firmSearchInput) {
            firmDropdown.style.display = 'none';
        }
    });

    function selectFirm(id) {
        var firm = FIRMS_DATA.find(function (f) { return f.id === id; });
        if (!firm) return;
        currentFirmId = id;
        firmDropdown.style.display     = 'none';
        firmSearchInput.style.display  = 'none';
        firmSelectedLabel.textContent  = firm.name + (firm.symbol ? ' (' + firm.symbol + ')' : '');
        firmSelectedInfo.style.display = '';
        currentPage = 1;
        loadProducts(currentFirmId, 1);
    }

    // ── Fetch products ─────────────────────────────────────────────
    function loadProducts(firmId, page) {
        page = page || 1;
        showAlert('');
        loadingIndicator.style.display = '';
        productsArea.style.display     = 'none';
        groupsContainer.innerHTML      = '';
        productRows = {};

        var url = '{{ route('price-list.products', ['firmId' => '_ID_']) }}'.replace('_ID_', firmId) + '?page=' + page;

        fetch(url)
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                loadingIndicator.style.display = 'none';
                if (!data.products || data.products.length === 0) {
                    showAlert('Brak produktów przypisanych do tej firmy.', 'warning');
                    return;
                }
                currentPage = data.current_page;
                lastPage    = data.last_page;
                renderProductsTable(data.products, data.header || {});
                renderPagination(data);
                productsArea.style.display = '';
            })
            .catch(function (err) {
                loadingIndicator.style.display = 'none';
                showAlert('Błąd pobierania danych: ' + err.message, 'danger');
            });
    }

    // ── Pagination ─────────────────────────────────────────────────
    function renderPagination(data) {
        var existing = document.getElementById('pagination-bar');
        if (existing) existing.remove();

        var bar = document.createElement('div');
        bar.id = 'pagination-bar';
        bar.style.cssText = 'display:flex;align-items:center;gap:8px;padding:12px 16px;background:#fff;border:1px solid #e0e4ea;border-radius:4px;margin-bottom:16px;flex-wrap:wrap;';

        var info = document.createElement('span');
        info.style.cssText = 'flex:1;font-size:13px;color:#666;min-width:180px;';
        info.textContent = 'Strona ' + data.current_page + ' z ' + data.last_page + ' (' + data.total + ' produktów nadrzędnych)';
        bar.appendChild(info);

        if (data.last_page > 1) {
            var prevBtn = document.createElement('button');
            prevBtn.className = 'btn btn-default btn-sm';
            prevBtn.innerHTML = '<i class="fa fa-chevron-left"></i> Poprzednia';
            prevBtn.disabled  = data.current_page <= 1;
            prevBtn.addEventListener('click', function () {
                loadProducts(currentFirmId, currentPage - 1);
                window.scrollTo(0, 0);
            });
            bar.appendChild(prevBtn);

            var start = Math.max(1, data.current_page - 3);
            var end   = Math.min(data.last_page, data.current_page + 3);
            for (var p = start; p <= end; p++) {
                (function (pageNum) {
                    var btn = document.createElement('button');
                    btn.className = 'btn btn-sm ' + (pageNum === data.current_page ? 'btn-primary' : 'btn-default');
                    btn.textContent = pageNum;
                    btn.addEventListener('click', function () {
                        loadProducts(currentFirmId, pageNum);
                        window.scrollTo(0, 0);
                    });
                    bar.appendChild(btn);
                })(p);
            }

            var nextBtn = document.createElement('button');
            nextBtn.className = 'btn btn-default btn-sm';
            nextBtn.innerHTML = 'Następna <i class="fa fa-chevron-right"></i>';
            nextBtn.disabled  = data.current_page >= data.last_page;
            nextBtn.addEventListener('click', function () {
                loadProducts(currentFirmId, currentPage + 1);
                window.scrollTo(0, 0);
            });
            bar.appendChild(nextBtn);
        }

        productsArea.insertBefore(bar, productsArea.firstChild);
    }

    // ── Render flat products table ─────────────────────────────────
    function renderProductsTable(products, header) {
        var cols = ['first'];

        var showMilling = products.some(function (p) { return !!p.show_milling; });

        var panel = document.createElement('div');
        panel.className = 'panel panel-bordered pl-panel';

        var body = document.createElement('div');
        body.className = 'panel-body';
        body.style.padding = '0';

        var table = document.createElement('table');
        table.className = 'pl-table';

        var thead = document.createElement('thead');
        var trH   = document.createElement('tr');
        trH.innerHTML =
            '<th class="td-product">Produkt</th>' +
            '<th>Symbol</th>' +
            '<th>Obowiązuje od</th>' +
            '<th>Następna zmiana ceny</th>' +
            '<th class="price-wrap">Cena netto<br><small class="text-muted">(PLN / j.p.)</small></th>' +
            (showMilling ? '<th class="price-wrap">Dopłata za frez.<br><small class="text-muted">(PLN/m³)</small></th>' : '') +
            '<th>Metoda wyliczenia</th>' +
            '<th class="price-wrap">Cena netto<br><small class="text-muted">(wyliczona)</small></th>' +
            '<th style="text-align:center;">Szt.<br><small class="text-muted">w opak.</small></th>' +
            '<th class="price-wrap">Cena netto<br><small class="text-muted">/ opak.</small></th>' +
            '<th style="text-align:center;">VAT</th>' +
            '<th class="price-wrap">Cena brutto<br><small class="text-muted">(wyliczona)</small></th>';
        thead.appendChild(trH);
        table.appendChild(thead);

        var tbody = document.createElement('tbody');
        products.forEach(function (p) { tbody.appendChild(renderProductRow(p, cols, showMilling)); });
        table.appendChild(tbody);

        body.appendChild(table);
        panel.appendChild(body);
        groupsContainer.appendChild(panel);
    }

    // ── Render one product row ─────────────────────────────────────
    function renderProductRow(p, activeCols, showMilling) {
        var isVariant  = !!p.is_variant;
        var tr         = document.createElement('tr');
        tr.dataset.productId = p.id;
        if (isVariant) tr.classList.add('tr-variant');

        var today      = formatDate(new Date());
        var dateChange = p.date_of_price_change || today;
        var dateNew    = p.date_of_the_new_prices || '';
        var vat        = p.vat || 23;
        var vatRate    = vat / 100;
        var pack       = p.numbers_of_basic_commercial_units_in_pack || 1;
        var firstPrice = parseFloat(p.value_of_price_change_data_first || 0);
        var milling    = parseFloat(p.additional_payment_for_milling || 0);
        var calcNet    = firstPrice + milling;
        var calcBrutto = calcNet * (1 + vatRate);

        var variantPrefix = isVariant
            ? '<span class="variant-arrow">↳</span><span class="badge-variant">wariant</span>'
            : '';

        // Date cells
        var tdDates = isVariant
            ? '<td><span class="readonly-val text-muted" style="font-size:12px;">' + escHtml(dateNew || '—') + '</span></td>' +
              '<td><span class="readonly-val text-muted" style="font-size:12px;">' + escHtml(dateChange || '—') + '</span></td>'
            : '<td><input type="date" class="form-control input-sm date-input date-new" data-field="date_of_the_new_prices" value="' + escHtml(dateNew) + '"></td>' +
              '<td><input type="date" class="form-control input-sm date-input date-change" data-field="date_of_price_change" value="' + escHtml(dateChange) + '"></td>';

        // First price cell
        var tdFirstPrice = isVariant
            ? '<td class="price-wrap"><span class="readonly-val">' + firstPrice.toFixed(2) + ' <small class="text-muted">PLN</small></span></td>'
            : '<td class="price-wrap">' +
                  '<span class="price-was">poprzednio: ' + firstPrice.toFixed(2) + '</span>' +
                  '<input type="number" class="form-control price-input" data-field="value_of_price_change_data_first" data-required="1" value="' + firstPrice.toFixed(2) + '" step="0.01" min="0">' +
              '</td>';

        // Milling cell
        var tdMilling = showMilling
            ? (isVariant
                ? '<td class="price-wrap"><span class="readonly-val">' + milling.toFixed(2) + ' <small class="text-muted">PLN</small></span></td>'
                : '<td class="price-wrap">' +
                      '<span class="price-was">poprzednio: ' + milling.toFixed(2) + '</span>' +
                      '<input type="number" class="form-control price-input" data-field="additional_payment_for_milling" value="' + milling.toFixed(2) + '" step="0.01" min="0">' +
                  '</td>')
            : '';

        tr.innerHTML =
            '<td class="td-product">' +
                (p.product_name_supplier_on_documents ? '<span class="product-alias">' + escHtml(p.product_name_supplier_on_documents) + '</span>' : '') +
                variantPrefix +
                '<span class="product-main">' + escHtml(p.name) + '</span>' +
            '</td>' +
            '<td><code>' + escHtml(p.symbol) + '</code></td>' +
            tdDates +
            tdFirstPrice +
            tdMilling +
            '<td><span class="pattern-val">' + escHtml(p.pattern_to_set_the_price || '—') + '</span></td>' +
            '<td class="price-wrap"><span class="readonly-val calc-net-val">' + calcNet.toFixed(2) + ' <small class="text-muted">PLN</small></span></td>' +
            '<td style="text-align:center;"><span class="readonly-val">' + pack + '</span></td>' +
            '<td class="price-wrap"><span class="readonly-val pack-price-val">' + (firstPrice * pack).toFixed(2) + ' <small class="text-muted">PLN</small></span></td>' +
            '<td style="text-align:center;"><span class="readonly-val">' + vat + '%</span></td>' +
            '<td class="price-wrap"><span class="readonly-val calc-brutto-val">' + calcBrutto.toFixed(2) + ' <small class="text-muted">PLN</small></span></td>';

        if (!isVariant) {
            productRows[p.id] = { row: tr, product: p };

            var firstInput   = tr.querySelector('[data-required="1"]');
            var millingInput = tr.querySelector('[data-field="additional_payment_for_milling"]');

            function updateCalc() {
                var v   = parseFloat(firstInput ? firstInput.value.replace(',', '.') : 0) || 0;
                var m   = parseFloat(millingInput ? millingInput.value.replace(',', '.') : 0) || 0;
                var net = v + m;
                tr.querySelector('.calc-net-val').innerHTML   = net.toFixed(2)           + ' <small class="text-muted">PLN</small>';
                tr.querySelector('.pack-price-val').innerHTML = (v * pack).toFixed(2)    + ' <small class="text-muted">PLN</small>';
                tr.querySelector('.calc-brutto-val').innerHTML= (net*(1+vatRate)).toFixed(2) + ' <small class="text-muted">PLN</small>';
            }

            if (firstInput)   firstInput.addEventListener('input', updateCalc);
            if (millingInput) millingInput.addEventListener('input', updateCalc);

            var dateChangeInput = tr.querySelector('.date-change');
            var dateNewInput    = tr.querySelector('.date-new');
            [dateNewInput, dateChangeInput].forEach(function (inp) {
                inp.addEventListener('change', function () { validateDates(dateNewInput, dateChangeInput); });
            });
        }

        return tr;
    }

    // ── Save ───────────────────────────────────────────────────────
    saveBtn.addEventListener('click', function () {
        if (!currentFirmId) return;

        var payload = [];
        var errors  = [];

        Object.values(productRows).forEach(function (entry) {
            var tr  = entry.row;
            var pid = parseInt(tr.dataset.productId, 10);

            var dateChange = tr.querySelector('[data-field="date_of_price_change"]').value;
            var dateNew    = tr.querySelector('[data-field="date_of_the_new_prices"]').value;
            var firstInput = tr.querySelector('[data-required="1"]');
            var firstVal   = firstInput ? parseFloat(firstInput.value.replace(',', '.')) : 0;

            if (!dateNew)    errors.push('Produkt ID ' + pid + ': brak daty "Obowiązuje od".');
            if (!dateChange) errors.push('Produkt ID ' + pid + ': brak daty następnej zmiany ceny.');
            if (dateNew && dateChange && dateNew > dateChange)
                errors.push('Produkt ID ' + pid + ': "Obowiązuje od" nie może być późniejsze niż następna zmiana.');
            if (firstInput && (isNaN(firstVal) || firstVal < 0))
                errors.push('Produkt ID ' + pid + ': cena nie może być ujemna.');

            var item = { id: pid, date_of_price_change: dateChange, date_of_the_new_prices: dateNew };
            tr.querySelectorAll('.price-input').forEach(function (input) {
                item[input.dataset.field] = parseFloat(input.value.replace(',', '.')) || 0;
            });
            payload.push(item);
        });

        if (errors.length) {
            showAlert('<strong>Popraw błędy przed zapisem:</strong><ul style="margin:8px 0 0;">' +
                errors.map(function (e) { return '<li>' + escHtml(e) + '</li>'; }).join('') +
                '</ul>', 'danger');
            window.scrollTo(0, 0);
            return;
        }

        showAlert('');
        saveBtn.disabled            = true;
        saveIndicator.style.display = '';

        fetch('{{ route('price-list.products', ['firmId' => '_ID_']) }}'.replace('_ID_', currentFirmId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify(payload),
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(function () {
            showAlert('Ceny zostały zapisane pomyślnie.', 'success');
            window.scrollTo(0, 0);
        })
        .catch(function (err) {
            showAlert('Błąd zapisu: ' + err.message + '. Sprawdź logi serwera.', 'danger');
            window.scrollTo(0, 0);
        })
        .finally(function () {
            saveBtn.disabled            = false;
            saveIndicator.style.display = 'none';
        });
    });

    // ── Helpers ────────────────────────────────────────────────────
    function validateDates(dateNewInput, dateChangeInput) {
        // "Obowiązuje od" must be <= "Następna zmiana ceny"
        if (dateNewInput.value && dateChangeInput.value && dateNewInput.value > dateChangeInput.value) {
            dateNewInput.setCustomValidity('"Obowiązuje od" nie może być późniejsze niż data następnej zmiany.');
            dateNewInput.style.borderColor = '#e74c3c';
        } else {
            dateNewInput.setCustomValidity('');
            dateNewInput.style.borderColor = '';
        }
    }

    function formatDate(d) {
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function showAlert(msg, type) {
        if (!msg) { alertArea.innerHTML = ''; return; }
        alertArea.innerHTML = '<div class="alert alert-' + (type || 'info') + '">' + msg + '</div>';
    }

})();
</script>
@endsection

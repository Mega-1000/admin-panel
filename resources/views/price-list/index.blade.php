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
                        <label for="global-date-change">Data zmiany ceny</label>
                        <input type="date" id="global-date-change" class="form-control">
                        <button class="btn btn-default btn-sm" id="apply-date-change">
                            <i class="fa fa-arrow-down"></i> Ustaw wszystkim
                        </button>
                    </div>
                    <div class="gd-group">
                        <label for="global-date-new">Obowiązuje od</label>
                        <input type="date" id="global-date-new" class="form-control">
                        <button class="btn btn-default btn-sm" id="apply-date-new">
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

    var CSRF_TOKEN  = '{{ csrf_token() }}';
    var FIRMS_DATA  = @json($firms);
    var currentFirmId = null;
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
        firmSearchInput.value          = '';
        firmSelectedInfo.style.display = 'none';
        firmSearchInput.style.display  = '';
        productsArea.style.display     = 'none';
        groupsContainer.innerHTML      = '';
        productRows = {};
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
        loadProducts(currentFirmId);
    }

    // ── Fetch products ─────────────────────────────────────────────
    function loadProducts(firmId) {
        showAlert('');
        loadingIndicator.style.display = '';
        productsArea.style.display     = 'none';
        groupsContainer.innerHTML      = '';
        productRows = {};

        fetch('{{ route('price-list.products', ['firmId' => '_ID_']) }}'.replace('_ID_', firmId))
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                loadingIndicator.style.display = 'none';
                if (!data || Object.keys(data).length === 0) {
                    showAlert('Brak produktów przypisanych do tej firmy.', 'warning');
                    return;
                }
                renderGroups(data);
                productsArea.style.display = '';
            })
            .catch(function (err) {
                loadingIndicator.style.display = 'none';
                showAlert('Błąd pobierania danych: ' + err.message, 'danger');
            });
    }

    // ── Render all groups ──────────────────────────────────────────
    function renderGroups(data) {
        Object.keys(data).sort().forEach(function (groupName) {
            var subgroups = data[groupName];
            Object.keys(subgroups).sort(function (a, b) { return a - b; }).forEach(function (subNum) {
                groupsContainer.appendChild(renderSubgroup(groupName, subNum, subgroups[subNum]));
            });
        });
    }

    // ── Render one subgroup panel ──────────────────────────────────
    function renderSubgroup(groupName, subNum, subgroup) {
        var mainText = (subgroup.mainText && subgroup.mainText.text_price_change) || (groupName + ' – ' + subNum);
        var header   = subgroup.header || {};

        var cols = ['first', 'second', 'third', 'fourth'].filter(function (c) {
            return !!header['text_price_change_data_' + c];
        });

        var products = Object.keys(subgroup)
            .filter(function (k) { return subgroup[k] && typeof subgroup[k] === 'object' && 'id' in subgroup[k]; })
            .map(function (k) { return subgroup[k]; })
            .sort(function (a, b) { return (a.order || 0) - (b.order || 0); });

        var panel = document.createElement('div');
        panel.className = 'panel panel-bordered pl-panel';

        var heading = document.createElement('div');
        heading.className = 'panel-heading';
        heading.innerHTML =
            '<h3 class="panel-title">' +
            '<i class="fa fa-list" style="margin-right:6px;color:#7f8c8d;"></i>' +
            escHtml(mainText) +
            ' <span class="text-muted" style="font-weight:400;font-size:12px;">— ' + products.length + ' produktów</span>' +
            '</h3>';
        panel.appendChild(heading);

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
            '<th>Data zmiany ceny</th>' +
            '<th>Obowiązuje od</th>' +
            cols.map(function (c) {
                var label = escHtml(header['text_price_change_data_' + c] || c);
                return '<th class="price-wrap">' + label + '</th>';
            }).join('');
        thead.appendChild(trH);
        table.appendChild(thead);

        var tbody = document.createElement('tbody');
        products.forEach(function (p) { tbody.appendChild(renderProductRow(p, cols)); });
        table.appendChild(tbody);

        body.appendChild(table);
        panel.appendChild(body);
        return panel;
    }

    // ── Render one product row ─────────────────────────────────────
    function renderProductRow(p, activeCols) {
        var tr = document.createElement('tr');
        tr.dataset.productId = p.id;

        var today      = formatDate(new Date());
        var dateChange = p.date_of_price_change || today;
        var dateNew    = p.date_of_the_new_prices || '';
        var vatMult    = 1 + (p.vat || 23) / 100;

        tr.innerHTML =
            '<td class="td-product">' +
                (p.product_name_supplier_on_documents
                    ? '<span class="product-alias">' + escHtml(p.product_name_supplier_on_documents) + '</span>'
                    : '') +
                '<span class="product-main">' + escHtml(p.name) + '</span>' +
            '</td>' +
            '<td><code>' + escHtml(p.symbol) + '</code></td>' +
            '<td>' +
                '<input type="date" class="form-control input-sm date-input date-change" ' +
                    'data-field="date_of_price_change" value="' + escHtml(dateChange) + '">' +
            '</td>' +
            '<td>' +
                '<input type="date" class="form-control input-sm date-input date-new" ' +
                    'data-field="date_of_the_new_prices" value="' + escHtml(dateNew) + '">' +
            '</td>' +
            activeCols.map(function (c) {
                var field   = 'value_of_price_change_data_' + c;
                var current = parseFloat(p[field] || 0).toFixed(2);
                var required = c === 'first' ? 'data-required="1"' : '';
                var bruttoHtml = '';
                if (c === 'first') {
                    var brutto = (parseFloat(current) * vatMult).toFixed(2);
                    bruttoHtml = ' <span class="brutto-preview text-muted" style="font-size:12px;white-space:nowrap;">' +
                        '→ brutto: <strong class="brutto-val">' + brutto + '</strong>' +
                        ' <small>(' + (p.vat || 23) + '%)</small></span>';
                }
                return '<td class="price-wrap">' +
                    '<span class="price-was">poprzednio: ' + current + '</span>' +
                    '<div style="display:flex;align-items:center;gap:8px;">' +
                        '<input type="number" class="form-control price-input" ' +
                            'data-field="' + field + '" ' + required +
                            ' value="' + current + '" step="0.01" min="0">' +
                        bruttoHtml +
                    '</div>' +
                    '</td>';
            }).join('');

        productRows[p.id] = { row: tr, product: p };

        // Live brutto preview for first price col
        var firstInput = tr.querySelector('[data-required="1"]');
        if (firstInput) {
            firstInput.addEventListener('input', function () {
                var v = parseFloat(this.value.replace(',', '.')) || 0;
                var el = tr.querySelector('.brutto-val');
                if (el) el.textContent = (v * vatMult).toFixed(2);
            });
        }

        var dateChangeInput = tr.querySelector('.date-change');
        var dateNewInput    = tr.querySelector('.date-new');
        dateNewInput.addEventListener('change', function () {
            validateDates(dateChangeInput, dateNewInput);
        });

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

            if (!dateChange) errors.push('Produkt ID ' + pid + ': brak daty zmiany ceny.');
            if (!dateNew)    errors.push('Produkt ID ' + pid + ': brak daty obowiązywania.');
            if (dateChange && dateNew && dateNew < dateChange)
                errors.push('Produkt ID ' + pid + ': "Obowiązuje od" musi być ≥ dacie zmiany.');
            if (firstInput && (isNaN(firstVal) || firstVal <= 0))
                errors.push('Produkt ID ' + pid + ': cena musi być większa od 0.');

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
    function validateDates(changeInput, newInput) {
        if (changeInput.value && newInput.value && newInput.value < changeInput.value) {
            newInput.setCustomValidity('Data obowiązywania musi być >= dacie zmiany.');
            newInput.style.borderColor = '#e74c3c';
        } else {
            newInput.setCustomValidity('');
            newInput.style.borderColor = '';
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

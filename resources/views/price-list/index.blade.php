@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="fa fa-tag"></i> Edycja cennika firmy
    </h1>
@endsection

@section('app-content')
<div class="container-fluid">

    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }}">{{ session('message') }}</div>
    @endif

    {{-- Firm selector --}}
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="firm-search"><strong>Wybierz firmę / dostawcę</strong>
                            <small class="text-muted">({{ $firms->count() }} firm z produktami)</small>
                        </label>
                        <div style="position:relative;">
                            <input type="text" id="firm-search" class="form-control"
                                   placeholder="Wpisz nazwę lub symbol firmy..."
                                   autocomplete="off">
                            <div id="firm-dropdown" style="
                                display:none; position:absolute; top:100%; left:0; right:0;
                                background:#fff; border:1px solid #ccc; border-top:none;
                                max-height:320px; overflow-y:auto; z-index:1000;
                                box-shadow:0 4px 8px rgba(0,0,0,.15);">
                            </div>
                        </div>
                        <div id="firm-selected-info" style="display:none; margin-top:6px;">
                            <span class="label label-info" id="firm-selected-label"></span>
                            <a href="#" id="firm-clear" style="margin-left:8px; font-size:12px;">zmień</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="padding-top:25px;">
                    <span id="loading-indicator" style="display:none;">
                        <i class="fa fa-spinner fa-spin"></i> Wczytuję produkty...
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
        <div class="panel panel-bordered">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar"></i> Ustaw daty dla wszystkich produktów</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon">Data zmiany</span>
                            <input type="date" id="global-date-change" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default" id="apply-date-change">Ustaw wszystkim</button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon">Obowiązuje od</span>
                            <input type="date" id="global-date-new" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default" id="apply-date-new">Ustaw wszystkim</button>
                            </span>
                        </div>
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
                <span id="save-indicator" style="display:none; margin-left:15px;">
                    <i class="fa fa-spinner fa-spin"></i> Zapisuję...
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
    var saveBtn           = document.getElementById('save-btn');
    var saveIndicator     = document.getElementById('save-indicator');
    var globalDateChange  = document.getElementById('global-date-change');
    var globalDateNew     = document.getElementById('global-date-new');
    var applyDateChange   = document.getElementById('apply-date-change');
    var applyDateNew      = document.getElementById('apply-date-new');

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
            return '<div class="firm-option" data-id="' + f.id + '" style="' +
                'padding:8px 12px; cursor:pointer; border-bottom:1px solid #eee;">' +
                '<strong>' + escHtml(f.name) + '</strong>' +
                (f.symbol ? ' <small class="text-muted">(' + escHtml(f.symbol) + ')</small>' : '') +
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
        firmSearchInput.value        = '';
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

    firmDropdown.addEventListener('mouseover', function (e) {
        var opt = e.target.closest('.firm-option');
        if (opt) opt.style.background = '#f5f5f5';
    });
    firmDropdown.addEventListener('mouseout', function (e) {
        var opt = e.target.closest('.firm-option');
        if (opt) opt.style.background = '';
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
        // data = { groupName: { subgroupNum: { mainText, header, 0:{}, 1:{}, ... } } }
        Object.keys(data).sort().forEach(function (groupName) {
            var subgroups = data[groupName];
            Object.keys(subgroups).sort(function(a,b){ return a - b; }).forEach(function (subNum) {
                var subgroup = subgroups[subNum];
                groupsContainer.appendChild(renderSubgroup(groupName, subNum, subgroup));
            });
        });
    }

    // ── Render one subgroup panel ──────────────────────────────────
    function renderSubgroup(groupName, subNum, subgroup) {
        var mainText = (subgroup.mainText && subgroup.mainText.text_price_change) || (groupName + ' – ' + subNum);
        var header   = subgroup.header || {};

        // Which value columns are active (non-null header)?
        var cols = ['first','second','third','fourth'].filter(function(c) {
            return !!header['text_price_change_data_' + c];
        });

        // Collect product rows (entries that have an 'id' key), sort by 'order'
        var products = Object.keys(subgroup)
            .filter(function (k) { return subgroup[k] && typeof subgroup[k] === 'object' && 'id' in subgroup[k]; })
            .map(function (k) { return subgroup[k]; })
            .sort(function (a, b) { return (a.order || 0) - (b.order || 0); });

        var panel = document.createElement('div');
        panel.className = 'panel panel-bordered';

        // Panel heading
        var heading = document.createElement('div');
        heading.className = 'panel-heading';
        heading.innerHTML = '<h3 class="panel-title"><i class="fa fa-list"></i> ' + escHtml(mainText) +
            ' <small class="text-muted">(' + products.length + ' produktów)</small></h3>';
        panel.appendChild(heading);

        // Panel body → table
        var body = document.createElement('div');
        body.className = 'panel-body';
        body.style.padding = '0';

        var table = document.createElement('table');
        table.className = 'table table-hover table-condensed';
        table.style.marginBottom = '0';

        // Table header
        var thead = document.createElement('thead');
        var trH   = document.createElement('tr');
        trH.innerHTML =
            '<th style="width:22%">Produkt</th>' +
            '<th style="width:10%">Symbol</th>' +
            '<th style="width:11%">Data zmiany</th>' +
            '<th style="width:11%">Obowiązuje od</th>' +
            cols.map(function(c) {
                return '<th>' + escHtml(header['text_price_change_data_' + c] || c) + '</th>';
            }).join('') +
            '<th class="text-info">Brutto/opak.<br><small>(podgląd)</small></th>' +
            '<th class="text-info">Netto/opak.<br><small>(podgląd)</small></th>';
        thead.appendChild(trH);
        table.appendChild(thead);

        // Table body
        var tbody = document.createElement('tbody');
        products.forEach(function (p) {
            tbody.appendChild(renderProductRow(p, cols));
        });
        table.appendChild(tbody);
        body.appendChild(table);
        panel.appendChild(body);

        return panel;
    }

    // ── Render one product row ─────────────────────────────────────
    function renderProductRow(p, activeCols) {
        var tr = document.createElement('tr');
        tr.dataset.productId = p.id;

        var packUnits  = p.numbers_of_basic_commercial_units_in_pack || 1;
        var today      = formatDate(new Date());
        var dateChange = p.date_of_price_change || today;
        var dateNew    = p.date_of_the_new_prices || '';
        var priceFirst = parseFloat(p.value_of_price_change_data_first || 0);

        function calcGross(v) { return (v * packUnits * 1.23).toFixed(2); }
        function calcNet(v)   { return (v * packUnits).toFixed(2); }

        tr.innerHTML =
            '<td>' +
                '<span class="text-muted" style="font-size:11px;">' + escHtml(p.product_name_supplier_on_documents || '') + '</span><br>' +
                '<strong>' + escHtml(p.name) + '</strong>' +
            '</td>' +
            '<td><code>' + escHtml(p.symbol) + '</code></td>' +
            '<td>' +
                '<input type="date" class="form-control input-sm date-change" ' +
                    'data-field="date_of_price_change" value="' + escHtml(dateChange) + '" required>' +
            '</td>' +
            '<td>' +
                '<input type="date" class="form-control input-sm date-new" ' +
                    'data-field="date_of_the_new_prices" value="' + escHtml(dateNew) + '" required>' +
            '</td>' +
            activeCols.map(function(c) {
                var field   = 'value_of_price_change_data_' + c;
                var current = parseFloat(p[field] || 0).toFixed(2);
                var required = c === 'first' ? 'data-required="1"' : '';
                return '<td>' +
                    '<small class="text-muted" style="display:block;white-space:nowrap;">było: ' + current + '</small>' +
                    '<input type="number" class="form-control input-sm price-input" ' +
                        'data-field="' + field + '" ' + required +
                        ' value="' + current + '" step="0.01" min="0" style="width:90px;">' +
                    '</td>';
            }).join('') +
            '<td class="text-info" style="white-space:nowrap;">' +
                '<strong class="preview-gross">' + calcGross(priceFirst) + '</strong> PLN' +
                (packUnits !== 1 ? '<br><small class="text-muted">(' + packUnits + ' szt.)</small>' : '') +
            '</td>' +
            '<td style="white-space:nowrap;">' +
                '<span class="preview-net">' + calcNet(priceFirst) + '</span> PLN' +
            '</td>';

        // Store reference keyed by product id
        productRows[p.id] = { row: tr, product: p };

        // Live preview update on first price input
        var firstInput = tr.querySelector('[data-required="1"]');
        if (firstInput) {
            firstInput.addEventListener('input', function () {
                var v = parseFloat(this.value.replace(',', '.')) || 0;
                tr.querySelector('.preview-gross').textContent = calcGross(v);
                tr.querySelector('.preview-net').textContent   = calcNet(v);
            });
        }

        // Cross-validate date_new >= date_change
        var dateChangeInput = tr.querySelector('.date-change');
        var dateNewInput    = tr.querySelector('.date-new');
        dateNewInput.addEventListener('change', function() {
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
            var firstVal   = firstInput ? parseFloat(firstInput.value) : 0;

            if (!dateChange) {
                errors.push('Produkt ID ' + pid + ': brak daty zmiany ceny.');
            }
            if (!dateNew) {
                errors.push('Produkt ID ' + pid + ': brak daty obowiązywania nowych cen.');
            }
            if (dateChange && dateNew && dateNew < dateChange) {
                errors.push('Produkt ID ' + pid + ': data obowiązywania musi być >= dacie zmiany.');
            }
            if (firstInput && (isNaN(firstVal) || firstVal <= 0)) {
                errors.push('Produkt ID ' + pid + ': główna cena musi być większa od 0.');
            }

            var item = { id: pid, date_of_price_change: dateChange, date_of_the_new_prices: dateNew };
            tr.querySelectorAll('.price-input').forEach(function(input) {
                item[input.dataset.field] = parseFloat(input.value) || 0;
            });
            payload.push(item);
        });

        if (errors.length) {
            showAlert('<strong>Popraw błędy:</strong><ul>' + errors.map(function(e){ return '<li>' + escHtml(e) + '</li>'; }).join('') + '</ul>', 'danger');
            window.scrollTo(0, 0);
            return;
        }

        showAlert('');
        saveBtn.disabled          = true;
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
                saveBtn.disabled           = false;
                saveIndicator.style.display = 'none';
            });
    });

    // ── Helpers ────────────────────────────────────────────────────
    function validateDates(changeInput, newInput) {
        if (changeInput.value && newInput.value && newInput.value < changeInput.value) {
            newInput.setCustomValidity('Data obowiązywania musi być >= dacie zmiany.');
            newInput.style.borderColor = '#d9534f';
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
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showAlert(msg, type) {
        if (!msg) { alertArea.innerHTML = ''; return; }
        alertArea.innerHTML = '<div class="alert alert-' + (type || 'info') + '">' + msg + '</div>';
    }

})();
</script>
@endsection

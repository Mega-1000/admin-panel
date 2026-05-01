@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-list"></i> Kategorie
        <a href="{{ route('categories.create') }}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>Dodaj kategorię</span>
        </a>
    </h1>
@endsection

@section('app-content')
    <div class="container-fluid">
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type', 'info') }}">
                {{ session('message') }}
            </div>
        @endif

        <div class="panel panel-bordered">
            <div class="panel-body">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                    <p class="text-muted" style="margin:0;">Drzewo kategorii — dowolna liczba poziomów.</p>
                    <div style="margin-left:auto; position:relative;">
                        <i class="fa fa-search" style="position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#aaa; font-size:12px; pointer-events:none;"></i>
                        <input type="text" id="cat-search" placeholder="Szukaj kategorii…"
                               style="padding:6px 28px 6px 28px; border:1px solid #ccc; border-radius:4px; font-size:13px; width:240px; outline:none;"
                               onfocus="this.style.borderColor='#3a5bd9'" onblur="this.style.borderColor='#ccc'">
                        <button id="cat-search-clear" style="display:none; position:absolute; right:7px; top:50%; transform:translateY(-50%); background:none; border:none; color:#aaa; cursor:pointer; font-size:16px; padding:0; line-height:1;">&times;</button>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width:55px">ID</th>
                            <th style="width:40%">Nazwa</th>
                            <th>Widoczna</th>
                            <th>Priorytet</th>
                            <th>Produkty</th>
                            <th>YouTube</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roots as $root)
                            @include('categories._row', [
                                'category' => $root,
                                'depth'    => 1,
                                'parentId' => 0,
                            ])
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Brak kategorii.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Modal usuwania kategorii ──────────────────────────────────────────── --}}
    <div id="cat-delete-modal" style="display:none" aria-modal="true" role="dialog">
        <div class="cdm-backdrop"></div>
        <div class="cdm-card">
            <div class="cdm-icon-wrap">
                <svg class="cdm-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#FEE2E2"/>
                    <path d="M9 3h6l1 2H8L9 3z" fill="#EF4444"/>
                    <rect x="4" y="5" width="16" height="2" rx="1" fill="#EF4444"/>
                    <path d="M6 7h12l-1.2 13.1A1 1 0 0 1 15.8 21H8.2a1 1 0 0 1-1-0.9L6 7z" fill="#EF4444" opacity=".85"/>
                    <line x1="10" y1="11" x2="10" y2="17" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="14" y1="11" x2="14" y2="17" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <h3 class="cdm-title">Usuń kategorię</h3>
            <p class="cdm-body">
                Czy na pewno chcesz usunąć kategorię<br>
                <strong id="cdm-cat-name" class="cdm-name"></strong>?
            </p>
            <p id="cdm-children-note" class="cdm-note" style="display:none">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="vertical-align:-2px;margin-right:4px;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-3a1 1 0 00-1 1v.5a1 1 0 002 0V11a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Wraz z nią zostaną usunięte wszystkie podkategorie.
            </p>
            <p class="cdm-warning">Tej operacji nie można cofnąć.</p>
            <div class="cdm-actions">
                <button type="button" class="cdm-btn-cancel" id="cdm-cancel">Anuluj</button>
                <button type="button" class="cdm-btn-confirm" id="cdm-confirm">
                    <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor" style="vertical-align:-2px;margin-right:5px;"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"/></svg>
                    Usuń kategorię
                </button>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
.cat-id {
    display: inline-block;
    font-family: monospace;
    font-size: 13px;
    font-weight: 700;
    color: #555;
    background: #eef0f5;
    border: 1px solid #d5d8e0;
    border-radius: 3px;
    padding: 1px 6px;
    cursor: pointer;
    user-select: all;
    white-space: nowrap;
}
.cat-id:hover { background: #dde2f0; border-color: #3a5bd9; color: #3a5bd9; }
.cat-id.copied { background: #d4edda; border-color: #28a745; color: #28a745; }

.expand-toggle {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 8px;
    padding: 2px 9px;
    font-size: 12px;
    color: #666;
    background: #f0f2f7;
    border: 1px solid #d0d4de;
    border-radius: 20px;
    cursor: pointer;
    user-select: none;
    transition: background .15s, color .15s, border-color .15s;
    vertical-align: middle;
}
.expand-toggle:hover {
    background: #e2e6f3;
    border-color: #a0a8c8;
    color: #333;
}
.expand-toggle.open {
    background: #3a5bd9;
    border-color: #2a4bbf;
    color: #fff;
}
.expand-toggle .fa {
    font-size: 10px;
    transition: transform .2s;
}
.expand-toggle.open .fa {
    transform: rotate(90deg);
}
.category-row.level-2 { background: #fafafa; }
.category-row.level-3 { background: #f4f4f4; }
.category-row.level-4 { background: #eef0f5; }
.category-row.level-5 { background: #e8eaf2; }

/* ── Delete modal ───────────────────────────────────────────────────────── */
#cat-delete-modal { position:fixed; inset:0; z-index:9990; display:flex; align-items:center; justify-content:center; }
.cdm-backdrop {
    position:fixed; inset:0; background:rgba(20,24,40,.45);
    backdrop-filter:blur(3px);
    animation:cdm-bg-in .18s ease both;
}
.cdm-card {
    position:relative; z-index:1;
    background:#fff; border-radius:16px;
    box-shadow:0 24px 60px rgba(0,0,0,.18);
    padding:36px 40px 32px;
    width:420px; max-width:92vw;
    text-align:center;
    animation:cdm-in .2s cubic-bezier(.34,1.28,.64,1) both;
}
.cdm-icon-wrap { margin-bottom:16px; }
.cdm-icon { width:64px; height:64px; }
.cdm-title {
    font-size:20px; font-weight:700; color:#111827;
    margin:0 0 10px;
}
.cdm-body {
    font-size:14px; color:#4b5563; line-height:1.6;
    margin:0 0 10px;
}
.cdm-name {
    display:inline-block; margin-top:4px;
    font-size:15px; color:#111827;
    word-break:break-word;
}
.cdm-note {
    font-size:12px; color:#b45309;
    background:#fffbeb; border:1px solid #fde68a;
    border-radius:6px; padding:7px 12px;
    margin:0 0 10px; text-align:left;
}
.cdm-warning {
    font-size:12px; color:#9ca3af; margin:0 0 24px;
}
.cdm-actions { display:flex; gap:10px; }
.cdm-btn-cancel {
    flex:1; padding:10px 0; border-radius:8px;
    border:1px solid #e5e7eb; background:#f9fafb;
    font-size:14px; font-weight:600; color:#374151;
    cursor:pointer; transition:background .15s, border-color .15s;
}
.cdm-btn-cancel:hover { background:#f3f4f6; border-color:#d1d5db; }
.cdm-btn-confirm {
    flex:1; padding:10px 0; border-radius:8px;
    border:none; background:#ef4444;
    font-size:14px; font-weight:600; color:#fff;
    cursor:pointer; transition:background .15s, transform .1s;
    display:flex; align-items:center; justify-content:center;
}
.cdm-btn-confirm:hover { background:#dc2626; }
.cdm-btn-confirm:active { transform:scale(.97); }

@keyframes cdm-bg-in { from { opacity:0; } to { opacity:1; } }
@keyframes cdm-in {
    from { opacity:0; transform:scale(.88) translateY(12px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
.cdm-out { animation:cdm-out .16s ease both; }
@keyframes cdm-out {
    to { opacity:0; transform:scale(.92) translateY(8px); }
}
</style>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.cat-id').forEach(function(el) {
        el.title = 'Kliknij aby skopiować ID';
        el.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent.trim()).then(() => {
                this.classList.add('copied');
                var orig = this.textContent;
                this.textContent = '✓ ' + orig;
                setTimeout(() => { this.textContent = orig; this.classList.remove('copied'); }, 1200);
            });
        });
    });

    document.querySelectorAll('.expand-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var id = this.dataset.id;
            var rows = document.querySelectorAll('.children-of-' + id);
            var isOpen = this.classList.contains('open');

            if (isOpen) {
                // collapse: hide this level AND any deeper levels that were open inside it
                rows.forEach(function(row) {
                    row.style.display = 'none';
                    // also collapse any open toggles inside these rows
                    row.querySelectorAll('.expand-toggle.open').forEach(function(inner) {
                        inner.classList.remove('open');
                        document.querySelectorAll('.children-of-' + inner.dataset.id).forEach(function(r) {
                            r.style.display = 'none';
                        });
                    });
                });
                this.classList.remove('open');
            } else {
                rows.forEach(function(row) { row.style.display = ''; });
                this.classList.add('open');
            }
        });
    });

    // Category search
    (function() {
        var searchInput = document.getElementById('cat-search');
        var clearBtn    = document.getElementById('cat-search-clear');

        function getAncestorRows(row) {
            var ancestors = [];
            var classes = Array.from(row.classList);
            var childrenOfClass = classes.find(function(c) { return c.startsWith('children-of-'); });
            if (!childrenOfClass) return ancestors;
            var parentId = childrenOfClass.replace('children-of-', '');
            if (!parentId || parentId === '0') return ancestors;
            var parentRow = document.querySelector('.category-row[data-id="' + parentId + '"]');
            if (parentRow) {
                ancestors.push(parentRow);
                ancestors = ancestors.concat(getAncestorRows(parentRow));
            }
            return ancestors;
        }

        function applySearch(query) {
            var allRows = document.querySelectorAll('.category-row');
            clearBtn.style.display = query ? '' : 'none';

            if (!query) {
                // Restore default state: only level-1 visible, all toggles closed
                allRows.forEach(function(row) {
                    row.style.display = row.classList.contains('level-1') ? '' : 'none';
                });
                document.querySelectorAll('.expand-toggle.open').forEach(function(t) {
                    t.classList.remove('open');
                });
                return;
            }

            var q = query.toLowerCase();
            var toShow = new Set();

            allRows.forEach(function(row) {
                var nameCell = row.querySelector('td:nth-child(2)');
                var name = nameCell ? nameCell.textContent.trim().toLowerCase() : '';
                if (name.indexOf(q) !== -1) {
                    toShow.add(row);
                    getAncestorRows(row).forEach(function(a) { toShow.add(a); });
                }
            });

            allRows.forEach(function(row) {
                row.style.display = toShow.has(row) ? '' : 'none';
            });

            // Mark toggles as open if their children are shown
            document.querySelectorAll('.expand-toggle').forEach(function(toggle) {
                var id = toggle.dataset.id;
                var hasVisibleChild = document.querySelector('.children-of-' + id + '[style*="display: ;"], .children-of-' + id + ':not([style*="none"])');
                // Simpler: check if any shown child row has children-of-{id}
                var anyShown = Array.from(document.querySelectorAll('.children-of-' + id)).some(function(r) { return r.style.display !== 'none'; });
                if (anyShown) {
                    toggle.classList.add('open');
                } else {
                    toggle.classList.remove('open');
                }
            });
        }

        searchInput.addEventListener('input', function() { applySearch(this.value.trim()); });
        clearBtn.addEventListener('click', function() { searchInput.value = ''; applySearch(''); searchInput.focus(); });
    }());

    // ── Delete modal ────────────────────────────────────────────────────────
    (function () {
        var modal       = document.getElementById('cat-delete-modal');
        var nameEl      = document.getElementById('cdm-cat-name');
        var noteEl      = document.getElementById('cdm-children-note');
        var confirmBtn  = document.getElementById('cdm-confirm');
        var cancelBtn   = document.getElementById('cdm-cancel');
        var card        = modal ? modal.querySelector('.cdm-card') : null;
        var pendingForm = null;

        function openModal(btn) {
            pendingForm = document.getElementById(btn.dataset.form);
            nameEl.textContent   = btn.dataset.name;
            noteEl.style.display = parseInt(btn.dataset.children, 10) > 0 ? '' : 'none';
            card.classList.remove('cdm-out');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            confirmBtn.focus();
        }

        function closeModal() {
            card.classList.add('cdm-out');
            setTimeout(function () {
                modal.style.display = 'none';
                card.classList.remove('cdm-out');
                document.body.style.overflow = '';
                pendingForm = null;
            }, 160);
        }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.cat-delete-btn');
            if (btn) { openModal(btn); return; }
            if (e.target === modal || e.target.closest('.cdm-backdrop')) { closeModal(); return; }
        });

        cancelBtn.addEventListener('click', closeModal);
        confirmBtn.addEventListener('click', function () {
            if (pendingForm) pendingForm.submit();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
        });
    }());
</script>
@endsection

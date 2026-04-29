{{-- Shared form partial for create and edit --}}
@php $category = $category ?? null; @endphp

<div class="form-group">
    <label for="name">Nazwa <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="name" maxlength="191"
           value="{{ old('name', $category?->name) }}" required>
    <small class="text-muted">Maksymalnie 191 znaków.</small>
</div>

<div class="form-group">
    <label for="description">Opis</label>
    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $category?->description) }}</textarea>
</div>

<div class="form-group">
    <label for="parent_id">Kategoria nadrzędna</label>
    <select class="selectpicker form-control" id="parent_id" name="parent_id"
            data-live-search="true"
            data-live-search-placeholder="Szukaj kategorii…"
            data-size="12"
            data-none-selected-text="— brak (kategoria główna) —"
            title="— brak (kategoria główna) —">
        @foreach($parents as $pid => $pname)
            @php
                $depth     = substr_count($pname, '— ');
                $label     = e(trim(str_replace('— ', '', $pname)));
                $pad       = str_repeat('<span style="display:inline-block;width:14px"></span>', $depth);
                $icon      = $depth === 0
                    ? '<i class="fa fa-folder" style="color:#3a5bd9;margin-right:6px;font-size:12px;"></i>'
                    : str_repeat('<i class="fa fa-angle-right" style="color:#bbb;font-size:10px;margin-right:2px;"></i>', $depth)
                      . '<span style="margin-right:4px;"></span>';
                $dataContent = $pad . $icon . $label;
            @endphp
            <option value="{{ $pid }}"
                    data-content="{{ $dataContent }}"
                    {{ old('parent_id', $category?->parent_id) == $pid ? 'selected' : '' }}>{{ $pname }}</option>
        @endforeach
    </select>
    <small class="text-muted">Zostaw puste, aby stworzyć kategorię główną.</small>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="priority">Priorytet (kolejność)</label>
            <input type="number" class="form-control" id="priority" name="priority" min="0"
                   value="{{ old('priority', $category?->priority ?? 0) }}">
            <small class="text-muted">Niższa liczba = wyżej na liście.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Zdjęcie kategorii</label>

            {{-- Thumbnail preview --}}
            <div id="imgPreviewBox" style="{{ old('img', $category?->img) ? '' : 'display:none;' }} margin-bottom:8px;">
                <div style="position:relative; display:inline-block; max-width:100%;">
                    <img id="imgPreview" src="{{ old('img', $category?->img) }}"
                         style="max-height:90px; max-width:100%; border:1px solid #ddd; border-radius:4px; padding:3px; display:block; background:#fafafa;">
                    <button type="button" id="imgClearBtn"
                            title="Usuń zdjęcie"
                            style="position:absolute; top:-7px; right:-7px; background:#c0392b; border:none; border-radius:50%; width:20px; height:20px; color:#fff; font-size:12px; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; box-shadow:0 1px 4px rgba(0,0,0,.3);">
                        &times;
                    </button>
                </div>
            </div>

            {{-- Pick / URL row --}}
            <div style="display:flex; gap:6px; align-items:stretch;">
                <input type="text" class="form-control" id="img" name="img" maxlength="191"
                       value="{{ old('img', $category?->img) }}" placeholder="/storage/…"
                       style="font-size:12px; flex:1; min-width:0;">
                <button type="button" id="mmBrowseBtn"
                        style="flex-shrink:0; background:#3a5bd9; color:#fff; border:none; border-radius:4px; padding:6px 12px; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:background .15s; white-space:nowrap;"
                        onmouseover="this.style.background='#2a4bbf'" onmouseout="this.style.background='#3a5bd9'">
                    <i class="fa fa-picture-o"></i> Wybierz
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="is_visible" value="0">
                <input type="checkbox" name="is_visible" value="1"
                       {{ old('is_visible', $category?->is_visible ?? true) ? 'checked' : '' }}>
                Widoczna na stronie
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="save_name" value="0">
                <input type="checkbox" name="save_name" value="1"
                       {{ old('save_name', $category?->save_name ?? true) ? 'checked' : '' }}>
                Zachowaj nazwę
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="save_description" value="0">
                <input type="checkbox" name="save_description" value="1"
                       {{ old('save_description', $category?->save_description ?? true) ? 'checked' : '' }}>
                Zachowaj opis
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>
                <input type="hidden" name="save_image" value="0">
                <input type="checkbox" name="save_image" value="1"
                       {{ old('save_image', $category?->save_image ?? true) ? 'checked' : '' }}>
                Zachowaj zdjęcie
            </label>
        </div>
    </div>
</div>

{{-- YouTube section --}}
<hr>
<h4>Filmy YouTube <small class="text-muted">(maksymalnie 10)</small></h4>
<div id="youtube-entries">
    @php $ytItems = old('youtube', $youtube ?? []); @endphp
    @foreach($ytItems as $index => $yt)
        <div class="youtube-entry panel panel-default" style="padding:10px; margin-bottom:10px;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom:5px;">
                        <label>Link YouTube <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="youtube[{{ $index }}][link]"
                               maxlength="191" value="{{ $yt['link'] ?? '' }}"
                               placeholder="https://youtu.be/...">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group" style="margin-bottom:5px;">
                        <label>Opis</label>
                        <input type="text" class="form-control" name="youtube[{{ $index }}][description]"
                               maxlength="500" value="{{ $yt['description'] ?? '' }}"
                               placeholder="Opis filmu (opcjonalnie)">
                    </div>
                </div>
                <div class="col-md-1" style="padding-top:25px;">
                    <button type="button" class="btn btn-danger btn-sm remove-youtube">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-youtube" class="btn btn-default btn-sm">
    <i class="fa fa-plus"></i> Dodaj film
</button>

<template id="youtube-template">
    <div class="youtube-entry panel panel-default" style="padding:10px; margin-bottom:10px;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group" style="margin-bottom:5px;">
                    <label>Link YouTube <span class="text-danger">*</span></label>
                    <input type="url" class="form-control" name="youtube[__INDEX__][link]"
                           maxlength="191" placeholder="https://youtu.be/...">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group" style="margin-bottom:5px;">
                    <label>Opis</label>
                    <input type="text" class="form-control" name="youtube[__INDEX__][description]"
                           maxlength="500" placeholder="Opis filmu (opcjonalnie)">
                </div>
            </div>
            <div class="col-md-1" style="padding-top:25px;">
                <button type="button" class="btn btn-danger btn-sm remove-youtube">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Mini media manager modal --}}
<div id="mmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:8px; width:860px; max-width:95vw; height:560px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 8px 32px rgba(0,0,0,.3);">
        <div style="padding:10px 16px; border-bottom:1px solid #eee; display:flex; align-items:center; gap:10px; flex-shrink:0;">
            <span style="font-weight:600; font-size:15px; flex:1;"><i class="fa fa-folder-open-o"></i> Wybierz zdjęcie</span>
            <div style="position:relative;">
                <input type="text" id="mmSearch" placeholder="Szukaj…"
                       style="padding:5px 28px 5px 8px; border:1px solid #ccc; border-radius:4px; font-size:13px; width:200px; outline:none;">
                <i class="fa fa-search" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#aaa; pointer-events:none; font-size:12px;"></i>
            </div>
            <button type="button" id="mmClose" style="background:none; border:none; font-size:22px; cursor:pointer; color:#666; padding:0 4px; line-height:1;">&times;</button>
        </div>
        <div style="display:flex; flex:1; overflow:hidden;">
            <div id="mmSidebar" style="width:180px; flex-shrink:0; background:#f8f9fb; border-right:1px solid #eee; overflow-y:auto; padding:8px 0; font-size:13px;">
                <div style="padding:4px 12px 2px; font-size:11px; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.05em;">Ulubione</div>
                <div id="mmFavList"></div>
                <div style="padding:4px 12px 2px; margin-top:8px; font-size:11px; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.05em;">Katalogi</div>
                <div class="mm-fav-item" data-path="" style="display:flex; align-items:center; gap:6px; padding:5px 14px; color:#444; cursor:pointer;">
                    <i class="fa fa-home" style="font-size:12px; color:#aaa;"></i> Główny
                </div>
            </div>
            <div id="mmGrid" style="flex:1; overflow-y:auto; padding:10px; display:flex; flex-wrap:wrap; gap:8px; align-content:start;"></div>
        </div>
        <div style="padding:8px 14px; border-top:1px solid #eee; display:flex; align-items:center; gap:10px; flex-shrink:0; min-height:44px;">
            <span id="mmSelName" style="flex:1; font-size:13px; color:#888; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Nic nie wybrano</span>
            <button type="button" id="mmSelectBtn" class="btn btn-primary btn-sm" disabled>
                <i class="fa fa-check"></i> Wybierz
            </button>
            <button type="button" id="mmCancelBtn" class="btn btn-default btn-sm">Anuluj</button>
        </div>
    </div>
</div>

<script>
(function () {
    // ── Thumbnail preview ─────────────────────────────────────────────────────
    function updateImgPreview(val) {
        var box      = document.getElementById('imgPreviewBox');
        var imgEl    = document.getElementById('imgPreview');
        if (!box || !imgEl) return;
        if (val && val.trim()) {
            imgEl.src     = val.trim();
            imgEl.onerror = function() { box.style.display = 'none'; };
            imgEl.onload  = function() { box.style.display = ''; };
            box.style.display = '';
        } else {
            box.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var imgInput = document.getElementById('img');
        if (imgInput) {
            imgInput.addEventListener('input', function () { updateImgPreview(this.value); });
        }
        var clearBtn = document.getElementById('imgClearBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('img').value = '';
                updateImgPreview('');
            });
        }

        // Bootstrap-select for parent dropdown
        if (typeof $.fn.selectpicker !== 'undefined') {
            $('#parent_id').selectpicker('refresh');
        }
    });

    // ── Mini media manager ────────────────────────────────────────────────────
    var mmState = { path: '', items: [], favs: [], selected: null };

    function mmIsImage(ext) {
        return ['jpg','jpeg','png','gif','webp','svg','bmp'].indexOf((ext||'').toLowerCase()) !== -1;
    }

    function mmLoad(path) {
        var grid = document.getElementById('mmGrid');
        grid.innerHTML = '<div style="width:100%;text-align:center;padding:40px;color:#aaa;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';
        mmState.selected = null;
        document.getElementById('mmSelName').textContent = 'Nic nie wybrano';
        document.getElementById('mmSelectBtn').disabled = true;

        fetch('{{ route('file-manager.list') }}?path=' + encodeURIComponent(path), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            mmState.path = path;
            mmState.items = data.items || [];
            mmState.favs  = data.favorites || [];
            mmRenderSidebar();
            mmRenderGrid();
        })
        .catch(function() {
            grid.innerHTML = '<div style="width:100%;text-align:center;padding:40px;color:#c00;">Błąd ładowania.</div>';
        });
    }

    function mmRenderSidebar() {
        var favList = document.getElementById('mmFavList');
        if (!mmState.favs.length) {
            favList.innerHTML = '<div style="padding:4px 14px;font-size:12px;color:#bbb;">Brak ulubionych</div>';
        } else {
            favList.innerHTML = mmState.favs.map(function(fav) {
                var name = fav.split('/').pop() || fav;
                var active = mmState.path === fav;
                return '<div class="mm-fav-item" data-path="' + fav + '" style="display:flex;align-items:center;gap:6px;padding:5px 14px;cursor:pointer;'
                    + (active ? 'color:#3a5bd9;background:#e6eaf5;font-weight:600;' : 'color:#444;')
                    + '"><i class="fa fa-folder" style="font-size:12px;color:#f5a623;"></i>' + name + '</div>';
            }).join('');
        }
        // Highlight "Główny" if at root
        var rootEl = document.querySelector('#mmSidebar .mm-fav-item[data-path=""]');
        if (rootEl) {
            rootEl.style.color = mmState.path === '' ? '#3a5bd9' : '#444';
            rootEl.style.background = mmState.path === '' ? '#e6eaf5' : '';
            rootEl.style.fontWeight = mmState.path === '' ? '600' : '';
        }
    }

    function mmRenderGrid() {
        var grid = document.getElementById('mmGrid');
        var search = (document.getElementById('mmSearch').value || '').trim().toLowerCase();
        var filtered = search
            ? mmState.items.filter(function(i) { return i.name.toLowerCase().indexOf(search) !== -1; })
            : mmState.items;

        if (!filtered.length) {
            grid.innerHTML = '<div style="width:100%;text-align:center;padding:40px;color:#bbb;font-size:14px;">'
                + (search ? 'Brak wyników dla "' + search + '"' : 'Folder jest pusty') + '</div>';
            return;
        }

        grid.innerHTML = filtered.map(function(item) {
            var sel = mmState.selected && mmState.selected.path === item.path;
            var thumb;
            if (item.is_dir) {
                thumb = '<div style="width:72px;height:60px;display:flex;align-items:center;justify-content:center;font-size:36px;color:#f5a623;"><i class="fa fa-folder"></i></div>';
            } else if (mmIsImage(item.ext)) {
                thumb = '<img src="' + item.url + '" style="width:72px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #eee;" loading="lazy" onerror="this.style.display=\'none\'">';
            } else {
                thumb = '<div style="width:72px;height:60px;display:flex;align-items:center;justify-content:center;font-size:36px;color:#bbb;"><i class="fa fa-file-o"></i></div>';
            }
            return '<div class="mm-item" data-path="' + item.path + '" data-isdir="' + (item.is_dir ? '1' : '0') + '" data-isimg="' + (mmIsImage(item.ext) ? '1' : '0') + '"'
                + ' style="display:flex;flex-direction:column;align-items:center;padding:8px 6px 6px;border-radius:6px;cursor:pointer;width:100px;'
                + (sel ? 'border:2px solid #3a5bd9;background:#e6eaf5;' : 'border:1px solid transparent;')
                + '">' + thumb
                + '<span style="font-size:11px;text-align:center;color:#333;word-break:break-all;line-height:1.3;max-height:32px;overflow:hidden;margin-top:4px;">'
                + item.name + '</span></div>';
        }).join('');
    }

    // Event handling via delegation
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('mmModal');

        // Open modal
        var browseBtn = e.target.id === 'mmBrowseBtn' ? e.target : e.target.closest('#mmBrowseBtn');
        if (browseBtn) {
            modal.style.display = 'flex';
            document.getElementById('mmSearch').value = '';
            mmLoad(mmState.path);
            return;
        }
        if (!modal || modal.style.display === 'none') return;

        // Close on backdrop
        if (e.target === modal) { modal.style.display = 'none'; return; }

        // Close / Cancel
        if (e.target.id === 'mmClose' || e.target.id === 'mmCancelBtn' || e.target.closest('#mmClose') || e.target.closest('#mmCancelBtn')) {
            modal.style.display = 'none'; return;
        }

        // Select button
        if ((e.target.id === 'mmSelectBtn' || e.target.closest('#mmSelectBtn')) && mmState.selected) {
            var val = '/storage/' + mmState.selected.path;
            document.getElementById('img').value = val;
            updateImgPreview(val);
            modal.style.display = 'none'; return;
        }

        // Sidebar favorite/root item
        var favItem = e.target.closest('.mm-fav-item');
        if (favItem && favItem.closest('#mmSidebar')) {
            var p = favItem.dataset.path;
            mmLoad(p !== undefined ? p : ''); return;
        }

        // Grid item
        var gridItem = e.target.closest('.mm-item');
        if (gridItem && gridItem.closest('#mmGrid')) {
            if (gridItem.dataset.isdir === '1') {
                mmLoad(gridItem.dataset.path);
            } else {
                mmState.selected = mmState.items.find(function(i) { return i.path === gridItem.dataset.path; }) || null;
                document.getElementById('mmSelName').textContent = mmState.selected ? mmState.selected.name : 'Nic nie wybrano';
                document.getElementById('mmSelectBtn').disabled = !mmState.selected;
                mmRenderGrid();
            }
            return;
        }
    });

    // Double-click to select immediately
    document.addEventListener('dblclick', function(e) {
        var modal = document.getElementById('mmModal');
        if (!modal || modal.style.display === 'none') return;
        var gridItem = e.target.closest('.mm-item');
        if (gridItem && gridItem.dataset.isdir === '0' && gridItem.dataset.isimg === '1') {
            var item = mmState.items.find(function(i) { return i.path === gridItem.dataset.path; });
            if (item) {
                var v = '/storage/' + item.path;
                document.getElementById('img').value = v;
                updateImgPreview(v);
                modal.style.display = 'none';
            }
        }
    });

    // Search input
    document.addEventListener('input', function(e) {
        if (e.target.id === 'mmSearch') mmRenderGrid();
    });

    // ESC closes modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var modal = document.getElementById('mmModal');
            if (modal && modal.style.display !== 'none') modal.style.display = 'none';
        }
    });
}());
</script>

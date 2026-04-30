@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="fa fa-folder-open"></i> Menedżer plików
    </h1>
@endsection

@section('app-content')
<style>
/* ── Layout ─────────────────────────────────────────────────────────────── */
.fm-wrap        { display:flex; gap:0; height:calc(100vh - 180px); min-height:500px; background:#fff; border:1px solid #dde1e7; border-radius:6px; overflow:hidden; }
.fm-sidebar     { width:220px; flex-shrink:0; background:#f8f9fb; border-right:1px solid #dde1e7; display:flex; flex-direction:column; overflow-y:auto; }
.fm-sidebar-sec { padding:10px 12px 4px; font-size:11px; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.05em; }
.fm-fav-item    { display:flex; align-items:center; gap:6px; padding:5px 14px; font-size:13px; color:#444; cursor:pointer; border-radius:4px; margin:1px 6px; transition:background .15s; }
.fm-fav-item:hover  { background:#eef0f5; }
.fm-fav-item.active { background:#e6eaf5; color:#3a5bd9; font-weight:600; }
.fm-fav-item i  { font-size:12px; color:#aaa; }
.fm-main        { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.fm-toolbar     { display:flex; align-items:center; gap:8px; padding:10px 14px; border-bottom:1px solid #eee; flex-wrap:wrap; }
.fm-toolbar .btn { padding:5px 12px; font-size:12px; }
.btn-fm-outline { background:#fff; border:1px solid #555; color:#333; border-radius:4px; padding:5px 12px; font-size:12px; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:5px; line-height:1.4; }
.btn-fm-outline:hover { background:#f5f5f5; border-color:#222; color:#222; }
.fm-nav         { display:flex; align-items:center; gap:8px; padding:7px 14px; background:#fafbfc; border-bottom:1px solid #eee; min-height:42px; }
.fm-nav-back    { background:#fff; border:1px solid #aaa; color:#444; border-radius:4px; padding:4px 11px; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:all .15s; line-height:1.4; }
.fm-nav-back:hover { background:#f0f3ff; border-color:#3a5bd9; color:#3a5bd9; }
.fm-nav-title   { font-size:14px; font-weight:600; color:#333; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.fm-nav-fav     { background:#fff; border:1px solid #ccc; border-radius:4px; padding:4px 9px; font-size:12px; color:#aaa; cursor:pointer; transition:all .15s; line-height:1.4; display:inline-flex; align-items:center; gap:5px; }
.fm-nav-fav:hover  { border-color:#f5a623; color:#f5a623; }
.fm-nav-fav.active { border-color:#e6a010; color:#e6a010; background:#fff9e6; }
.fm-grid        { flex:1; overflow-y:auto; padding:12px; display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:10px; align-content:start; }

/* ── Grid items ──────────────────────────────────────────────────────────── */
.fm-item        { display:flex; flex-direction:column; align-items:center; padding:8px 6px 6px; border:1px solid transparent; border-radius:6px; cursor:pointer; transition:all .15s; position:relative; min-width:0; }
.fm-item:hover  { background:#f0f3ff; border-color:#c5ceee; }
.fm-item.selected { background:#e6eaf5; border-color:#3a5bd9; }
.fm-item.is-checked { background:#e8edf9; border-color:#3a5bd9 !important; box-shadow:0 0 0 2px #3a5bd940; }
.fm-item-thumb  { width:72px; height:60px; object-fit:cover; border-radius:4px; margin-bottom:6px; border:1px solid #eee; }
.fm-item-icon   { width:72px; height:60px; display:flex; align-items:center; justify-content:center; font-size:36px; margin-bottom:6px; }
.fm-item-icon.dir  { color:#f5a623; }
.fm-item-icon.pdf  { color:#e04040; }
.fm-item-icon.xls  { color:#1a7a4a; }
.fm-item-icon.doc  { color:#2b5fbd; }
.fm-item-icon.other{ color:#888; }
.fm-item-name   { font-size:11px; text-align:center; color:#333; word-break:break-all; line-height:1.3; max-height:32px; overflow:hidden; }
.fm-item-fav    { position:absolute; top:3px; right:4px; font-size:11px; cursor:pointer; opacity:.4; }
.fm-item-fav:hover  { opacity:1; }
.fm-item-fav.active { opacity:1; color:#f5a623; }

/* ── Multi-select checkbox ───────────────────────────────────────────────── */
.fm-item-cb     { position:absolute; top:3px; left:4px; font-size:15px; cursor:pointer; color:#ccc; opacity:.35; transition:opacity .15s, color .15s; z-index:2; line-height:1; }
.fm-item:hover .fm-item-cb  { opacity:1; color:#888; }
.fm-item.is-checked .fm-item-cb { opacity:1; color:#3a5bd9; }
.fm-batch-bar   { display:flex; align-items:center; gap:8px; padding:6px 14px; background:#eef1fb; border-bottom:1px solid #c8d0f0; font-size:12px; }

/* ── Selected-file info bar ──────────────────────────────────────────────── */
.fm-selected-bar { display:flex; align-items:center; gap:10px; padding:5px 14px; background:#fff9e6; border-bottom:1px solid #f0d98a; font-size:12px; flex-wrap:wrap; min-height:32px; }
.fm-selected-bar .fm-sel-name { font-weight:700; color:#333; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.fm-selected-bar .fm-sel-sep  { color:#ccc; }
.fm-selected-bar a            { color:#3a5bd9; text-decoration:none; }
.fm-selected-bar a:hover      { text-decoration:underline; }
.fm-count { margin-left:auto; color:#aaa; font-size:11px; }

/* ── Modals ───────────────────────────────────────────────────────────────── */
.fm-modal-bg    { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; display:flex; align-items:center; justify-content:center; }
.fm-modal       { background:#fff; border-radius:8px; padding:24px 28px; min-width:320px; box-shadow:0 8px 32px rgba(0,0,0,.18); }
.fm-modal h4    { margin:0 0 16px; font-size:16px; }
.fm-modal input { width:100%; padding:7px 10px; border:1px solid #ccc; border-radius:4px; font-size:13px; margin-bottom:14px; }

/* ── Conflict modal ───────────────────────────────────────────────────────── */
.fm-conflict-list { max-height:180px; overflow-y:auto; margin:0 0 16px; padding:0; list-style:none; border:1px solid #eee; border-radius:4px; }
.fm-conflict-list li { display:flex; align-items:center; gap:8px; padding:6px 12px; border-bottom:1px solid #f5f5f5; font-size:13px; }
.fm-conflict-list li:last-child { border-bottom:none; }
.fm-conflict-list li i { color:#f5a623; }

/* ── Upload progress panel ───────────────────────────────────────────────── */
.fm-up-panel    { position:fixed; bottom:20px; right:24px; width:340px; background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,.22); z-index:1500; overflow:hidden; transition:height .2s; }
.fm-up-header   { padding:10px 14px; background:#3a5bd9; color:#fff; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; cursor:pointer; user-select:none; }
.fm-up-header .fm-up-summary { flex:1; }
.fm-up-header button { background:none; border:none; color:#fff; cursor:pointer; font-size:16px; line-height:1; padding:0 2px; opacity:.8; }
.fm-up-header button:hover { opacity:1; }
.fm-up-list     { max-height:260px; overflow-y:auto; }
.fm-up-item     { padding:8px 14px 6px; border-bottom:1px solid #f2f2f2; }
.fm-up-item-row { display:flex; align-items:center; gap:7px; margin-bottom:4px; }
.fm-up-item-name{ flex:1; font-size:12px; color:#333; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.fm-up-pct      { font-size:11px; color:#888; width:32px; text-align:right; flex-shrink:0; }
.fm-up-bar      { height:3px; background:#e8e8e8; border-radius:2px; overflow:hidden; }
.fm-up-bar-fill { height:100%; border-radius:2px; transition:width .25s; }
.fm-up-err      { font-size:11px; color:#c0392b; margin-top:2px; }

/* ── Misc ────────────────────────────────────────────────────────────────── */
.fm-copy-url    { font-size:12px; padding:3px 8px; }
.fm-empty       { grid-column:1/-1; text-align:center; color:#bbb; padding:60px 0; font-size:14px; }
.fm-drop-overlay{ position:absolute; inset:0; background:rgba(58,91,217,.12); border:2px dashed #3a5bd9; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:16px; color:#3a5bd9; font-weight:600; pointer-events:none; z-index:10; }
.fm-preview-bg  { position:fixed; inset:0; background:rgba(0,0,0,.88); z-index:2000; display:flex; align-items:center; justify-content:center; }
.fm-preview-img { max-width:90vw; max-height:88vh; border-radius:6px; box-shadow:0 4px 32px rgba(0,0,0,.6); display:block; }
.fm-preview-close { position:fixed; top:18px; right:24px; font-size:28px; color:#fff; cursor:pointer; line-height:1; opacity:.8; background:none; border:none; z-index:2001; }
.fm-preview-close:hover { opacity:1; }
.fm-preview-nav { position:fixed; top:50%; transform:translateY(-50%); font-size:32px; color:#fff; cursor:pointer; opacity:.6; background:none; border:none; padding:12px 18px; z-index:2001; }
.fm-preview-nav:hover { opacity:1; }
.fm-preview-nav.prev { left:12px; }
.fm-preview-nav.next { right:12px; }
.fm-preview-name { position:fixed; bottom:18px; left:50%; transform:translateX(-50%); color:#fff; font-size:13px; opacity:.75; white-space:nowrap; }
</style>

<div id="fm-app"
     x-data="fileManager()"
     x-init="init()"
     @dragover.prevent="dragging=true"
     @dragleave="dragging=false"
     @drop.prevent="handleDrop($event)"
     style="position:relative">

    <div x-show="dragging" class="fm-drop-overlay"><i class="fa fa-cloud-upload"></i>&nbsp; Upuść pliki tutaj</div>

    <div class="fm-wrap">
        {{-- Sidebar --}}
        <div class="fm-sidebar">
            <div class="fm-sidebar-sec">Ulubione</div>
            <template x-if="favorites.length === 0">
                <div style="padding:8px 14px;font-size:12px;color:#bbb">Brak ulubionych</div>
            </template>
            <template x-for="fav in favorites" :key="fav">
                <div class="fm-fav-item" :class="{active: currentPath===fav}" @click="navigate(fav)">
                    <i class="fa fa-folder"></i>
                    <span x-text="fav.split('/').pop() || fav"></span>
                </div>
            </template>
            <div class="fm-sidebar-sec" style="margin-top:12px">Katalogi</div>
            <div class="fm-fav-item" :class="{active: currentPath===''}" @click="navigate('')">
                <i class="fa fa-home"></i> Główny
            </div>
        </div>

        {{-- Main --}}
        <div class="fm-main">
            {{-- Toolbar --}}
            <div class="fm-toolbar">
                <label class="btn btn-primary" style="margin:0;cursor:pointer">
                    <i class="fa fa-upload"></i> Wgraj pliki
                    <input type="file" multiple style="display:none" @change="startUploadFlow(Array.from($event.target.files)); $event.target.value=''" accept="*">
                </label>
                <button class="btn-fm-outline" @click="showNewFolder=true"><i class="fa fa-folder-o"></i> Nowy folder</button>

                {{-- Single-file delete --}}
                <button class="btn btn-danger" x-show="selected && !hasChecked" :disabled="!selected" @click="confirmDelete()">
                    <i class="fa fa-trash"></i> Usuń
                </button>

                {{-- Batch delete --}}
                <template x-if="hasChecked">
                    <span style="display:contents">
                        <button class="btn btn-danger" @click="confirmDeleteBatch()">
                            <i class="fa fa-trash"></i> Usuń zaznaczone (<span x-text="checkedPaths.length"></span>)
                        </button>
                        <button class="btn-fm-outline" @click="checkedPaths=[]" style="border-color:#aaa;">
                            <i class="fa fa-times"></i> Odznacz
                        </button>
                    </span>
                </template>

                <div style="flex:1"></div>
                <span x-show="toast" x-text="toast" style="font-size:12px;color:#28a745;font-weight:600"></span>
                <div style="position:relative;display:flex;align-items:center">
                    <i class="fa fa-search" style="position:absolute;left:9px;color:#aaa;font-size:12px;pointer-events:none"></i>
                    <input type="text" x-model="search" placeholder="Szukaj pliku…"
                           @keydown.escape="search=''"
                           style="padding:5px 28px 5px 28px;font-size:12px;border:1px solid #ccc;border-radius:4px;width:200px;outline:none"
                           @focus="$el.style.borderColor='#3a5bd9'" @blur="$el.style.borderColor='#ccc'">
                    <template x-if="search">
                        <button @click="search=''" style="position:absolute;right:7px;background:none;border:none;color:#aaa;cursor:pointer;font-size:14px;padding:0;line-height:1">&times;</button>
                    </template>
                </div>
            </div>

            {{-- Navigation bar --}}
            <div class="fm-nav">
                <template x-if="currentPath">
                    <button class="fm-nav-back" @click="navigate(parentPath)">
                        <i class="fa fa-arrow-left"></i> Wróć
                    </button>
                </template>
                <span class="fm-nav-title">
                    <i class="fa" :class="currentPath ? 'fa-folder-open' : 'fa-home'" style="margin-right:6px;color:#f5a623"></i>
                    <span x-text="currentFolderName"></span>
                </span>
                <template x-if="currentPath">
                    <button class="fm-nav-fav" :class="{active: isFavorite(currentPath)}" @click="toggleFavorite(currentPath)">
                        <i class="fa" :class="isFavorite(currentPath) ? 'fa-star' : 'fa-star-o'"></i>
                        <span x-text="isFavorite(currentPath) ? 'W ulubionych' : 'Dodaj do ulubionych'"></span>
                    </button>
                </template>
                <span class="fm-count" x-text="search ? filteredItems.length + '/' + items.length + ' elementów' : items.length + ' elementów'"></span>
            </div>

            {{-- Selected file info bar --}}
            <div class="fm-selected-bar" x-show="selected && !hasChecked" style="display:none">
                <template x-if="selected && selected.is_dir">
                    <span style="display:contents">
                        <i class="fa fa-folder" style="color:#f5a623;margin-right:5px"></i>
                        <span class="fm-sel-name" x-text="selected && selected.name"></span>
                        <span class="fm-sel-sep">·</span>
                        <a href="#" @click.prevent="startRename()"><i class="fa fa-pencil"></i> Zmień nazwę</a>
                    </span>
                </template>
                <template x-if="selected && !selected.is_dir">
                    <span style="display:contents">
                        <i class="fa fa-file-o" style="color:#888;margin-right:5px"></i>
                        <span class="fm-sel-name" x-text="selected && selected.name"></span>
                        <span class="fm-sel-sep">·</span>
                        <span x-text="selected && formatSize(selected.size)"></span>
                        <span class="fm-sel-sep">·</span>
                        <template x-if="selected && isImage(selected.ext)">
                            <span style="display:contents">
                                <a href="#" @click.prevent="openPreview(selected)"><i class="fa fa-search-plus"></i> Podgląd</a>
                                <span class="fm-sel-sep">·</span>
                            </span>
                        </template>
                        <a :href="selected && selected.url" target="_blank"><i class="fa fa-external-link"></i> Otwórz</a>
                        <span class="fm-sel-sep">·</span>
                        <a href="#" @click.prevent="copyUrl('/storage/' + selected.path)"><i class="fa fa-copy"></i> Kopiuj ścieżkę</a>
                        <span class="fm-sel-sep">·</span>
                        <a href="#" @click.prevent="startRename()"><i class="fa fa-pencil"></i> Zmień nazwę</a>
                    </span>
                </template>
            </div>

            {{-- Batch info bar --}}
            <div class="fm-batch-bar" x-show="hasChecked">
                <i class="fa fa-check-square-o" style="color:#3a5bd9"></i>
                <span>Zaznaczono <strong x-text="checkedPaths.length"></strong> plik(ów)</span>
                <a href="#" @click.prevent="selectAllFiles()" style="margin-left:8px;font-size:12px;color:#3a5bd9;text-decoration:none;">Zaznacz wszystkie pliki</a>
            </div>

            {{-- Grid --}}
            <div class="fm-grid" @click.self="selected=null; checkedPaths=[]">
                <template x-if="loading">
                    <div class="fm-empty"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                </template>
                <template x-if="!loading && errorMsg">
                    <div class="fm-empty" style="color:#c0392b">
                        <i class="fa fa-exclamation-triangle fa-2x" style="display:block;margin-bottom:10px"></i>
                        <span x-text="errorMsg"></span>
                    </div>
                </template>
                <template x-if="!loading && !errorMsg && items.length===0">
                    <div class="fm-empty"><i class="fa fa-folder-open-o fa-3x" style="display:block;margin-bottom:10px"></i>Folder jest pusty</div>
                </template>
                <template x-for="item in filteredItems" :key="item.path">
                    <div class="fm-item"
                         :class="{selected: selected && selected.path===item.path, 'is-checked': isChecked(item.path)}"
                         @click="item.is_dir ? navigate(item.path) : selectItem(item)">

                        {{-- Checkbox (files only) --}}
                        <template x-if="!item.is_dir">
                            <span class="fm-item-cb"
                                  :class="{active: isChecked(item.path)}"
                                  @click.stop="toggleCheck(item.path)"
                                  title="Zaznacz">
                                <i class="fa" :class="isChecked(item.path) ? 'fa-check-square' : 'fa-square-o'"></i>
                            </span>
                        </template>

                        <template x-if="!item.is_dir && isImage(item.ext)">
                            <img :src="item.url" class="fm-item-thumb" :alt="item.name" onerror="this.style.display='none'" loading="lazy"
                                 @dblclick.stop="openPreview(item)" style="cursor:zoom-in" title="Podwójne kliknięcie = podgląd">
                        </template>
                        <template x-if="item.is_dir">
                            <div class="fm-item-icon dir"><i class="fa fa-folder"></i></div>
                        </template>
                        <template x-if="!item.is_dir && !isImage(item.ext)">
                            <div class="fm-item-icon" :class="iconClass(item.ext)"><i class="fa" :class="iconFa(item.ext)"></i></div>
                        </template>
                        <span class="fm-item-name" x-text="item.name"></span>
                        <template x-if="item.is_dir">
                            <span class="fm-item-fav" :class="{active: isFavorite(item.path)}" @click.stop="toggleFavorite(item.path)" title="Dodaj do ulubionych">
                                <i class="fa fa-star"></i>
                            </span>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ── New folder modal ──────────────────────────────────────────────── --}}
    <div class="fm-modal-bg" x-show="showNewFolder" @click.self="showNewFolder=false">
        <div class="fm-modal">
            <h4><i class="fa fa-folder-o"></i> Nowy folder</h4>
            <input type="text" x-model="newFolderName" placeholder="Nazwa folderu" @keydown.enter="createFolder()" @keydown.escape="showNewFolder=false" x-ref="folderInput">
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button class="btn btn-default" @click="showNewFolder=false">Anuluj</button>
                <button class="btn btn-primary" @click="createFolder()">Utwórz</button>
            </div>
        </div>
    </div>

    {{-- ── Single delete modal ───────────────────────────────────────────── --}}
    <div class="fm-modal-bg" x-show="showDelete" @click.self="showDelete=false">
        <div class="fm-modal">
            <h4><i class="fa fa-trash text-danger"></i> Potwierdź usunięcie</h4>
            <p style="font-size:13px;margin-bottom:16px">
                Czy na pewno chcesz usunąć <strong x-text="selected && selected.name"></strong>?
                <template x-if="selected && selected.is_dir">
                    <span style="color:#c0392b"> (wraz z całą zawartością)</span>
                </template>
            </p>
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button class="btn btn-default" @click="showDelete=false">Anuluj</button>
                <button class="btn btn-danger" @click="deleteItem()">Usuń</button>
            </div>
        </div>
    </div>

    {{-- ── Batch delete modal ────────────────────────────────────────────── --}}
    <div class="fm-modal-bg" x-show="showDeleteBatch" @click.self="showDeleteBatch=false">
        <div class="fm-modal">
            <h4><i class="fa fa-trash text-danger"></i> Usuń zaznaczone pliki</h4>
            <p style="font-size:13px;margin-bottom:16px">
                Czy na pewno chcesz trwale usunąć <strong x-text="checkedPaths.length"></strong> plik(ów)?
                Ta operacja jest nieodwracalna.
            </p>
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button class="btn btn-default" @click="showDeleteBatch=false">Anuluj</button>
                <button class="btn btn-danger" @click="deleteBatch()"><i class="fa fa-trash"></i> Usuń wszystkie</button>
            </div>
        </div>
    </div>

    {{-- ── Rename modal ──────────────────────────────────────────────────── --}}
    <div class="fm-modal-bg" x-show="showRename" @click.self="showRename=false">
        <div class="fm-modal">
            <h4><i class="fa fa-pencil"></i> Zmień nazwę</h4>
            <input type="text" x-model="renameName" placeholder="Nowa nazwa"
                   @keydown.enter="doRename()" @keydown.escape="showRename=false"
                   x-ref="renameInput">
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <button class="btn btn-default" @click="showRename=false">Anuluj</button>
                <button class="btn btn-primary" @click="doRename()">Zmień</button>
            </div>
        </div>
    </div>

    {{-- ── Conflict resolution modal ─────────────────────────────────────── --}}
    <div class="fm-modal-bg" x-show="showConflictModal" @click.self="showConflictModal=false">
        <div class="fm-modal" style="max-width:440px;width:100%;">
            <h4><i class="fa fa-exclamation-triangle" style="color:#f5a623"></i> Wykryto konflikty nazw</h4>
            <p style="font-size:13px;margin-bottom:10px;color:#555">
                Poniższe pliki już istnieją w tym folderze. Co chcesz zrobić?
            </p>
            <ul class="fm-conflict-list">
                <template x-for="name in conflictFiles" :key="name">
                    <li>
                        <i class="fa fa-file-o"></i>
                        <span x-text="name" style="flex:1;font-size:13px;word-break:break-all;"></span>
                    </li>
                </template>
            </ul>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button class="btn btn-warning" @click="resolveConflicts('replace')" style="flex:1;">
                    <i class="fa fa-refresh"></i> Zastąp istniejące
                </button>
                <button class="btn btn-primary" @click="resolveConflicts('rename')" style="flex:1;">
                    <i class="fa fa-plus-circle"></i> Dodaj jako nowe (sufiks)
                </button>
            </div>
            <div style="margin-top:8px;text-align:right;">
                <button class="btn btn-default btn-sm" @click="showConflictModal=false; pendingUploadFiles=null; conflictFiles=[]">Anuluj wgrywanie</button>
            </div>
        </div>
    </div>

    {{-- ── Image preview lightbox ────────────────────────────────────────── --}}
    <div class="fm-preview-bg" x-show="previewItem" @click.self="previewItem=null"
         @keydown.escape.window="previewItem=null"
         @keydown.arrow-left.window="prevImage()"
         @keydown.arrow-right.window="nextImage()">
        <button class="fm-preview-close" @click="previewItem=null">&times;</button>
        <template x-if="previewImages.length > 1">
            <button class="fm-preview-nav prev" @click="prevImage()"><i class="fa fa-chevron-left"></i></button>
        </template>
        <template x-if="previewItem">
            <img :src="previewItem.url" class="fm-preview-img" :alt="previewItem.name">
        </template>
        <template x-if="previewImages.length > 1">
            <button class="fm-preview-nav next" @click="nextImage()"><i class="fa fa-chevron-right"></i></button>
        </template>
        <template x-if="previewItem">
            <span class="fm-preview-name" x-text="previewItem.name + (previewImages.length > 1 ? ' (' + (previewIndex+1) + '/' + previewImages.length + ')' : '')"></span>
        </template>
    </div>

    {{-- ── Upload progress panel ────────────────────────────────────────── --}}
    <div class="fm-up-panel" x-show="showUploadPanel">
        <div class="fm-up-header" @click="uploadPanelMinimized = !uploadPanelMinimized">
            <i class="fa fa-cloud-upload" style="flex-shrink:0;"></i>
            <span class="fm-up-summary">
                <template x-if="!uploadAllDone">
                    Wgrywanie… <span x-text="uploadDoneCount + '/' + uploadQueue.length"></span>
                </template>
                <template x-if="uploadAllDone">
                    Gotowe — <span x-text="uploadDoneCount + '/' + uploadQueue.length"></span> wgrano
                </template>
            </span>
            <button @click.stop="uploadPanelMinimized = !uploadPanelMinimized" :title="uploadPanelMinimized ? 'Rozwiń' : 'Zwiń'">
                <i class="fa" :class="uploadPanelMinimized ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <button @click.stop="showUploadPanel = false" title="Zamknij panel">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="fm-up-list" x-show="!uploadPanelMinimized">
            <template x-for="(item, idx) in uploadQueue" :key="idx">
                <div class="fm-up-item">
                    <div class="fm-up-item-row">
                        <i class="fa fa-fw"
                           :class="{
                               'fa-clock-o': item.status==='waiting',
                               'fa-spinner fa-spin': item.status==='uploading',
                               'fa-check-circle': item.status==='done',
                               'fa-times-circle': item.status==='error'
                           }"
                           :style="'color:' + (item.status==='done' ? '#28a745' : item.status==='error' ? '#dc3545' : item.status==='uploading' ? '#3a5bd9' : '#ccc')">
                        </i>
                        <span class="fm-up-item-name" x-text="item.name"></span>
                        <span class="fm-up-pct" x-show="item.status==='uploading'" x-text="item.progress + '%'"></span>
                    </div>
                    <template x-if="item.status !== 'waiting'">
                        <div class="fm-up-bar">
                            <div class="fm-up-bar-fill"
                                 :style="'width:' + item.progress + '%;background:' + (item.status==='done' ? '#28a745' : item.status==='error' ? '#dc3545' : '#3a5bd9')">
                            </div>
                        </div>
                    </template>
                    <template x-if="item.error">
                        <div class="fm-up-err" x-text="item.error"></div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
<script>
function fileManager() {
    return {
        // ── Core state ───────────────────────────────────────────────────────
        currentPath: '',
        items: [],
        favorites: [],
        selected: null,
        loading: false,
        dragging: false,
        errorMsg: '',
        search: '',
        toast: '',
        _toastTimer: null,

        // ── Modals ───────────────────────────────────────────────────────────
        showNewFolder: false,
        newFolderName: '',
        showDelete: false,
        showDeleteBatch: false,
        showRename: false,
        renameName: '',
        previewItem: null,

        // ── Multi-select ─────────────────────────────────────────────────────
        checkedPaths: [],

        // ── Upload ───────────────────────────────────────────────────────────
        uploadQueue: [],
        showUploadPanel: false,
        uploadPanelMinimized: false,

        // ── Conflict resolution ──────────────────────────────────────────────
        showConflictModal: false,
        conflictFiles: [],
        pendingUploadFiles: null,

        // ── Computed ─────────────────────────────────────────────────────────
        get parentPath() {
            if (!this.currentPath) return ''
            const p = this.currentPath.split('/')
            p.pop()
            return p.join('/')
        },

        get currentFolderName() {
            return this.currentPath ? this.currentPath.split('/').pop() : 'Główny'
        },

        get filteredItems() {
            if (!this.search.trim()) return this.items
            const q = this.search.trim().toLowerCase()
            return this.items.filter(i => i.name.toLowerCase().includes(q))
        },

        get previewImages() {
            return this.filteredItems.filter(i => !i.is_dir && this.isImage(i.ext))
        },

        get previewIndex() {
            if (!this.previewItem) return -1
            return this.previewImages.findIndex(i => i.path === this.previewItem.path)
        },

        get hasChecked() { return this.checkedPaths.length > 0 },

        get uploadDoneCount() {
            return this.uploadQueue.filter(i => i.status === 'done' || i.status === 'error').length
        },

        get uploadAllDone() {
            return this.uploadQueue.length > 0 &&
                   this.uploadQueue.every(i => i.status === 'done' || i.status === 'error')
        },

        // ── Init / load ──────────────────────────────────────────────────────
        init() { this.load('') },

        load(path) {
            this.loading = true
            this.errorMsg = ''
            this.selected = null
            this.search = ''
            fetch(`{{ route('file-manager.list') }}?path=${encodeURIComponent(path)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) { this.errorMsg = data.error; this.items = [] }
                else { this.items = data.items || []; this.favorites = data.favorites || []; this.currentPath = path }
                this.loading = false
            })
            .catch(e => { this.errorMsg = 'Błąd połączenia: ' + e.message; this.loading = false })
        },

        navigate(path) { this.checkedPaths = []; this.load(path) },

        selectItem(item) {
            this.selected = this.selected && this.selected.path === item.path ? null : item
        },

        // ── Multi-select ─────────────────────────────────────────────────────
        isChecked(path) { return this.checkedPaths.includes(path) },

        toggleCheck(path) {
            const idx = this.checkedPaths.indexOf(path)
            if (idx === -1) this.checkedPaths.push(path)
            else this.checkedPaths.splice(idx, 1)
        },

        selectAllFiles() {
            this.checkedPaths = this.filteredItems.filter(i => !i.is_dir).map(i => i.path)
        },

        // ── Batch delete ─────────────────────────────────────────────────────
        confirmDeleteBatch() { this.showDeleteBatch = true },

        deleteBatch() {
            const paths = [...this.checkedPaths]
            fetch('{{ route('file-manager.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this._csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ paths })
            })
            .then(r => r.json())
            .then(data => {
                this.showDeleteBatch = false
                this.checkedPaths = []
                this.selected = null
                this.load(this.currentPath)
                this.showToast('Usunięto ' + (data.deleted || []).length + ' plik(ów)')
            })
            .catch(() => { this.showDeleteBatch = false; alert('Błąd usuwania') })
        },

        // ── Single delete ─────────────────────────────────────────────────────
        confirmDelete() { if (!this.selected) return; this.showDelete = true },

        deleteItem() {
            if (!this.selected) return
            fetch('{{ route('file-manager.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this._csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ paths: [this.selected.path] })
            })
            .then(r => r.json())
            .then(data => {
                if (data.errors && data.errors.length) { alert('Nie znaleziono pliku/folderu.'); return }
                this.showDelete = false
                this.selected = null
                this.load(this.currentPath)
                this.showToast('Usunięto')
            })
        },

        // ── Upload flow ───────────────────────────────────────────────────────
        handleDrop(e) {
            this.dragging = false
            if (e.dataTransfer.files.length) this.startUploadFlow(Array.from(e.dataTransfer.files))
        },

        startUploadFlow(files) {
            if (!files.length) return
            const filenames = files.map(f => f.name)

            fetch('{{ route('file-manager.check-conflicts') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this._csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path: this.currentPath, files: filenames })
            })
            .then(r => r.json())
            .then(data => {
                if (data.conflicts && data.conflicts.length > 0) {
                    this.conflictFiles = data.conflicts
                    this.pendingUploadFiles = files
                    this.showConflictModal = true
                } else {
                    this._doUpload(files, 'rename')
                }
            })
            .catch(() => this._doUpload(files, 'rename'))
        },

        resolveConflicts(strategy) {
            this.showConflictModal = false
            const files = this.pendingUploadFiles
            this.pendingUploadFiles = null
            this.conflictFiles = []
            this._doUpload(files, strategy)
        },

        _doUpload(files, strategy) {
            this.uploadPanelMinimized = false
            this.showUploadPanel = true
            this.uploadQueue = files.map(f => ({ name: f.name, progress: 0, status: 'waiting', error: null }))
            this._uploadNext(files, strategy, 0)
        },

        _uploadNext(files, strategy, idx) {
            if (idx >= files.length) {
                this.load(this.currentPath)
                this.showToast('Wgrano ' + files.length + ' plik(ów)')
                return
            }

            const self = this
            self.uploadQueue.splice(idx, 1, Object.assign({}, self.uploadQueue[idx], { status: 'uploading', progress: 0 }))

            const fd = new FormData()
            fd.append('files[]', files[idx])
            fd.append('path', self.currentPath)
            fd.append('strategy', strategy)
            fd.append('_token', self._csrf())

            const xhr = new XMLHttpRequest()

            xhr.upload.addEventListener('progress', function (e) {
                if (!e.lengthComputable) return
                const pct = Math.round((e.loaded / e.total) * 100)
                self.uploadQueue.splice(idx, 1, Object.assign({}, self.uploadQueue[idx], { progress: pct }))
            })

            xhr.addEventListener('load', function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    self.uploadQueue.splice(idx, 1, Object.assign({}, self.uploadQueue[idx], { progress: 100, status: 'done' }))
                } else {
                    let msg = 'Błąd serwera (' + xhr.status + ')'
                    try { const d = JSON.parse(xhr.responseText); msg = d.message || d.error || msg } catch (e) {}
                    self.uploadQueue.splice(idx, 1, Object.assign({}, self.uploadQueue[idx], { status: 'error', error: msg }))
                }
                self._uploadNext(files, strategy, idx + 1)
            })

            xhr.addEventListener('error', function () {
                self.uploadQueue.splice(idx, 1, Object.assign({}, self.uploadQueue[idx], { status: 'error', error: 'Błąd połączenia' }))
                self._uploadNext(files, strategy, idx + 1)
            })

            xhr.open('POST', '{{ route('file-manager.upload') }}')
            xhr.send(fd)
        },

        // ── New folder ───────────────────────────────────────────────────────
        createFolder() {
            if (!this.newFolderName.trim()) return
            fetch('{{ route('file-manager.folder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this._csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path: this.currentPath, name: this.newFolderName.trim() })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) { alert(data.error); return }
                this.newFolderName = ''
                this.showNewFolder = false
                this.load(this.currentPath)
                this.showToast('Folder utworzony')
            })
        },

        // ── Rename ───────────────────────────────────────────────────────────
        startRename() {
            if (!this.selected) return
            this.renameName = this.selected.name
            this.showRename = true
            this.$nextTick(() => { if (this.$refs.renameInput) this.$refs.renameInput.select() })
        },

        doRename() {
            if (!this.selected || !this.renameName.trim()) return
            fetch('{{ route('file-manager.rename') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this._csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path: this.selected.path, name: this.renameName.trim() })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) { alert(data.error); return }
                this.showRename = false
                this.renameName = ''
                this.load(this.currentPath)
                this.showToast('Zmieniono nazwę')
            })
        },

        // ── Preview ──────────────────────────────────────────────────────────
        openPreview(item) { this.previewItem = item },

        prevImage() {
            if (!this.previewItem || !this.previewImages.length) return
            const idx = this.previewIndex
            this.previewItem = this.previewImages[(idx - 1 + this.previewImages.length) % this.previewImages.length]
        },

        nextImage() {
            if (!this.previewItem || !this.previewImages.length) return
            this.previewItem = this.previewImages[(this.previewIndex + 1) % this.previewImages.length]
        },

        // ── Favorites ────────────────────────────────────────────────────────
        toggleFavorite(path) {
            fetch('{{ route('file-manager.favorite') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this._csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path })
            })
            .then(r => r.json())
            .then(data => {
                if (data.favorited) { if (!this.favorites.includes(path)) this.favorites.push(path) }
                else { this.favorites = this.favorites.filter(f => f !== path) }
            })
        },

        isFavorite(path) { return this.favorites.includes(path) },

        // ── Utilities ────────────────────────────────────────────────────────
        copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => this.showToast('URL skopiowany!'))
        },

        showToast(msg) {
            this.toast = msg
            clearTimeout(this._toastTimer)
            this._toastTimer = setTimeout(() => { this.toast = '' }, 2500)
        },

        isImage(ext) { return ['jpg','jpeg','png','gif','webp','svg','bmp'].includes((ext||'').toLowerCase()) },

        iconClass(ext) {
            if (['pdf'].includes(ext)) return 'pdf'
            if (['xls','xlsx','csv'].includes(ext)) return 'xls'
            if (['doc','docx'].includes(ext)) return 'doc'
            return 'other'
        },

        iconFa(ext) {
            if (ext === 'pdf') return 'fa-file-pdf-o'
            if (['xls','xlsx','csv'].includes(ext)) return 'fa-file-excel-o'
            if (['doc','docx'].includes(ext)) return 'fa-file-word-o'
            if (['zip','rar','7z','tar','gz'].includes(ext)) return 'fa-file-archive-o'
            if (['mp4','avi','mov','mkv'].includes(ext)) return 'fa-file-video-o'
            return 'fa-file-o'
        },

        formatSize(bytes) {
            if (!bytes) return '0 B'
            if (bytes < 1024) return bytes + ' B'
            if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB'
            return (bytes/1048576).toFixed(1) + ' MB'
        },

        _csrf() {
            const m = document.querySelector('meta[name=csrf-token]')
            return m ? m.content : ''
        }
    }
}
</script>
@endsection

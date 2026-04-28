@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="fa fa-folder-open"></i> Menedżer plików
    </h1>
@endsection

@section('app-content')
<style>
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
.fm-item        { display:flex; flex-direction:column; align-items:center; padding:8px 6px 6px; border:1px solid transparent; border-radius:6px; cursor:pointer; transition:all .15s; position:relative; min-width:0; }
.fm-item:hover  { background:#f0f3ff; border-color:#c5ceee; }
.fm-item.selected { background:#e6eaf5; border-color:#3a5bd9; }
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
.fm-drop-overlay{ position:absolute; inset:0; background:rgba(58,91,217,.12); border:2px dashed #3a5bd9; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:16px; color:#3a5bd9; font-weight:600; pointer-events:none; z-index:10; }
.fm-selected-bar { display:flex; align-items:center; gap:10px; padding:5px 14px; background:#fff9e6; border-bottom:1px solid #f0d98a; font-size:12px; flex-wrap:wrap; min-height:32px; }
.fm-selected-bar .fm-sel-name { font-weight:700; color:#333; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.fm-selected-bar .fm-sel-sep  { color:#ccc; }
.fm-selected-bar a            { color:#3a5bd9; text-decoration:none; }
.fm-selected-bar a:hover      { text-decoration:underline; }
.fm-count { margin-left:auto; color:#aaa; font-size:11px; }
.fm-modal-bg    { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; display:flex; align-items:center; justify-content:center; }
.fm-modal       { background:#fff; border-radius:8px; padding:24px 28px; min-width:320px; box-shadow:0 8px 32px rgba(0,0,0,.18); }
.fm-modal h4    { margin:0 0 16px; font-size:16px; }
.fm-modal input { width:100%; padding:7px 10px; border:1px solid #ccc; border-radius:4px; font-size:13px; margin-bottom:14px; }
.fm-copy-url    { font-size:12px; padding:3px 8px; }
.fm-empty       { grid-column:1/-1; text-align:center; color:#bbb; padding:60px 0; font-size:14px; }
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

    {{-- Drop overlay --}}
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
                    <input type="file" multiple style="display:none" @change="uploadFiles($event.target.files)" accept="*">
                </label>
                <button class="btn-fm-outline" @click="showNewFolder=true"><i class="fa fa-folder-o"></i> Nowy folder</button>
                <button class="btn btn-danger" :disabled="!selected" @click="confirmDelete()" x-show="selected"><i class="fa fa-trash"></i> Usuń</button>
                <div style="flex:1"></div>
                <span x-show="toast" x-text="toast" style="font-size:12px;color:#28a745;font-weight:600"></span>
            </div>

            {{-- Navigation bar (drill-down) --}}
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
                <span class="fm-count" x-text="items.length + ' elementów'"></span>
            </div>

            {{-- Selected file info bar --}}
            <div class="fm-selected-bar" x-show="selected" style="display:none">
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
                        <a href="#" @click.prevent="copyUrl(selected.url)"><i class="fa fa-copy"></i> Kopiuj URL</a>
                        <span class="fm-sel-sep">·</span>
                        <a href="#" @click.prevent="startRename()"><i class="fa fa-pencil"></i> Zmień nazwę</a>
                    </span>
                </template>
            </div>

            {{-- Grid --}}
            <div class="fm-grid" @click.self="selected=null">
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
                <template x-for="item in items" :key="item.path">
                    <div class="fm-item"
                         :class="{selected: selected && selected.path===item.path}"
                         @click="item.is_dir ? navigate(item.path) : selectItem(item)">
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

    {{-- New folder modal --}}
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

    {{-- Delete confirm modal --}}
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

    {{-- Rename modal --}}
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

    {{-- Image preview lightbox --}}
    <div class="fm-preview-bg" x-show="previewItem" @click.self="previewItem=null" @keydown.escape.window="previewItem=null" @keydown.arrow-left.window="prevImage()" @keydown.arrow-right.window="nextImage()">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
<script>
function fileManager() {
    return {
        currentPath: '',
        items: [],
        favorites: [],
        selected: null,
        loading: false,
        dragging: false,
        showNewFolder: false,
        newFolderName: '',
        showDelete: false,
        showRename: false,
        renameName: '',
        previewItem: null,
        toast: '',
        errorMsg: '',
        _toastTimer: null,

        get parentPath() {
            if (!this.currentPath) return ''
            const parts = this.currentPath.split('/')
            parts.pop()
            return parts.join('/')
        },

        get currentFolderName() {
            if (!this.currentPath) return 'Główny'
            return this.currentPath.split('/').pop()
        },

        get previewImages() {
            return this.items.filter(i => !i.is_dir && this.isImage(i.ext))
        },

        get previewIndex() {
            if (!this.previewItem) return -1
            return this.previewImages.findIndex(i => i.path === this.previewItem.path)
        },

        init() {
            this.load('')
        },

        load(path) {
            this.loading = true
            this.errorMsg = ''
            this.selected = null
            fetch(`{{ route('file-manager.list') }}?path=${encodeURIComponent(path)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    this.errorMsg = data.error
                    this.items = []
                } else {
                    this.items = data.items || []
                    this.favorites = data.favorites || []
                    this.currentPath = path
                }
                this.loading = false
            })
            .catch((e) => {
                this.errorMsg = 'Błąd połączenia: ' + e.message
                this.loading = false
            })
        },

        navigate(path) {
            this.load(path)
        },

        selectItem(item) {
            this.selected = this.selected && this.selected.path === item.path ? null : item
        },

        uploadFiles(files) {
            if (!files || files.length === 0) return
            const fd = new FormData()
            for (const f of files) fd.append('files[]', f)
            fd.append('path', this.currentPath)
            fd.append('_token', document.querySelector('meta[name=csrf-token]').content)
            fetch('{{ route('file-manager.upload') }}', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.error) { alert(data.error); return }
                    this.load(this.currentPath)
                    this.showToast('Wgrano ' + data.uploaded.length + ' plik(ów)')
                })
                .catch(() => alert('Błąd wgrywania'))
        },

        handleDrop(e) {
            this.dragging = false
            if (e.dataTransfer.files.length) this.uploadFiles(e.dataTransfer.files)
        },

        createFolder() {
            if (!this.newFolderName.trim()) return
            fetch('{{ route('file-manager.folder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
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

        confirmDelete() {
            if (!this.selected) return
            this.showDelete = true
        },

        deleteItem() {
            if (!this.selected) return
            fetch('{{ route('file-manager.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path: this.selected.path })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) { alert(data.error); return }
                this.showDelete = false
                this.selected = null
                this.load(this.currentPath)
                this.showToast('Usunięto')
            })
        },

        startRename() {
            if (!this.selected) return
            this.renameName = this.selected.name
            this.showRename = true
            this.$nextTick(() => { if (this.$refs.renameInput) this.$refs.renameInput.select() })
        },

        doRename() {
            if (!this.selected || !this.renameName.trim()) return
            const oldPath = this.selected.path
            fetch('{{ route('file-manager.rename') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path: oldPath, name: this.renameName.trim() })
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

        openPreview(item) {
            this.previewItem = item
        },

        prevImage() {
            if (!this.previewImages.length) return
            const idx = this.previewIndex
            this.previewItem = this.previewImages[(idx - 1 + this.previewImages.length) % this.previewImages.length]
        },

        nextImage() {
            if (!this.previewImages.length) return
            const idx = this.previewIndex
            this.previewItem = this.previewImages[(idx + 1) % this.previewImages.length]
        },

        toggleFavorite(path) {
            fetch('{{ route('file-manager.favorite') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ path })
            })
            .then(r => r.json())
            .then(data => {
                if (data.favorited) {
                    if (!this.favorites.includes(path)) this.favorites.push(path)
                } else {
                    this.favorites = this.favorites.filter(f => f !== path)
                }
            })
        },

        isFavorite(path) { return this.favorites.includes(path) },

        copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => this.showToast('URL skopiowany!'))
        },

        showToast(msg) {
            this.toast = msg
            clearTimeout(this._toastTimer)
            this._toastTimer = setTimeout(() => { this.toast = '' }, 2500)
        },

        isImage(ext) { return ['jpg','jpeg','png','gif','webp','svg','bmp'].includes(ext) },

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
        }
    }
}
</script>
@endsection

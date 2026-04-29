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
                <p class="text-muted" style="margin-bottom: 20px;">
                    Drzewo kategorii — dowolna liczba poziomów.
                </p>
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
</script>
@endsection

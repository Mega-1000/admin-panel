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
                    Drzewo kategorii — maksymalnie 3 poziomy.
                    <strong>Poziom 1</strong> (pogrubiony) → <strong>Poziom 2</strong> (wcięcie 1×) → <strong>Poziom 3</strong> (wcięcie 2×).
                </p>
                <table class="table table-hover">
                    <thead>
                        <tr>
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
                            <tr class="category-row level-1">
                                <td>
                                    <strong>{{ $root->name }}</strong>
                                    <small class="text-muted">#{{ $root->id }}</small>
                                    @if($root->children->count())
                                        <button class="btn btn-xs btn-default toggle-children" data-id="{{ $root->id }}" style="margin-left:6px;">
                                            <i class="fa fa-chevron-down"></i> {{ $root->children->count() }}
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if($root->is_visible)
                                        <span class="badge badge-success">Tak</span>
                                    @else
                                        <span class="badge badge-danger">Nie</span>
                                    @endif
                                </td>
                                <td>{{ $root->priority }}</td>
                                <td>{{ $root->products_count ?? $root->products()->count() }}</td>
                                <td>
                                    @if(!empty($root->youtube))
                                        <span class="badge badge-info">{{ count($root->youtube) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('categories.edit', $root->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i> Edytuj
                                    </a>
                                    <form action="{{ route('categories.destroy', $root->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Usunąć kategorię {{ addslashes($root->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            @foreach($root->children as $child)
                                <tr class="category-row level-2 children-of-{{ $root->id }}" style="display:none; background:#fafafa;">
                                    <td style="padding-left:30px;">
                                        <i class="fa fa-angle-right text-muted"></i>
                                        {{ $child->name }}
                                        <small class="text-muted">#{{ $child->id }}</small>
                                        @if($child->children->count())
                                            <button class="btn btn-xs btn-default toggle-children" data-id="{{ $child->id }}" style="margin-left:6px;">
                                                <i class="fa fa-chevron-down"></i> {{ $child->children->count() }}
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($child->is_visible)
                                            <span class="badge badge-success">Tak</span>
                                        @else
                                            <span class="badge badge-danger">Nie</span>
                                        @endif
                                    </td>
                                    <td>{{ $child->priority }}</td>
                                    <td>{{ $child->products()->count() }}</td>
                                    <td>
                                        @if(!empty($child->youtube))
                                            <span class="badge badge-info">{{ count($child->youtube) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('categories.edit', $child->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i> Edytuj
                                        </a>
                                        <form action="{{ route('categories.destroy', $child->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Usunąć kategorię {{ addslashes($child->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                @foreach($child->children as $grandchild)
                                    <tr class="category-row level-3 children-of-{{ $child->id }}" style="display:none; background:#f4f4f4;">
                                        <td style="padding-left:60px;">
                                            <i class="fa fa-angle-double-right text-muted"></i>
                                            {{ $grandchild->name }}
                                            <small class="text-muted">#{{ $grandchild->id }}</small>
                                        </td>
                                        <td>
                                            @if($grandchild->is_visible)
                                                <span class="badge badge-success">Tak</span>
                                            @else
                                                <span class="badge badge-danger">Nie</span>
                                            @endif
                                        </td>
                                        <td>{{ $grandchild->priority }}</td>
                                        <td>{{ $grandchild->products()->count() }}</td>
                                        <td>
                                            @if(!empty($grandchild->youtube))
                                                <span class="badge badge-info">{{ count($grandchild->youtube) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('categories.edit', $grandchild->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> Edytuj
                                            </a>
                                            <form action="{{ route('categories.destroy', $grandchild->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Usunąć kategorię {{ addslashes($grandchild->name) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Brak kategorii.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.toggle-children').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var rows = document.querySelectorAll('.children-of-' + id);
            var icon = this.querySelector('i');
            var visible = rows[0] && rows[0].style.display !== 'none';

            rows.forEach(function(row) {
                row.style.display = visible ? 'none' : '';
            });

            icon.className = visible ? 'fa fa-chevron-down' : 'fa fa-chevron-up';

            if (visible) {
                document.querySelectorAll('[class*="children-of-"]').forEach(function(row) {
                    var parentId = row.className.match(/children-of-(\d+)/);
                    if (parentId) {
                        var parentRow = document.querySelector('.children-of-' + id + '.level-2');
                        if (parentRow && row.classList.contains('level-3')) {
                            row.style.display = 'none';
                        }
                    }
                });
            }
        });
    });
</script>
@endsection

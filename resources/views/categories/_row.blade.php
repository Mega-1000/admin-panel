<tr class="category-row level-{{ $depth }} children-of-{{ $parentId }}"
    @if($depth > 1) style="display:none;" @endif
    data-id="{{ $category->id }}">
    <td><span class="cat-id">{{ $category->id }}</span></td>
    <td style="padding-left:{{ ($depth - 1) * 30 }}px;">
        @if($depth > 1)
            <i class="fa fa-angle-right text-muted" style="margin-right:4px;"></i>
        @endif
        @if($depth === 1)<strong>{{ $category->name }}</strong>@else{{ $category->name }}@endif
        @if($category->allChildren->count())
            <span class="expand-toggle" data-id="{{ $category->id }}">
                <i class="fa fa-chevron-right"></i>
                {{ $category->allChildren->count() }}
                podkategori{{ $category->allChildren->count() === 1 ? 'a' : ($category->allChildren->count() < 5 ? 'e' : 'i') }}
            </span>
        @endif
    </td>
    <td>
        @if($category->is_visible)
            <span class="badge badge-success">Tak</span>
        @else
            <span class="badge badge-danger">Nie</span>
        @endif
    </td>
    <td>{{ $category->priority }}</td>
    <td>{{ $category->products()->count() }}</td>
    <td>
        @if(!empty($category->youtube))
            <span class="badge badge-info">{{ count($category->youtube) }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td>
        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-primary">
            <i class="fa fa-edit"></i> Edytuj
        </a>
        <form id="del-form-{{ $category->id }}"
              action="{{ route('categories.destroy', $category->id) }}"
              method="POST" style="display:inline">
            @csrf @method('DELETE')
            <button type="button" class="btn btn-sm btn-danger cat-delete-btn"
                    data-form="del-form-{{ $category->id }}"
                    data-name="{{ addslashes($category->name) }}"
                    data-children="{{ $category->allChildren->count() }}">
                <i class="voyager-trash"></i>
            </button>
        </form>
    </td>
</tr>

@foreach($category->allChildren as $child)
    @include('categories._row', [
        'category' => $child,
        'depth'    => $depth + 1,
        'parentId' => $category->id,
    ])
@endforeach

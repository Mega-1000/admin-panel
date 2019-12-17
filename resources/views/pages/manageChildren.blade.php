<ul>
    @foreach($childrens as $children)
    <li>
        {{ $children->name }}
        <button type="button" class="btn btn-danger" style="margin-left: 12px" onclick="window.location='{{ route('pages.delete', ['id' => $children->id]) }}'">
            @lang('voyager.generic.delete')
        </button>
        <button type="button" class="btn btn-primary" onclick="window.location='{{ route('pages.edit', ['id' => $children->id]) }}'">
            @lang('voyager.generic.edit')
        </button>
        @if(count($children->childrens))
        @include('pages.manageChildren',['childrens' => $children->childrens])
        @endif
    </li>
    @endforeach
</ul>

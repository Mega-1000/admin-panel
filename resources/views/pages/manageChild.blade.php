<ul>
    @foreach($childs as $child)
    <li>
        {{ $child->name }}
        <button type="button" class="btn btn-danger" style="margin-left: 12px">
            @lang('voyager.generic.delete')
        </button>
        <button type="button" class="btn btn-primary" onclick="window.location='{{ route('pages.edit', ['id' => $child->id]) }}'">
            @lang('voyager.generic.edit')
        </button>
        @if(count($child->childs))
        @include('pages.manageChild',['childs' => $child->childs])
        @endif
    </li>
    @endforeach
</ul>

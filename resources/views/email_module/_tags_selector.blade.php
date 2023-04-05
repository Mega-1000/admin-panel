<div class="tags-selector">
    <select id="tag_select">
        @foreach ($tags as $tag)
            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
        @endforeach
    </select>
    <button id="add_tag" class="btn btn-secondary">Dodaj Tag</button>
</div>
<script defer src="{{ asset('js/email-settings.js') }}"></script>

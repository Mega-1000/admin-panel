@section('scripts')
<script>
(function() {
    var container = document.getElementById('youtube-entries');
    var addBtn    = document.getElementById('add-youtube');
    var template  = document.getElementById('youtube-template');
    var maxEntries = 10;

    function getNextIndex() {
        var entries = container.querySelectorAll('.youtube-entry');
        return entries.length;
    }

    function updateIndices() {
        container.querySelectorAll('.youtube-entry').forEach(function(entry, i) {
            entry.querySelectorAll('input').forEach(function(input) {
                input.name = input.name.replace(/youtube\[\d+\]/, 'youtube[' + i + ']');
            });
        });
    }

    function toggleAddButton() {
        var count = container.querySelectorAll('.youtube-entry').length;
        addBtn.style.display = count >= maxEntries ? 'none' : '';
    }

    addBtn.addEventListener('click', function() {
        var count = container.querySelectorAll('.youtube-entry').length;
        if (count >= maxEntries) return;

        var html = template.innerHTML.replace(/__INDEX__/g, getNextIndex());
        var div = document.createElement('div');
        div.innerHTML = html;
        var entry = div.firstElementChild;
        entry.querySelector('.remove-youtube').addEventListener('click', removeHandler);
        container.appendChild(entry);
        toggleAddButton();
    });

    function removeHandler() {
        this.closest('.youtube-entry').remove();
        updateIndices();
        toggleAddButton();
    }

    container.querySelectorAll('.remove-youtube').forEach(function(btn) {
        btn.addEventListener('click', removeHandler);
    });

    toggleAddButton();
})();
</script>
@endsection

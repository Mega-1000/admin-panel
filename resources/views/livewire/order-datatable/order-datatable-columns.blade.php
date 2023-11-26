<colgroup>
    @foreach($columns as $column)
        <col style="width: 150px;"> <!-- Set an initial width for each column -->
    @endforeach
</colgroup>

<thead wire:model="columns">
    <tr x-on:dragover.prevent="dragOver">
        @foreach($this->columns as $column)
            <th
                draggable="true"
                @dragstart="draggedColumn = $event.target"
                @dragover.prevent="dragOver"
                @drop="drop($event, '{{ $column['label'] }}')"
                data-column-name="{{ $column['label'] }}"
                style="cursor: move;"
            >
                <span @click="hideColumn('{{ $column['label'] }}')">
                    {{ $column['label'] }}
                </span>
                <br>
                <input
                    type="text"
                    wire:model="search.{{ $column['label'] }}"
                    placeholder="Search {{ $column['label'] }}"
                    class="w-full text-sm"
                >
            </th>
        @endforeach
    </tr>
</thead>

<script>
    function dragOver(event) {
        event.preventDefault();
    }

    function hideColumn(columnName) {
        Livewire.emit('hideColumn', columnName);
    }

    function drop(event, columnName) {
        if (event.target.tagName === "TH") {
            const newOrder = [];
            const columns = document.querySelectorAll('th');
            let startColumnIndex = null;
            let endColumnIndex = null;

            columns.forEach((column, index) => {
                if (column.dataset.columnName === window.draggedColumn.dataset.columnName) {
                    startColumnIndex = index;
                }
                if (column.dataset.columnName === columnName) {
                    endColumnIndex = index;
                }
            });

            columns.forEach((column, index) => {
                if (startColumnIndex < endColumnIndex) {
                    if (index < startColumnIndex || index > endColumnIndex) {
                        newOrder.push(column.dataset.columnName);
                    }
                    if (index === endColumnIndex) {
                        newOrder.push(window.draggedColumn.dataset.columnName);
                    }
                    if (index >= startColumnIndex && index < endColumnIndex) {
                        newOrder.push(columns[index + 1].dataset.columnName);
                    }
                } else {
                    if (index > startColumnIndex || index < endColumnIndex) {
                        newOrder.push(column.dataset.columnName);
                    }
                    if (index === endColumnIndex) {
                        newOrder.push(window.draggedColumn.dataset.columnName);
                    }
                    if (index <= startColumnIndex && index > endColumnIndex) {
                        newOrder.push(columns[index - 1].dataset.columnName);
                    }
                }
            });

            Livewire.emit('updateColumnOrder', newOrder);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        let startX;
        let startWidth;

        document.addEventListener('mouseup', function () {
            startX = null;
            startWidth = null;
            window.draggedColumn = null;
        });
    });
</script>


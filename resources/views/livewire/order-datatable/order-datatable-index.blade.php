<div>
    <form @wire:submit.prevent="updatedPageLength">
        <div class="d-flex">
            Ilość zamówień na stronę:
            <input type="number" class="form-control" placeholder="Ilość zamówień na stronę" wire:model="pageLength">
        </div>
    </form>

    <a href="{{ route('orderDatatableColumnsFiltering') }}" class="btn btn-primary">
        Zarządzaj kolumnami
    </a>

    <table class="table table-bordered">
        <colgroup>
            @foreach($columns as $column)
                <col style="width: 150px;"> <!-- Set an initial width for each column -->
            @endforeach
        </colgroup>

        <thead>
            <tr x-on:dragover.prevent="dragOver">
                @foreach($columns as $column)
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
                            wire:model.debounce.500ms="filters.{{ $column['label'] }}"
                            placeholder="Search {{ $column['label'] }}"
                            class="w-full text-sm"
                        >
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($orders['data'] as $order)
                <tr>
                    @foreach($columns as $column)
                        <td>{!! $order[$column['label']] !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="100%">
                    <div class="d-flex justify-content-between">
                        <div>
                            Showing {{ $orders['from'] }} to {{ $orders['to'] }} out of {{ $orders['total'] }} results
                        </div>
                        <div>
                            @foreach($orders['links'] as $link)
                                <a href="{{ $link['url'] }}" class="btn">{!! $link['label'] !!}</a>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
    </table>
</div>

<script>
    let startX;
    let startWidth;
    let resizingColumn;

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

    function startResizing(event, column) {
        startX = event.clientX;
        startWidth = column.offsetWidth;
        resizingColumn = column;

        // make drag and drop not possible while resizing
        document.body.classList.add('resizing');

        document.addEventListener('mousemove', handleResizing);
        document.addEventListener('mouseup', stopResizing);
    }

    function handleResizing(event) {
        const newWidth = startWidth + (event.clientX - startX);
        resizingColumn.style.width = newWidth + 'px';
    }

    function stopResizing() {
        document.body.classList.remove('resizing');
        document.removeEventListener('mousemove', handleResizing);
        document.removeEventListener('mouseup', stopResizing);
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('th').forEach(column => {
            const resizer = document.createElement('div');
            resizer.className = 'resizer';
            resizer.addEventListener('mousedown', (event) => startResizing(event, column));
            column.appendChild(resizer);
        });
    });
</script>

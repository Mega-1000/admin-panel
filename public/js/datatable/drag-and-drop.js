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

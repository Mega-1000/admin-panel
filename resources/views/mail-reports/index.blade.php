@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com"></script>

<script defer>
    function showHide() {
        setTimeout(() => {
            let elements = document.getElementsByClassName('hidden');
            elements = Array.from(elements);
            for (let i = 0; i < elements.length; i++) {
                elements[i].classList.remove('hidden');
                console.log(elements[i])
            }
        }, 1000)
    }

    function filterTable() {
        const selectEmail = document.getElementById('emailFilter');
        const selectedEmail = selectEmail.value;
        const tableRows = document.querySelectorAll('table tr');

        tableRows.forEach((row, index) => {
            if (index === 0) return; // Skip header row
            const emailCell = row.cells[1];
            if (selectedEmail === '' || emailCell.textContent.trim() === selectedEmail) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    showHide();
</script>

@section('table')
    <div class="mb-4">
        <label for="emailFilter" class="mr-2">Filter by Email:</label>
        <select id="emailFilter" onchange="filterTable()" class="border rounded p-1">
            <option value="">All</option>
            @foreach($mailReports->pluck('email')->unique() as $email)
                <option value="{{ $email }}">{{ $email }}</option>
            @endforeach
        </select>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Email adresata</th>
            <th>Tytuł</th>
            <th>opis</th>
            <th>Stworzono</th>
        </tr>
        @foreach($mailReports as $operation)
            <tr>
                <td>{{ $operation->id }}</td>
                <td>{{ $operation->email }}</td>
                <td>{{ $operation->subject }}</td>
                <td>{!! quoted_printable_decode($operation->body) !!}</td>
                <td>{{ $operation->created_at }}</td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $mailReports->links() }}
    </div>
@endsection

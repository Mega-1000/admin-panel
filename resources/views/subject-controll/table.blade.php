<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</head>

<table class="table table-striped table-hover">
    <thead class="thead-dark">
    <tr>
        <th scope="col">Data wystawienia</th>
        <th scope="col">Uwagi</th>
        <th scope="col">Numer</th>
        <th scope="col">Wartość</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($report as $invoice)
        <tr>
            @php
                $invoice = $invoice[0];
            @endphp
            <td>
                {{ $invoice['issueDate'] }}
            </td>
            <td>
                {{ $invoice['notes'] }}
            </td>
            <td>
                {{ $invoice['number'] }}
            </td>
            <td>
                {{ $invoice['value'] }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<form action="{{ route('styro-lead.load-csv') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="csv_file">

    <button>
        Zapisz plik
    </button>
</form>

<!DOCTYPE html>
<html>
<head>
    <title>Styro Leads</title>
    <style>
        .boolean-true {
            background-color: green;
            width: 20px;
            height: 20px;
            display: inline-block;
        }
        .boolean-false {
            background-color: red;
            width: 20px;
            height: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
<h1>Styro Leads</h1>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Phone</th>
        <th>Firm Name</th>
        <th>Email</th>
        <th>Email Sent</th>
        <th>Email Read</th>
        <th>On Website</th>
        <th>Made Inquiry</th>
        <th>Number of Emails Sent</th>
        <th>Created At</th>
        <th>Updated At</th>
    </tr>
    </thead>
    <tbody>
    @foreach($leads as $lead)
        <tr>
            <td>{{ $lead->id }}</td>
            <td>{{ $lead->phone }}</td>
            <td>{{ $lead->firm_name }}</td>
            <td>{{ $lead->email }}</td>
            <td>
                <div class="{{ $lead->email_sent ? 'boolean-true' : 'boolean-false' }}"></div>
            </td>
            <td>
                <div class="{{ $lead->email_read ? 'boolean-true' : 'boolean-false' }}"></div>
            </td>
            <td>
                <div class="{{ $lead->on_website ? 'boolean-true' : 'boolean-false' }}"></div>
            </td>
            <td>
                <div class="{{ $lead->made_inquiry ? 'boolean-true' : 'boolean-false' }}"></div>
            </td>
            <td>{{ $lead->number_of_emails_sent }}</td>
            <td>{{ $lead->created_at }}</td>
            <td>{{ $lead->updated_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>



@extends('layouts.datatable')

@section('app-header')
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
@endsection

@section('app-content')
    <div class="browse container-fluid">
<form action="{{ route('styro-lead.load-csv') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="csv_file">

    <button class="btn btn-primary mt-4">
        Zapisz plik
    </button>

    <br>

    <div style="color: black; font-weight: bold; font-size: larger">
        Ilość osób która weszła na stronę:  {{ \App\StyroLead::where('on_website', true)->count() }}
        <br>
        Ilość osób która otworzyła wiadomość:  {{ \App\StyroLead::where('email_read', true)->count() }}
    </div>
</form>

<table border="1" class="table">
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
    </div>
@endsection

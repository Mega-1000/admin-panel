@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
@endsection

<body class="bg-gray-100 p-6">
<div class="container mx-auto">
    <h1 class="text-3xl font-bold mb-6">Reprezantci</h1>

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full bg-white">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b">id</th>
                <th class="py-2 px-4 border-b">Info kontaktowe</th>
                <th class="py-2 px-4 border-b">Email</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($represents as $represent)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $represent->id }}</td>
                    <td class="py-2 px-4 border-b">{{ $represent->contact_info }}</td>
                    <td class="py-2 px-4 border-b">{{ $represent->email_of_employee }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $represents->links() }}
    </div>
</div>

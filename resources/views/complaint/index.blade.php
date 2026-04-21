@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>
@section('table')
    <a href="{{ route('low-quantity-alerts.create') }}" class="my-2 px-4 py-2 bg-blue-500 text-white rounded">
        Stwórz
    </a>

    <table class="table table-bordered">
        <tr>
            <th>
                ID
            </th>
            <th>
                Login klienta
            </th>
            <th>
                Data stworzenia
            </th>
            <th>
                Akcje
            </th>
        </tr>
        @foreach($orders as $order)
            <tr>
                <td>
                    {{ $order->id }}
                </td>
                <td>
                    {!! $order->customer->login  !!}
                </td>
                <td>
                    {{ $order->created_at }}
                </td>
                <td>
                    <button class="btn btn-primary" onclick="showComplaint({{ $order->chat->complaint_form }})">
                        Zobacz reklamację
                    </button>
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>

    <script>
        const showComplaint = (complaint) => {
            // show
        }
    </script>
@endsection

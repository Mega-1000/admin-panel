<div class="row">
    <div class="col-lg-12 text-center">
        <table class="table">
            <thead>
            <tr>
                <th scope="col" style="width: 15%"></th>
                <th scope="col" style="width: 15%" class="text-center">Klient</th>
                <th scope="col" style="width: 15%" class="text-center">Konsultant</th>
                <th scope="col" style="width: 15%" class="text-center">Magazyn</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">Preferowana data nadania</th>
                <td>{{ $order->dates->customer_preferred_shipment_date }}</td>
                <td>{{ $order->dates->consultant_preferred_shipment_date }}</td>
                <td>{{ $order->dates->warehouse_preferred_shipment_date }}</td>
            </tr>
            <tr>
                <th scope="row">Preferowana data dostawy</th>
                <td>{{ $order->dates->customer_preferred_delivery_date }}</td>
                <td>{{ $order->dates->consultant_preferred_delivery_date }}</td>
                <td>{{ $order->dates->warehouse_preferred_delivery_date }}</td>
            </tr>
            <tr>
                <th scope="row">Akceptacja</th>
                <td>
                    <span
                        class="glyphicon glyphicon-{{ ($order->dates->customer_acceptance) ? 'ok text-success' : 'remove text-danger'}}"></span>
                </td>
                <td>
                    <span
                        class="glyphicon glyphicon-{{ ($order->dates->consultant_acceptance) ? 'ok text-success' : 'remove text-danger'}}"></span>
                </td>
                <td>
                    <span
                        class="glyphicon glyphicon-{{ ($order->dates->warehouse_acceptance) ? 'ok text-success' : 'remove text-danger'}}"></span>
                </td>
            </tr>
            <tr>
                <th scope="row">Akcje</th>
                <td>
                    <button
                        class="btn btn-sm btn-primary {{ is_a(Auth::user(), \App\Entities\Customer::class) ?:'disabled' }}">
                        Modyfikuj
                    </button>
                    <button
                        class="btn btn-sm btn-success {{ is_a(Auth::user(), \App\Entities\Customer::class) ?:'disabled' }}">
                        Akceptuj
                    </button>
                </td>

                <td>
                    <button class="btn btn-sm btn-primary {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Modyfikuj
                    </button>
                    <button class="btn btn-sm btn-success {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Akceptuj
                    </button>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Modyfikuj
                    </button>
                    <button class="btn btn-sm btn-success {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Akceptuj
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

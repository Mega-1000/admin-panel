<div class="row">
    <div class="col-lg-12 text-center">
        <table class="table">
            <thead>
            <tr>
                <th scope="col" style="width: 20%"></th>
                <th scope="col" style="width: 5%" class="text-center"></th>
                <th scope="col" style="width: 15%" class="text-center">Klient</th>
                <th scope="col" style="width: 15%" class="text-center">Konsultant</th>
                <th scope="col" style="width: 15%" class="text-center">Magazyn</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row" rowspan="2" style="vertical-align: middle">Preferowana data nadania</th>
                <th scope="row">Od</th>
                <td>{{ $order->dates->customer_shipment_date_from }}</td>
                <td>{{ $order->dates->consultant_shipment_date_from }}</td>
                <td>{{ $order->dates->warehouse_shipment_date_from }}</td>
            </tr>
            <tr>
                <th scope="row">Do</th>
                <td>{{ $order->dates->customer_shipment_date_to }}</td>
                <td>{{ $order->dates->consultant_shipment_date_to }}</td>
                <td>{{ $order->dates->warehouse_shipment_date_to }}</td>
            </tr>
            <tr>
                <th scope="row" rowspan="2" style="vertical-align: middle">Preferowana data dostawy</th>
                <th scope="row">Od</th>
                <td>{{ $order->dates->customer_delivery_date_from }}</td>
                <td>{{ $order->dates->consultant_delivery_date_from }}</td>
                <td>{{ $order->dates->warehouse_delivery_date_from }}</td>
            </tr>
            <tr>
                <th scope="row">Do</th>
                <td>{{ $order->dates->customer_delivery_date_to }}</td>
                <td>{{ $order->dates->consultant_delivery_date_to }}</td>
                <td>{{ $order->dates->warehouse_delivery_date_to }}</td>
            </tr>
            <tr>
                <th scope="row" colspan="2" style="text-align: center">Akceptacja</th>
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
                <th scope="row" colspan="2" style="text-align: center">Akcje</th>
                <td>
                    <a
                        class="btn btn-sm btn-primary {{ is_a(Auth::user(), \App\Entities\Customer::class) ?:'disabled' }}">
                        Modyfikuj
                    </a>
                    <a
                        class="btn btn-sm btn-success {{ is_a(Auth::user(), \App\Entities\Customer::class) ?:'disabled' }}">
                        Akceptuj
                    </a>
                </td>

                <td>
                    <a class="btn btn-sm btn-primary {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Modyfikuj
                    </a>
                    <a class="btn btn-sm btn-success {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Akceptuj
                    </a>
                </td>
                <td>
                    <a class="btn btn-sm btn-primary {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Modyfikuj
                    </a>
                    <a class="btn btn-sm btn-success {{ is_a(Auth::user(), \App\User::class) ?:'disabled' }}">
                        Akceptuj
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

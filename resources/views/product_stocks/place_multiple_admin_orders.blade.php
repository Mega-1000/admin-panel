@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <div class="form-group-default">
        <div>
            <label for="product-name">Ilość dni do tyłu</label>
            <input id="days-back" class="form-control" placeholder="Ilość dni do tyłu">
        </div>
        <div class="mt-5">
            <label for="days-to-future">Ilość dni do przodu</label>
            <input id="days-to-future" class="form-control" placeholder="Ilość dni do przodu">
        </div>
        <div class="mt-5">
            <label for="firm-id">Nazwa firmy</label>
            <select class="form-control" id="firm-id">
                @foreach($firms as $firm)
                    <option value="{{ $firm->id }}">{{ $firm->symbol }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-5">
            <label for="client-email">Email kliena</label>
            <input value="antoniwoj@o2.pl" id="client-email" class="form-control" placeholder="Email klienta">
        </div>
        <button class="btn btn-primary" id="submit-button">
            Oblicz
        </button>

        <div id="result-value"></div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const encodeToGetParams = (data) => {
            return Object.keys(data).map((key) => {
                return encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
            }).join('&')
        }

        const buy = async () => {
            await Swal.fire({
                title: 'Czy jesteś pewien?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Tak, zamów towary!',
                cancelButtonText: 'Nie, anuluj zamówienie!'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        }

        const calculate = () => {
            const firmId = document.getElementById('firm-id').value
            const daysBack = document.getElementById('days-back').value
            const daysToFuture = document.getElementById('days-to-future').value
            const clientEmail = document.getElementById('client-email').value

            const params = encodeToGetParams({
                firmId,
                daysBack,
                daysToFuture,
                clientEmail
            })

            axios.get(`place-multiple-admin-orders/calculate?${params}`).then((response) => {
                this.orders = response.data.orders
                let res = '';

                response.data.orders.forEach((item) => {
                    res += `
                        <tr>
                            <td>${item.product.name}</td>
                            <td>${item.product.symbol}</td>
                            <td>${item.product.manufacturer}</td>
                            <td>${item.productStock.quantity}</td>
                            <td>${item.orderQuantity}</td>

                        </tr>
                    `
                });


                const result = document.getElementById('result-value')
                result.innerHTML = `
                        <div class="container">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Nazwa towaru</th>
                                <th>Symbol towaru</th>
                                <th>Nazwa producenta towaru</th>
                                <th>Stan magazynowy</th>
                                <th>Ilość towaru, którą powinniśmy zamówićw jednostkach handlowych</th>
                                <th>Ilość opakowań handlowych w jednosce zbiorczej</th>
                                <th>Ilość jednostek zbiorczych bez zaokrąglania</th>
                                <th>Ilość jednostek zbiorczych zaokrąglona w górę</th>
                                <th>Ilość jednostek zbiorczych zaokrąglona w dół</th>
                                <th>ilość jednostek handlowych więcej po zaokrągleniu w górę</th>
                                <th>okres na jaki starczy towaru przy zamówieniu po zaokrągleniu w górę</th>
                                <th>ilość jednostek handlowych mniej po zaokrągleniu w dół</th>
                                <th>okres na jaki starczy towaru przy zamówieniu po zaokrągleniu w dół</th>
                                <th>ostateczna iloć jednostek zbiorczych wybranych w zamówieniu</th>
                                <th>ostateczna ilość jednostek handlowych wybranych w zamówieniu</th>
                              </tr>
                            </thead>
                            <tbody>
                              ${res}
                            </tbody>
                          </table>
                        </div>

                        <button class="btn btn-primary" id="submition-buy-button">
                            Zamów
                        </button>
                    `

                document.querySelector('#submition-buy-button').addEventListener('click', buy)
            }).catch((error) => {
                console.log(error)
            })
        }

        document.querySelector('#submit-button').addEventListener('click', calculate)
    </script>
@endsection

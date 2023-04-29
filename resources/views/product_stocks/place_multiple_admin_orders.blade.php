@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <div class="form-group-default">
        <div>
            <label for="product-name"> Ilość dni wstecz od dzisiaj dla których będziemy liczyć sprzedaż</label>
            <input id="days-back" class="form-control" placeholder="Ilość dni do tyłu">
        </div>
        <div class="mt-5">
            <label for="days-to-future">Ilość dni od dzisiaj dla jakich chcemy przeliczyć zapotrzebowanie</label>
            <input id="days-to-future" class="form-control" placeholder="Ilość dni do przodu">
        </div>
        <div class="mt-5">
            <label for="firm-id">Symbol firmy</label>
            <input class="form-control" id="firm-id" type="text">
        </div>
        <div class="mt-5">
            <label for="client-email">Email kliena</label>
            <input value="info@ephpolska.pl" id="client-email" class="form-control" placeholder="Email klienta">
        </div>
        <button class="btn btn-primary" id="submit-button">
            Oblicz
        </button>

        <div id="spinner"></div>

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
                    const postData = {
                        products: this.orders.map((item) => {
                            return {
                                id: item.product.id,
                                quantity: item.orderQuantity.calculatedQuantity
                            }
                        }).filter((item) => item.quantity > 0),
                        clientEmail: document.getElementById('client-email').value
                    }
                    axios.post('place-multiple-admin-orders/confirm', postData).then((response) => {
                        Swal.fire(
                            'Zamówienie złożone!',
                            'Zamówienie zostało złożone.',
                            'success'
                        )
                    }).catch((error) => {
                        Swal.fire(
                            'Błąd!',
                            'Wystąpił błąd podczas złożenia zamówienia.',
                            'error'
                        )
                    });
                }
            })
        }

        const updateUnitsBox = (id) => {
            const orders = this.orders;
            const item = orders.find((item) => item.product.id === id);

            const resultBox = document.getElementById(`quantity-pack-units-${id}`);
            let quantity = document.getElementById(`quantity-${id}`).value;

            const value = Math.round(quantity * Number(item.product.packing.number_of_sale_units_in_the_pack)) / Number(item.product.packing.number_of_sale_units_in_the_pack) * Number(item.product.packing.number_of_sale_units_in_the_pack);
            document.getElementById(`quantity-${id}`).value = value / Number(item.product.packing.number_of_sale_units_in_the_pack);

            resultBox.innerHTML = value;

            item.orderQuantity.calculatedQuantity = value;
        }

        const calculate = () => {
            const firmSymbol = document.getElementById('firm-id').value
            const daysBack = document.getElementById('days-back').value
            const daysToFuture = document.getElementById('days-to-future').value
            const clientEmail = document.getElementById('client-email').value

            const params = encodeToGetParams({
                firmSymbol,
                daysBack,
                daysToFuture,
                clientEmail
            })

            axios.get(`place-multiple-admin-orders/calculate?${params}`)
                .then(response => {
                    const orders = response.data.orders;
                    let res = '';

                    orders.forEach(item => {
                        const product = item.product;
                        const packing = product.packing;
                        const orderQuantity = item.orderQuantity;
                        const calculatedQuantity = Number(orderQuantity.calculatedQuantity).toFixed(2);
                        const numSaleUnitsInPack = Number(packing.number_of_sale_units_in_the_pack).toFixed(2);
                        const ceilCalcQuantity = Math.ceil(calculatedQuantity / numSaleUnitsInPack);
                        const floorCalcQuantity = Math.floor(calculatedQuantity / numSaleUnitsInPack);
                        const ceilPackUnits = (ceilCalcQuantity * numSaleUnitsInPack).toFixed(2);
                        const floorPackUnits = (floorCalcQuantity * numSaleUnitsInPack).toFixed(2);

                        res += `
                            ${orderQuantity.soldInLastDays}
                            <tr>
                              <td>${product.name}</td> <!-- Nazwa towaru -->
                              <td>${product.symbol}</td> <!-- Symbol towaru -->
                              <td>${product.manufacturer}</td> <!-- Nazwa producenta towaru -->
                              <td>${item.currentQuantity}</td> <!-- Stan magazynowy -->
                              <td>${orderQuantity.soldInLastDays}</td> <!-- ilosci sprzedazy w danym okresie -->
                              <td>${orderQuantity.calculatedQuantity.toFixed(2)}</td> <!-- Ilość towaru, którą powinniśmy zamówićw jednostkach handlowych -->
                              <td>${packing.unit_commercial}</td> <!-- Nazwa jednostki handlowej -->
                              <td>${packing.unit_of_collective}</td> <!-- Nazwa jednostki zbiorczej -->
                              <td>${numSaleUnitsInPack}</td>  <!-- Ilość opakowań handlowych w jednosce zbiorczej -->
                              <td>${(calculatedQuantity / numSaleUnitsInPack).toFixed(2)}</td> <!-- Ilość jednostek zbiorczych bez zaokrąglania -->
                              <td>${ceilCalcQuantity}</td> <!-- Ilość jednostek zbiorczych zaokrąglona w górę -->
                              <td>${floorCalcQuantity}</td> <!-- Ilość jednostek zbiorczych zaokrąglona w dół -->
                              <td>${ceilPackUnits}</td> <!-- ilość jednostek handlowych więcej po zaokrągleniu w górę -->
                              <td>${item.currentQuantity / orderQuantity.inOneDay}</td> <!-- okres na jaki starczy towaru bez zamowienia -->
                              <td>${Math.floor((Number(ceilPackUnits) + Number(item.currentQuantity))  / orderQuantity.inOneDay)}</td> <!-- okres na jaki starczy towaru przy zamówieniu po zaokrągleniu w górę -->
                              <td>${floorPackUnits}</td> <!-- ilość jednostek handlowych mniej po zaokrągleniu w dół jednostek zbiorczych -->
                              <td>${Math.floor((Number(floorPackUnits) + Number(item.currentQuantity))  / orderQuantity.inOneDay)}</td> <!--- okres na jaki starczy towaru przy zamówieniu po zaokrągleniu w dół -->
                              <td>
                                <input type="text" onChange="updateUnitsBox(${product.id})" class="form-control" id="quantity-${product.id}" value="${ceilCalcQuantity}">
                              </td>
                              <td>
                                <span id="quantity-pack-units-${product.id}">${ceilPackUnits}</span>
                              </td>
                            </tr>
                        `;

                    this.orders = orders;

                    return res;
                })

            const result = document.getElementById('result-value')
                result.innerHTML = `
                        <div>
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Nazwa towaru</th>
                                <th>Symbol towaru</th>
                                <th>Nazwa producenta towaru</th>
                                <th>Stan magazynowy</th>
                                <th>ilosc sprzedanego towaru dla zadanego okresu</th>
                                <th>Ilość towaru, którą powinniśmy zamówić w jednostkach handlowych</th>
                                <th>Nazwa jednostki handlowej</th>
                                <th>Nazwa jednostki zbiorczej</th>
                                <th>Ilość opakowań handlowych w jednosce zbiorczej</th>
                                <th>Ilość jednostek zbiorczych bez zaokrąglania</th>
                                <th>Ilość jednostek zbiorczych zaokrąglona w górę</th>
                                <th>Ilość jednostek zbiorczych zaokrąglona w dół</th>
                                <th>ilość jednostek handlowych więcej po zaokrągleniu w górę</th>
                                <th>okres na jaki starczy towaru bez zamowienia</th>
                                <th>okres na jaki starczy towaru przy zamówieniu po zaokrągleniu w górę</th>
                                <th>ilość jednostek handlowych mniej po zaokrągleniu w dół jednostek zbiorczych</th>
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

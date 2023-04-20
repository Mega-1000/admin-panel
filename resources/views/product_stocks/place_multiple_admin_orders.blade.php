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
            <input class="form-control" id="firm-id" type="text">
        </div>
        <div class="mt-5">
            <label for="client-email">Email kliena</label>
            <input value="info@ephpolska.pl" id="client-email" class="form-control" placeholder="Email klienta">
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
                        const calculatedQuantity = Number(orderQuantity.calculatedQuantity);
                        const numSaleUnitsInPack = Number(packing.number_of_sale_units_in_the_pack);
                        const ceilCalcQuantity = Math.ceil(calculatedQuantity / numSaleUnitsInPack);
                        const floorCalcQuantity = Math.floor(calculatedQuantity / numSaleUnitsInPack);
                        const ceilPackUnits = ceilCalcQuantity * numSaleUnitsInPack;
                        const floorPackUnits = floorCalcQuantity * numSaleUnitsInPack;
                        const floorCeilPackUnits = Math.floor(ceilPackUnits / orderQuantity.inOneDay);
                        const floorFloorPackUnits = Math.floor(floorPackUnits / orderQuantity.inOneDay);

                        res += `
                            <tr>
                              <td>${product.name}</td>
                              <td>${product.symbol}</td>
                              <td>${item.currentQuantity}</td>
                              <td>${orderQuantity.calculatedQuantity}</td>
                              <td>${packing.unit_commercial}</td>
                              <td>${packing.unit_of_collective}</td>
                              <td>${numSaleUnitsInPack}</td>
                              <td>${calculatedQuantity / numSaleUnitsInPack}</td>
                              <td>${ceilCalcQuantity}</td>
                              <td>${floorCalcQuantity}</td>
                              <td>${packing.unit_of_collective}</td>
                              <td>${ceilPackUnits}</td>
                              <td>${floorCeilPackUnits}</td>
                              <td>${floorPackUnits}</td>
                              <td>${floorFloorPackUnits}</td>
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
                                <th>Symbol towaru</th
                                <th>Nazwa producenta towaru</th>
                                <th>Stan magazynowy</th>
                                <th>Ilość towaru, którą powinniśmy zamówićw jednostkach handlowych</th>
                                <th>Nazwa jednostki handlowej</th>
                                <th>Ilość opakowań handlowych w jednosce zbiorczej</th>
                                <th>Nazwa jednostki zbiorczej</th>
                                <th>Ilość jednostek zbiorczych bez zaokrąglania</th>
                                <th>Ilość jednostek zbiorczych zaokrąglona w górę</th>
                                <th>Ilość jednostek zbiorczych zaokrąglona w dół</th>
                                <th>Nazwa jednostki zbiorczej</th>
                                <th>ilość jednostek handlowych więcej po zaokrągleniu w górę</th>
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

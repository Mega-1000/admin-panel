<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <title>{{ config('app.chat_name') }}</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

   <style>
       body {
           font-family: Arial, sans-serif;
       }

       .container {
           width: 40%;
           margin: auto;
       }

       @media screen and (max-width: 768px) {
           .container {
               width: 100%;
           }
       }

       table {
           border-collapse: collapse;
       }

       th, td {
           border: 1px solid #ddd;
           padding: 8px;
       }

       th {
           background-color: #4CAF50;
           color: white;
       }

       tr:nth-child(even) {
           background-color: #f2f2f2;
       }

       td {
           vertical-align: middle;
       }

       #chat-container {
           margin-top: 50px;
       }
   </style>

   <script>
       (() => {
           function sortTable(n) {
               let table = document.querySelector("table");
               let switching = true;
               let dir = "asc";
               let switchcount = 0;
               while (switching) {
                   switching = false;
                   let rows = Array.from(table.rows);
                   for (let i = 1; i < (rows.length - 1); i++) {
                       let shouldSwitch = false;
                       let x = rows[i].getElementsByTagName("TD")[n].innerText.toLowerCase().trim();
                       let y = rows[i + 1].getElementsByTagName("TD")[n].innerText.toLowerCase().trim();
                       shouldSwitch = shouldSwitchRows(x, y, dir);
                       if (shouldSwitch) {
                           [rows[i], rows[i + 1]] = [rows[i + 1], rows[i]];
                           switching = true;
                           switchcount++;
                       } else if (switchcount === 0 && dir === "asc") {
                           dir = "desc";
                           switching = true;
                       }
                   }
                   table.innerHTML = '';
                   rows.forEach(row => table.appendChild(row));
               }

               // Add the sorting class to the current column header
               let ths = table.getElementsByTagName("th");
               for (let i = 0; i < ths.length; i++) {
                   ths[i].classList.remove("asc", "desc");
               }
               ths[n].classList.add(dir);
           }

           function shouldSwitchRows(x, y, dir) {
               if (dir === "asc") {
                   return x > y;
               } else if (dir === "desc") {
                   return x < y;
               }
               return false;
           }

           // Add click event listeners to the sort buttons
           window.onload = () => {
               let sortButtons = document.querySelectorAll("th button.btn-primary");
               sortButtons.forEach((button, index) => {
                   button.addEventListener("click", () => sortTable(index));
               });
           };
       })();
   </script>

</head>

<body>
<div>
    @if(session()->get('success'))
        <div class="alert alert-success">
            Pomyślnie stworzono zamówienie i dodano przedstawicieli do chatu
        </div>
    @endif

    <div class="container" id="flex-container">
        <div id="chat-container">
            <div class="alert-success alert">
                Poleć naszą platformę znajomym, a my zaoferujemy Ci 30zł zniżki za każdego nowego użytkownika!
                <br>
                Po więcej informacji kliknij przycisk zobacz więcej
                <br>
                <br>
                <a href="https://mega1000.pl/polec-znajomego" target="_blank" class="btn btn-primary">
                    Zobacz więcej na temat promocji
                </a>
            </div>

            @if($firms->count() == 0)
                <div class="text-center">
                    <h1>Tu za nie długo zaczną wyświetlać się wyniki twojego przetargu.</h1>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>
                                <h5 style="text-align: right">Ceny brutto za m3</h5>
                            </th>
                            @php $iteration = 2; @endphp
                            @foreach($products as $product)
                                <th>
                                    @php
                                        $name = $product->product->name;
                                        $words = explode(' ', $name);
                                        array_shift($words);
                                        $name = implode(' ', $words);
                                    @endphp
                                    {{ $name }}
                                </th>
                                @php $iteration++; @endphp
                            @endforeach
                            <th>Wartość oferty w przypadku wybrania najtańszych opcji</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                    <!-- Table rows will be dynamically added here -->
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
</body>

<script>
    const firms = {!! json_encode($firms->toArray()) !!};
    const products = {!! json_encode($products->toArray()) !!};
    const auction = {!! json_encode($auction ?? null) !!};
    const order = {!! json_encode($order->toArray()) !!};

    function calculateTotalCost(firm, products, auction) {
        let totalCost = 0;

        products.forEach(product => {
            const allProductsToBeDisplayed = firm.firm.products.filter(p => p.product_group === product.product.product_group);

            const offers = [];
            allProductsToBeDisplayed.forEach(p => {
                if (auction && auction.offers.find(offer => offer.firm_id === firm.firm.id && offer.product_id === p.id)) {
                    const chatAuctionOffer = auction.offers.find(offer => offer.firm_id === firm.firm.id && offer.product_id === p.id);
                    offers.push(chatAuctionOffer);
                }
            });

            offers.sort((a, b) => a.basic_price_net - b.basic_price_net);

            const minOffer = offers.length > 0 ? offers[0] : null;
            if (minOffer) {
                const orderItem = order.items.find(item => item.product.product_group === product.product.product_group);
                totalCost += (minOffer.basic_price_net * 1.23) * (orderItem ? orderItem.quantity : 1);
            }
        });

        return Math.round(totalCost / 3.33 * 100) / 100;
    }

    const tableBody = document.getElementById('tableBody');

    firms.forEach(firm => {
        const totalCost = calculateTotalCost(firm, products, auction);

        const row = document.createElement('tr');

        const firmSymbolCell = document.createElement('td');
        firmSymbolCell.textContent = `${firm.firm.symbol}\nOdległość: ${firm.distance} KM`;
        row.appendChild(firmSymbolCell);

        products.forEach(product => {
            const productCell = document.createElement('td');

            const allProductsToBeDisplayed = firm.firm.products.filter(p => p.product_group === product.product.product_group);

            const offers = [];
            allProductsToBeDisplayed.forEach(p => {
                if (auction && auction.offers.find(offer => offer.firm_id === firm.firm.id && offer.product_id === p.id)) {
                    const chatAuctionOffer = auction.offers.find(offer => offer.firm_id === firm.firm.id && offer.product_id === p.id);
                    offers.push(chatAuctionOffer);
                }
            });

            offers.sort((a, b) => a.basic_price_net - b.basic_price_net);

            const offerDetails = offers.map(offer => {
                const product = allProductsToBeDisplayed.find(p => p.id === offer.product_id);
                return `${product.additional_info1}: ${(offer.basic_price_net * 1.23).toFixed(2)}`;
            }).join('<br>');

            productCell.innerHTML = offerDetails || 'No offer';
            if (offers.length > 0) {
                productCell.innerHTML += '<br><span style="color: green">- specjalnie dla ciebie</span>';
            }

            row.appendChild(productCell);
        });

        const totalValueCell = document.createElement('td');
        totalValueCell.textContent = totalCost;
        const orderLink = document.createElement('a');
        orderLink.textContent = 'Wyślij zamówienie na tego producenta';
        orderLink.classList.add('btn', 'btn-primary');
        orderLink.href = `https://admin.mega1000.pl/make-order/${firm.firm.symbol}/${order.id}`;
        totalValueCell.appendChild(document.createElement('br'));
        totalValueCell.appendChild(orderLink);
        row.appendChild(totalValueCell);

        tableBody.appendChild(row);
    });

    const rows = Array.from(tableBody.getElementsByTagName('tr'));
    rows.sort((a, b) => {
        const aTotalValue = parseFloat(a.cells[a.cells.length - 1].textContent);
        const bTotalValue = parseFloat(b.cells[b.cells.length - 1].textContent);
        return aTotalValue - bTotalValue;
    });

    rows.forEach(row => tableBody.appendChild(row));
</script>

@extends('layouts.datatable')

@section('head')
    @livewireStyles
@endsection


<script src="https://cdn.tailwindcss.com" ></script>

<script defer>
    function showHide() {
        setTimeout(() => {
            let elements = document.getElementsByClassName('hidden');

            elements = Array.from(elements);

            for (let i = 0; i < elements.length; i++) {
                elements[i].classList.remove('hidden');
                console.log(elements[i] )
            }
        }, 1000)
    }

    showHide();
</script>
@section('table')
    <form id="form">
        <input id="invoice-number" type="text" class="form-control mt-4 mb-2" placeholder="Wpisz numer faktury">
        <button class="btn btn-primary">
            Szukaj
        </button>

        <div id="results-container">

        </div>
    </form>
    <div>
        <table class="table table-bordered">
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Numer listu
                </th>
                <th>
                    Numer faktury do której dany LP został przydzielony
                </th>
                <th>
                    Data nadania otrzymania
                </th>
                <th>
                    Nr i d
                </th>
                <th>
                    Rzeczywisty koszt transportu brutto
                </th>
                <th>
                    Wartość pobrania
                </th>
                <th>
                    plik
                </th>
                <th>
                    Reszta
                </th>
                <th>
                    Rodzaj
                </th>
                <th>
                    Data faktury
                </th>
                <th>
                    Treść
                </th>
                <th>
                    Dopłata
                </th>
                <th>
                    Znaleziono
                </th>
                <th>
                    Komentarz
                </th>
            </tr>
            @foreach($report as $entry)
                <tr>

                    @foreach(\App\Entities\ShippingPayInReport::getColumns() as $title)
                        <td>
                            {{ $entry->$title }}
                        </td>
                    @endforeach

                    <livewire:shipment-pay-in-report-datatable :reportId="$entry->id" />
                </tr>
            @endforeach
        </table>
    </div>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $report->links() }}
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

    @livewireScripts

    <script>
        $('document').ready(() => {
            document.getElementById('form').addEventListener('submit', searchByInvoiceNumber);
        });

        function searchByInvoiceNumber(e) {
            e.preventDefault();
            const invoiceNumber = document.getElementById('invoice-number').value;

            fetch(`/api/shipment-pay-in-report?invoice_number=${invoiceNumber}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('results-container');

                    container.innerHTML = '';

                    data.forEach(entry => {
                        const row = document.createElement('tr');

                        Object.keys(entry).forEach(key => {
                            const cell = document.createElement('td');

                            cell.innerText = entry[key];

                            row.appendChild(cell);
                        });

                        container.appendChild(row);
                    });
                });
        };
    </script>
@endsection

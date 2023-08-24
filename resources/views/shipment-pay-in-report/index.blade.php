@extends('layouts.datatable')
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
    <table class="table table-bordered">
        <!--
                'symbol_spedytora',
        'numer_listu',
        'nr_faktury_do_ktorej_dany_lp_zostal_przydzielony',
        'data_nadania_otrzymania',
        'nr_i_d',
        'rzeczywisty_koszt_transportu_brutto',
        'wartosc_pobrania',
        'file',
        'reszta',
        'rodzaj',
        'invoice_date',
        'content',
        'surcharge',
        'found',
        -->
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
        </tr>
        @foreach($report as $entry)
            <tr>
                @foreach(ShippingPayInReport::fillable as $title)
                    <td>
                        {{ $entry->$title }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $report->links() }}
    </div>
@endsection

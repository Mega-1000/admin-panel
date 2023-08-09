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
        <tr>
            <th>
                ID
            </th>
            <th>
                Nazwa
            </th>
            <th>
                Identyfikator
            </th>
            <th>
                Typ operacji
            </th>
            <th>
                Obciążenia
            </th>
            <th>
                Uznania
            </th>
            <th>
                Szczegóły operacji
            </th>
        </tr>
        @foreach($expenses as $operation)
            <tr>
                <td>
                    {{ $operation['id'] ?? '' }}
                </td>
                <td>
                    {{ $operation['offer_name'] ?? '' }}
                </td>
                <td>
                    {{ $operation['offer_identification'] ?? '' }}
                </td>
                <td>
                    {{ $operation['operation_type'] ?? '' }}
                </td>
                <td>
                    {{ $operation['debit'] ?? ''}}
                </td>
                <td>
                    {{ $operation['credit'] ?? '' }}
                </td>
                <td>
                    {{ $operation['operation_details'] ?? '' }}
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $expenses->links() }}
    </div>

@endsection

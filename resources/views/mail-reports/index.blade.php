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
                Email adresata
            </th>
            <th>
                Tytuł
            </th>
            <th>
                opis
            </th>
            <th>
                Stworzono
            </th>
        </tr>
        @foreach($mailReports as $operation)
            <tr>
                <td>
                    {{ $operation->id }}
                </td>
                <td>
                    {{ $operation->email }}
                </td>
                <td>
                    {{ $operation->subject }}
                </td>
                <td>
                    {{ $operation->body }}
                </td>
                <td>
                    {{ $operation->created_at }}
                </td>
            </tr>
        @endforeach
    </table>

    <div style="display: block !important;" class="d-flex justify-content-center">
        {{ $mailReports->links() }}
    </div>
@endsection

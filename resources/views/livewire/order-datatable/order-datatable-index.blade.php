<!-- resources/views/livewire/datatable.blade.php -->
<table class="table table-bordered">
    @livewire('order-datatable.order-datatable-columns', ['key' => now()])

    <tbody>
        @foreach($orders['data'] as $order)
            <tr>
                @foreach($columns as $column)
                    <td>{{ $order[$column['label']] }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="100%">
                <div class="d-flex justify-content-between">
                    <div>
                        Showing {{ $orders['from'] }} to {{ $orders['to'] }} out of {{ $orders['total'] }} results
                    </div>
                    <div>
                        @foreach($orders['links'] as $link)
                            <a href="{{ $link['url'] }}" class="btn">{!! $link['label'] !!}</a>
                        @endforeach
                    </div>
                </div>
            </td>
        </tr>
</table>

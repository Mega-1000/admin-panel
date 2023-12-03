<div>
    <form @wire:submit.prevent="updatedPageLength">
        <div class="d-flex">
            Ilość zamówień na stronę:
            <input type="number" class="form-control" placeholder="Ilość zamówień na stronę" wire:model="pageLength">
        </div>
    </form>

    <a href="{{ route('orderDatatableColumnsFiltering') }}" class="btn btn-primary">
        Zarządzaj kolumnami
    </a>

    <table class="table table-bordered">
        <colgroup>
            @foreach($columns as $column)
                <col style="width: {{ $column['size'] }}px;"> <!-- Set an initial width for each column -->
            @endforeach
        </colgroup>

        <thead>
            <tr x-on:dragover.prevent="dragOver">
                @foreach($columns as $column)
                    <th
                        draggable="true"
                        @dragstart="draggedColumn = $event.target"
                        @dragover.prevent="dragOver"
                        @drop="drop($event, '{{ $column['label'] }}')"
                        data-column-name="{{ $column['label'] }}"
                        style="cursor: move;"
                    >
                        <span @click="hideColumn('{{ $column['label'] }}')">
                            {{ $column['label'] }}
                        </span>

                        <br>

                        @if(empty($column['filterComponent']))
                            <input
                                type="text"
                                wire:model.debounce.500ms="filters.{{ $column['label'] }}"
                                placeholder="Search {{ $column['label'] }}"
                                class="w-full text-sm"
                            >
                        @else
                            {!! $column['filterComponent'] !!}
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($orders['data'] as $order)
                <tr>
                    @foreach($columns as $column)
                        <td>{!! $order[$column['label']] !!}</td>
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
</div>

<script src="{{ asset('js/datatable/drag-and-drop.js') }}"></script>
<script>
    function createSimilar(id, orderId) {
        if (window.isCreatingSimilar) {
            return;
        }

        window.isCreatingSimilar = true;

        setTimeout(() => {window.isCreatingSimilar = false}, 1000);
        let action = "{{ route('order_packages.duplicate',['packageId' => '%id']) }}"
        action = action.replace('%id', id)
        $('#createSimilarPackForm').attr('action', action)
        $('#createSimilarPackForm').submit(function (e) {
            e.preventDefault();

            // Disable the submit button to prevent multiple submissions
            var submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true);

            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                success: function (data) {
                    $('#createSimilarPackage').modal('hide');
                    setTimeout(function () {
                        // Re-enable the submit button after a delay
                        submitButton.prop('disabled', false);
                        table.ajax.reload(null, false);
                    }, 10);
                },
                error: function (data) {
                    alert('Coś poszło nie tak');

                    // Re-enable the submit button in case of an error
                    submitButton.prop('disabled', false);
                }
            });
        });

        $('#createSimilarPackage').modal();

    }

    function cancelPackage(id, orderId) {
        if (confirm('Potwierdź anulację paczki')) {
            url = '{{route('order_packages.sendRequestForCancelled', ['orderPackage' => '%id'])}}';
            $.ajax({
                url: url.replace('%id', id),
            }).done(function (data) {
                table.ajax.reload(null, false);
            }).fail(function () {
                alert('Coś poszło nie tak')
            });
        }
    }

    function deletePackage(id, orderId) {
        if (confirm('Potwierdź usunięcię paczki')) {
            url = '{{route('order_packages.destroy', ['id' => '%id'])}}';
            $.ajax({
                url: url.replace('%id', id),
                type: 'delete',
                dataType: 'text',
                contentType: 'application/json',
                data: {
                    'redirect': false
                }
            }).done(function (data) {
                table.ajax.reload();
            }).fail(function () {
                table.ajax.reload();
            });
        }
    }

    function sendPackage(id, orderId) {
        $('#package-' + id).attr("disabled", true);
        $('#order_courier > div > div > div.modal-header > h4 > span').remove();
        $('#order_courier > div > div > div.modal-header > span').remove();

        $.ajax({
            url: `/admin/orders/${orderId}/package/${id}/send`,
        }).done(function (data) {
            setTimeout(() => {
                table.ajax.reload(null, false);
            }, 50);
        }).fail(function () {
            setTimeout(() => {
                table.ajax.reload(null, false);
            }, 50);
        });
    }
</script>

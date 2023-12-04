<div>
    <form @wire:submit.prevent="updatedPageLength">
        <div class="d-flex">
            Ilość zamówień na stronę:
            <input type="number" class="form-control" placeholder="Ilość zamówień na stronę" wire:model="pageLength">
        </div>
    </form>

    <div class="form-group">
        <label for="fs_generator">Generator faktur sprzedaży </label>
        <a name="fs_generator" class="btn btn-success" href="{{ route('orders.fs') }}">Generuj</a>
    </div>

    <div class="col-md-3">
        <h4>Drukuj paczki z grupy:</h4>
        @foreach(app(\App\Services\TaskService::class)->groupTaskByShipmentDate() as $courierCode => $tasksInDay)
            @if(isset(\App\Enums\CourierName::DELIVERY_TYPE_LABELS[$courierCode]))
                <div class="row">
                    <div class="col-lg-12 print-group">
                        {{ \App\Enums\CourierName::DELIVERY_TYPE_LABELS[$courierCode] }}
                        <div>
                            <form target="_blank" method="POST" id="print-auto-package-form"
                                  action="{{ route('orders.findPackageAuto') }}">
                                @csrf()
                                <input name="package_type" id="print-package-type" value="{{ $courierCode }}"
                                       type="hidden">
                                <button type="submit" class="print-auto btn btn-success">Automat</button>
                            </form>
                            <span class="print-list btn btn-primary"
                                  name="{{ $courierCode }}"
                                  data-courierTasks="{{ json_encode($tasksInDay) }}">Z listy</span>
                            <span class="badge"
                                  style="color:#fff !important; background-color:#f96868 !important;">{{ count($tasksInDay['past']) }}</span>
                            @foreach($tasksInDay as $date => $tasks)
                                @if(preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $date))
                                    <span class="badge badge-light">{{ count($tasks) }}</span>
                                @endif
                            @endforeach
                            <span class="badge"
                                  style="color:#fff !important; background-color:#526069 !important;">{{ count($tasksInDay['future']) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    <a href="{{ route('orderDatatableColumnsFiltering') }}" class="btn btn-primary">
        Zarządzaj kolumnami
    </a>

    <table class="table table-borderless" style="overflow-x: auto;">
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
                        @php
                            $data = data_get($order, $column['label']);
                        @endphp

                        <td>{!! $data !!}</td>
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

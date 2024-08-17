<div>
    <div>
        <div>
            <div>
                <button onclick="selectAllOrders()" class="btn btn-primary">
                    Zaznacz wszystkie zamówienia
                </button>
            </div>

            <a href="https://admin.mega1000.pl/polecenia" class="btn btn-primary">
                @if(\App\Entities\ContactApproach::where('done', false)->count() > 0)
                    <div style="color: red">
                        Zobacz listę poleceń i WYKONAJ TELEFONY
                    </div>
                @else
                    <div style="color: green">
                        Lista poleceń jest pusta
                    </div>
                @endif
            </a>

            <a href="{{route('represents.index')}}">Lista reprezentantów wpisanych przez fabryki</a>

            <form @wire:submit.prevent="updatedPageLength">
                <div class="d-flex">
                    Ilość zamówień na stronę:
                    <input type="number" class="form-control" placeholder="Ilość zamówień na stronę" wire:model="pageLength">
                </div>
            </form>

            <a href="{{ route('orderDatatableColumnsFiltering') }}" class="btn btn-primary">
                Zarządzaj kolumnami
            </a>

            <input
                wire:change="updateOrderPackageFilterNumber"
                wire:model.debounce.500ms="orderPackageFilterNumber"
                class="form-control"
                placeholder="Filtruj po numerze paczki"
            >

            <button
                class="btn btn-primary"
                wire:click="updateIsSortingByPreferredInvoiceDate"
                style="background-color: {{ json_decode(auth()->user()->grid_settings)->is_sorting_by_preferred_invoice_date ?? false ? 'green' : 'blue' }}"
            >
                @if (json_decode(auth()->user()->grid_settings)->is_sorting_by_preferred_invoice_date ?? false)
                    Sortowanie po preferowanych datach wystawienia faktury jest włączone
                @else
                    Filtruj po preferowanych datach wystawienia faktury
                @endif
            </button>
            <button
                class="btn btn-primary" wire:click="updateOnlyStyroFilter"
                style="background-color: {{ json_decode(auth()->user()->grid_settings)->only_styro ?? false ? 'green' : 'blue' }}"
            >
                @if (json_decode(auth()->user()->grid_settings)->only_styro ?? false)
                    Filtr tylko styropian jest włączony
                @else
                    Tylko styropian
                @endif
            </button>

            <button
                class="btn btn-primary" wire:click="updateOnlyPaidOffersFilter"
                style="background-color: {{ json_decode(auth()->user()->grid_settings)->only_paid_offers ?? false ? 'green' : 'blue' }}"
            >
                @if (json_decode(auth()->user()->grid_settings)->only_paid_offers ?? false)
                    Filtr tylko opłacone oferty jest włączony
                @else
                    Tylko opłacone oferty
                @endif
            </button>


            <div class="form-group">
                <label for="fs_generator">Generator faktur sprzedaży </label>
                <a name="fs_generator" class="btn btn-success" href="{{ route('orders.fs') }}">Generuj</a>
            </div>
            <div class="form-group">
                <label for="fs_generator">Generator faktur zaliczkowych </label>
                <a name="fs_generator" class="btn btn-success" href="/admin/generate-advanced-invoices">Generuj</a>
            </div>
        </div>

        <div wire:ignore>
            <select style="margin-left: 10px;" class="form-control text-uppercase selectpicker" data-live-search="true"
                    id="choosen-label">
                <option value="" selected="selected">@lang('orders.table.choose_label')</option>
                @php
                    $labelGroups = \App\Entities\LabelGroup::with('activeLabels')->get();
                    $groupedLabels = [];

                    foreach ($labelGroups as $labelGroup) {
                        $groupedLabels[$labelGroup->name] = $labelGroup->activeLabels;
                    }
//                @endphp
                @foreach($groupedLabels as $groupName => $group)
                    <optgroup label="{{ $groupName }}">
                        @foreach($group as $label)
                            <option value="{{ $label->id }}"
                                    data-content="
                                        <span class='order-label label__list' style='color: {{$label->font_color}}; background-color: {{$label->color}}'>
                                            <i class='{{$label->icon_name}}'></i>
                                        </span>
                                        {{ $label->name }}
                                        "
                                    data-timed="{{ $label->timed }}">
                                {{ $label->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            <button class="btn btn-primary" onclick="addLabelsForCheckedOrders()">
                Dodaj etyiety do zaznaconych
            </button>
        </div>

        <form method="POST" action="{{ route('order_packages.closeGroup') }}">
            {{ csrf_field() }}

            <div class="form-group col-md-6" style="margin-bottom: 5px">
                <label for="shipment_group" class="col-md-5" style="margin-top: 10px; padding-left: 0">Wybierz Grupę
                    przesyłek do zamknięcia</label>
                <div class="col-md-4" style="margin-top: 5px">
                    <select class="form-control" name="shipment_group" required>
                        <option disabled selected value="">Grupa przesyłek</option>
                        @foreach(\App\Entities\ShipmentGroup::getOpenGroups() as $group)
                            <option
                                value="{{ $group->id }}">{{ $group->getLabel()}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button name="close_shipment_group" class="btn btn-info" data-toggle="tooltip"
                            data-placement="right"
                            title="Spowoduje zamknięcie obecnej grupy przesyłek"
                            id="close_shipment_group">Zamknij grupę
                    </button>
                </div>
            </div>
        </form>
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
    </div>

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
                            <div>
                                <input
                                    type="text"
                                    wire:change="updateFilters"
                                    wire:model="filters.{{ $column['label'] }}"
                                    placeholder="Search {{ $column['label'] }}"
                                    class="w-full text-sm"
                                    id="filter"
                                >

                                @if($column['label'] === 'id')
                                    {!! view('livewire.order-datatable.nonstandard-columns.filters.additional.id')->render() !!}
                                @endif
                            </div>
                        @else
                            {!! $column['filterComponent'] !!}
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($orders['data'] as $order)
                @php
                    $htmlString = $order['id'];

                    $pattern = '/id=taskOrder-(\d+)/';

                    preg_match($pattern, $htmlString, $matches);
                    $id = $matches[1];
                @endphp
                <tr id="id-{{$id}}">
                    @foreach($columns as $column)
                        @php
                            $data = data_get($order, $column['label']);
                        @endphp

                        <td class="{{ $column['label'] === 'warehouse.symbol' ? 'warehouse-symbol' : '' }}">
                            @if(array_key_exists($column['label'], \App\Enums\OrderDatatableColumnsEnum::ADDITIONAL_VIEWS_COLUMNS) ?? false)
                                    @include(\App\Enums\OrderDatatableColumnsEnum::ADDITIONAL_VIEWS_COLUMNS[$column['label']], ['column' => $column, 'data' => $data, 'wholeOrder' => $order])
                            @endif
                            {!! $data !!}
                        </td>
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
                                @if ($link['active'])
                                    <span>{{ $link['label'] }}</span>
                                @else
                                    @php
                                        $url = '?page=' . $link['label'] . '&';
                                        if ($link['label'] === 'Next &raquo;') {
                                            $url = '?page=' . ($orders['current_page'] + 1) . '&';
                                        }

                                        if ($link['label'] === '&laquo; Previous') {
                                            $url = '?page=' . ($orders['current_page'] - 1) . '&';
                                        }
                                    @endphp

                                    <a href="{{ $url }}" class="btn">{!! $link['label'] !!}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
    </table>
</div>


@if($this->shouldRedirect)
    <script>
        alert('Zmieniono grupę przesyłek');
        window.location.href = {{ route('orders.index', ['applyFiltersFromQuery' => true]) }};
    </script>
@endif


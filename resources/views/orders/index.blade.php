@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('orders.title')
    </h1>
    <style>
        #dateTask {
            -webkit-border-radius: 0px !important;
            -moz-border-radius: 0px !important;
            border-radius: 0px !important;
        }
        .pointer {
            cursor: pointer;
        }
        .ui-tooltip {
            width: 700px !important;
            max-width: 700px !important;
        }

        .order-label {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .order-label i {
            font-size: 1.5rem
        }

        #filterByWarehouseMegaOlawa {
            position: absolute;
            top: 33px;
            right: 5px;
        }

        #btn-print:hover {
            background-color: #2ecc71;
            color: #FFF;
        }

        .input_div__label-search {
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        .spedition-exchange-generated-link {
            margin-left: 10px;
            display: inline-block;
        }

        .transport-exchange {
            border: 1px solid #d0d0d0;
            padding: 5px;
        }

        .transport-exchange-offer {
            cursor: pointer;
        }

        .transport-exchange-offer:not(:last-child) {
            border-bottom: 1px solid #8f8f8f;
        }

        .transport-exchange__spedition-chosen {
            background-color: rgba(0, 192, 22, 0.65);
        }

        .btn.btn-xs {
            padding: 2px 10px;
            font-size: 12px;
            display: inline-block;
            margin-right: 2px;
        }

        .btn.btn-xs:last-child {
            margin-right: 0;
        }

        .modal-content {
            border: 1px solid #000;
        }
    </style>
@endsection

@section('table')
    <div class="modal fade" tabindex="-1" id="magazine" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Proszę wybrac magazyn: </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select id="selectWarehouse" class="form-control">
                            <option value="0" selected="selected">Wybierz magazyn</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}" class="warehouseSelect" id="warehouseSelect">{{$warehouse->symbol}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="set-magazine" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Proszę wybrac magazyn: </h4>
                </div>
                <div class="modal-body">
                    <form id="addWarehouse" class="form-group">
                        <select required name="warehouse_id" class="form-control">
                            <option value="" selected="selected">Wybierz magazyn</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}" class="warehouseSelect">{{$warehouse->symbol}}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="addWarehouse" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="addStorekeeperTimeError" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas pobierania listy pracowników</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="errorUpdate" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas akceptacji zadania</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="errorGetToUpdate" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas pobierania danych zadania</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="addStorekeeperTime" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Poniżej znajduje się lista pracowników: </h4>
                </div>
                <div class="modal-body">
                    <form id="workHours" action="{{ action('UserWorksController@addWorkHours') }}" method="POST">
                        @csrf
                        <table style="width: 100%;" id="storekeepers" class="table-active">
                            <thead>
                            <tr>
                                <th>Lp.</th>
                                <th>Nazwa</th>
                                <th>Imię</th>
                                <th>Nazwisko</th>
                                <th>Godzina rozpoczęcia</th>
                                <th>Godzina zakończenia</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="4"><span style="color:red">UWAGA: Jeśli chcesz zmienić daty dla wszystkich pracowników uzupełnij komórki na samej górze.</span>
                                </td>
                                <td><input class="form-control" type="time" name="start" id="start"></td>
                                <td><input class="form-control" type="time" name="end" id="end"></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="workHours" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="addNewTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Dodaj zadanie</h4>
                </div>
                <div class="modal-body">
                    <form id="addTask" action="{{ action('TasksController@addNewTask') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="addTask" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="updateTaskModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zaktualizuj zadanie</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="updateTaskDetail" name="update" value="1"
                            class="btn btn-success pull-right">Wyślij
                    </button>
                    <button type="submit" form="updateTaskDetail" name="delete" value="1"
                            class="btn btn-danger pull-right">Usuń
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="editTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zaktualizuj zadanie</h4>
                </div>
                <div class="modal-body">
                    <form id="updateTask" method="POST">
                        {{method_field('put')}}
                        @csrf
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="updateTask" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="acceptTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zadania oczekujące akceptacji</h4>
                    <span>Jeśli chcesz akceptować zadanie kliknij - AKCEPTUJ.</span>
                    <span>W przypadku odrzucenia wykonania zadania należy kliknąć - ODRZUĆ. Poskutkuje to tym, że zadanie zostanie przeniesione na kolejny dzień, na tą samą godzinę, jeśli chcesz aby było wykonane w innych godzinach, prosimy o zmianę godziny.</span>
                </div>
                <div class="modal-body">

                    <table style="width: 100%;" id="tasksToAccept" class="table-active">
                        <thead>
                        <tr>
                            <th width="5%">Lp.</th>
                            <th width="5%">Nazwa</th>
                            <th width="15%">Godzina rozpoczęcia</th>
                            <th width="15%">Godzina zakończenia</th>
                            <th width="30%">Status</th>
                            <th width="5%">Akceptuj</th>
                            <th width="5%">Odrzuć</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="moveTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form id="moveTaskForm" action="" method="POST">
                        {{method_field('put')}}
                        @csrf
                        <input type="hidden" name="old_resource">
                        <input type="hidden" name="new_resource">
                        <input type="hidden" name="view_type" id="view_type">
                        <input type="hidden" name="active_start" id="active_start">
                        <input type="hidden" name="start">
                        <input type="hidden" name="end">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="moveTaskForm" class="btn btn-success pull-right" name="move" value="1">
                        Przesuń
                    </button>
                    <button type="submit" form="moveTaskForm" class="btn btn-success pull-right" name="moveAllRight"
                            value="1">Przesuń wszystkie w prawo
                    </button>
                    <button type="submit" form="moveTaskForm" class="btn btn-success pull-right" name="moveAllLeft"
                            value="1">Przesuń wszystkie w lewo
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <button class="btn btn-warning" onclick="clearFilters()">Wyszczyść filtry</button>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <input type="hidden" id="spedition-exchange-selected-items" value="[]">
        <button class="btn btn-secondary" onclick="gerateSpeditionExchangeLink()">Giełda spedycyjna (generuj link)</button>
        <div class="spedition-exchange-generated-link"></div>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <a name="actualization-price" target="_blank" class="btn btn-success" href="/admin/actualizationPrice">Wyślij prośbę o aktualizację cen</a>
    </div>
    <div class="form-group">
        <label for="protocols">Protokoły z dnia dzisiejszego</label>
        <a name="protocols" target="_blank" class="btn btn-success" href="/admin/orderPackages/inpost/protocols">Inpost</a>
        <a name="protocols" target="_blank" class="btn btn-success" href="/admin/orderPackages/dpd/protocols">DPD</a>
        <a name="protocols" target="_blank" class="btn btn-success" href="/admin/orderPackages/pocztex/protocols">Pocztex</a>
        <a name="protocols" target="_blank" class="btn btn-success" href="/admin/orderPackages/apaczka/protocols">Apaczka</a>
        <a name="protocols" target="_blank" class="btn btn-success" href="/admin/orderPackages/jas/protocols">Jas</a>
        <a name="protocols" target="_blank" class="btn btn-success" href="/admin/orderPackages/gielda/protocols">Gielda</a>
        <a name="protocols" target="_blank" class="btn btn-info" href="/admin/orderPackages/all/protocols">Wszystkie protokoły</a>
    </div>
    <div class="form-group">
        <label for="send_courier">Wyślij kurierów: </label>
        <a name="send_courier" class="btn btn-success" href="/admin/orderPackages/INPOST/send">Inpost</a>
        <a name="send_courier" class="btn btn-success" href="/admin/orderPackages/DPD/send">DPD</a>
        <a name="send_courier" class="btn btn-success" href="/admin/orderPackages/POCZTEX/send">Pocztex</a>
        <a name="send_courier" class="btn btn-success" href="/admin/orderPackages/APACZKA/send">Apaczka</a>
        <a name="send_courier" class="btn btn-success" href="/admin/orderPackages/JAS/send">Jas</a>
        <a name="send_courier" class="btn btn-info" href="/admin/orderPackages/ALL/send">Wyślij wszystkie</a>
    </div>
    <div class="form-group">
        <label for="send_courier">Szablony paczek:</label>
        <a name="send_courier" class="btn btn-success" href="/admin/packageTemplates/">Lista Szablonów</a>
        <a name="send_courier" class="btn btn-info" href="/admin/packageTemplates/create">Dodaj szablon</a>
        <label style="margin-left: 20px" for="container_type">Rodzaje przesyłek:</label>
        <a style="margin-left: 5px" name="container_type" class="btn btn-success" href="/admin/containerTypes/">Lista rodzajów przesyłek</a>
        <a name="container_type" class="btn btn-info" href="/admin/containerTypes/create">Dodaj rodzaj przesyłki</a>
    </div>
    <div>
        <label for="content_type">Typy zawartości przesyłek:</label>
        <a name="content_type" class="btn btn-success" href="/admin/contentTypes/">Lista Typów zawartości przesyłek</a>
        <a name="content_type" class="btn btn-info" href="/admin/contentTypes/create">Dodaj typ zawartości przesyłki</a>
        <label style="margin-left: 20px" for="packing_type">Typy opakowań przesyłek:</label>
        <a style="margin-left: 5px" name="packing_type" class="btn btn-success" href="/admin/packingTypes/">Lista Typów opakowań przesyłek</a>
        <a name="packing_type" class="btn btn-info" href="/admin/packingTypes/create">Dodaj typ opakowania przesyłki</a>
    </div>
    <br>
    <div class="form-group">
        <label for="send_courier">Import z SELLO: </label>
        <a name="send_courier" class="btn btn-success" href="/admin/sello-import/">Importuj</a>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <button onclick="addLabel()" type="button" class="btn btn-primary">@lang('orders.table.save_label')</button>
        <select style="margin-left: 10px;" class="form-control text-uppercase" id="choosen-label">
            <option value="" selected="selected">@lang('orders.table.choose_label')</option>
            @foreach($groupedLabels as $groupName => $group)
                <optgroup label="{{ $groupName }}">
                    @foreach($group as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <button class="btn btn-text">Filtr zamówień</button>
        <select style="margin-left: 10px;" class="form-control text-uppercase orderFilter" id="orderFilter">
            <option value="ALL">Wszystkie</option>
            <option value="PENDING">Wszystkie zlecenia wyłączając bez realizacji oraz zakończone</option>
            <option value="WITHOUT_REALIZATION">Bez realizacji</option>
            <option value="COMPLETED">Oferta zakończona</option>
        </select>
    </div>
    <div class="form-row">
        <div class="col-md-3 mb-3">
            <div class="form-group">
                <label for="searchLP">Szukaj po LP</label>
                <input type="search" class="form-control" name="searchLP" id="searchLP">
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="form-group">
                <label for="searchOrderValue">Szukaj po Wartości zamówienia</label>
                <input type="search" class="form-control" name="searchOrderValue" id="searchOrderValue">
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="form-group">
                <label for="searchPayment">Szukaj po Zaliczce</label>
                <input type="search" class="form-control" name="searchPayment" id="searchPayment">
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="form-group">
                <label for="searchLeft">Szukaj po Pozostały do zapłaty</label>
                <input type="search" class="form-control" name="searchLeft" id="searchLeft">
            </div>
        </div>
    </div>
    <table id="dataTable" class="table table-hover spacious-container ordersTable">
        <thead>
        <tr>
            <th></th>
            <th>@lang('orders.table.spedition_exchange_invoiced_selector')</th>
            <th>
                <div><span>@lang('orders.table.packages_sent')</span></div>
                <div class="input_div">
                    <select class="columnSearchSelect" id="columnSearch-packages_sent">
                        <option value="">Wszystkie</option>
                        @foreach($couriers as $courier)
                            <option value="{{$courier->delivery_courier_name}}">{{$courier->delivery_courier_name}}</option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.packages_not_sent')</span></div>
                <div class="input_div">
                    <select class="columnSearchSelect" id="columnSearch-packages_not_sent">
                        <option value="">Wszystkie</option>
                        @foreach($couriers as $courier)
                            <option value="{{$courier->delivery_courier_name}}">{{$courier->delivery_courier_name}}</option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>@lang('orders.table.print')</th>
            <th>
                <div><span>@lang('orders.table.name')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-name"/>
                </div>
            <th>
                <div><span>@lang('orders.table.orderDate')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-orderDate"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.orderId')</span></div>
                <div class="input_div">
                    <span title="KLIKNIECIE TEGO POLA USUWA FILTRY"><input type="text" id="columnSearch-orderId"/></span>
                </div>
            </th>
            <th>
                <span id="columnSearch-actions">@lang('orders.table.actions')</span>
            </th>
            <th>@lang('orders.table.section')</th>
            <th>
                <div><span>@lang('orders.table.statusName')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-statusName"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.symbol')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-symbol"/>
                    <button class="btn btn-default" id="filterByWarehouseMegaOlawa" onclick="event.stopPropagation(); filterByWarehouseMegaOlawa()">M</button>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.customer_notices')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-customer_notices"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.consultant_notices')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-consultant_notices"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.clientPhone')</span></div>
                <div class="input_div">
                    <span title="KLIKNIJ PONOWNIE ABY USUNĄĆ SPACJE I MYŚLNIKI"><input type="text" id="columnSearch-clientPhone"/></span>
                </div>
            <th>
                <div><span>@lang('orders.table.clientEmail')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-clientEmail"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.clientFirstname')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-clientFirstname"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.clientLastname')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-clientLastname"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.nick_allegro')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-nick_allegro"/>
                </div>
            </th>
            <th>
                @lang('orders.table.profit')
            </th>
            <th>
                <div><span>@lang('orders.table.weight')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-weight"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.products_value_gross')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-products_value_gross"/>
                </div>
            </th>
            <th>@lang('orders.table.additional_service_cost')</th>
            <th>
                <div><span>@lang('orders.table.additional_cash_on_delivery_cost')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-additional_cash_on_delivery_cost"/>
                </div>
            </th>
            <th>@lang('orders.table.shipment_price_for_client')</th>
            <th>
                <div><span>@lang('orders.table.shipment_price_for_us')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-shipment_price_for_us"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.sum_of_gross_values')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-sum_of_gross_values"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.sum_of_payments')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-sum_of_payments"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.left_to_pay')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-left_to_pay"/>
                </div>
            </th>
            <th>@lang('orders.table.transport_exchange_offers')</th>
            <th>@lang('orders.table.production_date')</th>
            <th>
                <div><span>@lang('orders.table.shipment_date')</span></div>
                <div class="input_div">
                    <select class="columnSearchSelect" id="columnSearch-shipment_date">
                        <option value="all">Wszystkie</option>
                        <option value="yesterday">Wczoraj</option>
                        <option value="today">Dzisiaj</option>
                        <option value="tomorrow">Jutro</option>
                        <option value="from_tomorrow">Wszystkie od jutra</option>
                    </select>
                </div>
            </th>
            @foreach($customColumnLabels as $key => $label)
                <th>
                    <div><span>@lang('orders.table.label_' . str_replace(" ", "_", $key))</span></div>
                    <div class="input_div input_div__label-search">
                        <filter-by-labels-in-group
                            group-name="{{ $key }}"
                        />
                    </div>
                </th>
            @endforeach
            <th>@lang('orders.table.invoices')</th>
            <th>@lang('orders.table.invoice_gross_sum')</th>
            <th>@lang('orders.table.icons')</th>
            <th>@lang('orders.table.consultant_earning')</th>
            <th>@lang('orders.table.real_cost_for_company')</th>
            <th>@lang('orders.table.difference')</th>
            <th>@lang('orders.table.correction_amount')</th>
            <th>@lang('orders.table.correction_description')</th>
            <th>@lang('orders.table.document_number')</th>
            <th></th>
        </tr>
        </thead>
    </table>
@endsection

@section('datatable-scripts')
    <script src="//cdn.jsdelivr.net/npm/jquery.scrollto@2.1.2/jquery.scrollTo.min.js"></script>
    <script>

        $(function() {
            if (localStorage.getItem("filter") != null) {
                $("#orderFilter").val(localStorage.getItem('filter')).prop('selected', true);
            }
        });
        const deleteRecord = (id) => {
            $('#delete_form')[0].action = "{{ url()->current() }}/" + id;
            $('#delete_modal').modal('show');
        };
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        var visibility = {
            'true': '',
            'false': 'noVis'
        }

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='javascript:void();'>Zamówienia</a></li>");

        $.fn.dataTable.ext.errMode = 'throw';

        function sendPackage(id, orderId){
            $('#package-'+id).attr("disabled", true);
            $('#order_courier > div > div > div.modal-header > h4 > span').remove();
            $('#order_courier > div > div > div.modal-header > span').remove();
            $.ajax({
                url: '/admin/orders/'+orderId+'/package/'+id+'/send',
            }).done(function(data) {
                $('#order_courier').modal('show');
                if(data.message == 'Kurier zostanie zamówiony w przeciągu kilku minut' || data.message == null) {
                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Kurier zostanie zamówiony w przeciągu kilku minut</span>');
                } else {
                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Jedno z wymaganych pól nie zostało zdefiniowane:</span>');
                    $('#order_courier > div > div > div.modal-header').append('<span style="color:red;">'+data.message.message+'</span><br>');
                }
                $('#package-'+id).attr("disabled", false);
                $('#success-ok').on('click', function(){
                    window.location.href= '/admin/orders?order_id='+orderId;
                });
            }).fail(function() {
                $('#package-'+id).attr("disabled", false);
                $('#order_courier_problem').modal('show');
                $('#problem-ok').on('click', function() {
                    window.location.href = '/admin/orders?order_id='+orderId;
                });
            });
        }

        // DataTable
        window.table = table = $('#dataTable').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            stateSave: true,
            "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "Wszystkie"]],
            "oLanguage": {
                "sSearch": "Szukaj: "
            },
            columnDefs: [
                {className: "dt-center", targets: "_all"},
                {
                    'targets': 8,
                    'createdCell': function (td, cellData, rowData, row, col) {
                        $(td).attr('id', 'action-'+rowData.orderId);
                    }
                },
                {
                    'targets': 13,
                    'createdCell': function (td, cellData, rowData, row, col) {
                        $(td).attr('id', 'customer_notices-'+rowData.orderId);
                    }
                }
            ],
            responsive: true,
            'fnCreatedRow': function (nRow, aData, iDataIndex) {
                $(nRow).attr('id', 'id-' + aData.orderId);
            },
            dom: 'Bfrtip',
            language: {
                buttons: {
                    pageLength: {
                        _: "Pokaż %d zamówień",
                        '-1': "Wszystkie"
                    }
                }
            },
            buttons: [
                'pageLength',
                {
                    extend: 'colvis',
                    text: 'Widzialność kolumn',
                    columns: ':not(.noVis)'
                },
                {
                    extend: 'colvisGroup',
                    text: 'Pokaż wszystkie',
                    show: ':hidden'
                },
            ],
            order: [[7, "desc"]],//ma być sortowane po id czyli 7 kolumna!!!
            ajax: {
                url: '{!! route('orders.datatable') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            rowCallback: function( row, data, index ) {
                if(data.login == 'magazyn-olawa@mega1000.pl') {
                    $('td', row).css('background-color', 'rgb(0, 0, 255, 0.1)');
                }
            },
            drawCallback: function( settings ) {
                setTimeout(function () {
                    $('.ui-tooltip-content').parent().remove()

                    $('[data-toggle="tooltip"]').tooltip();
                    $('[data-toggle="transport-exchange-tooltip"]').tooltip();
                    $('[data-toggle="label-tooltip"]').tooltip();
                }, 1000);

                $(".spedition-exchange-selector-no-invoice").on('click', function () {
                    updateSpeditionExchangeSelectedItem(this.value, 'no-invoice');
                });

                $(".spedition-exchange-selector-invoiced").on('click', function () {
                    updateSpeditionExchangeSelectedItem(this.value, 'invoiced');
                });
            },

            columns: [
                {
                    data: 'orderId',
                    name: 'mark',
                    orderable: false,
                    render: function (id) {
                        return '<input class="order-id-checkbox" value="'+id+'" type="checkbox">';
                    },
                },
                {
                    data: 'orderId',
                    name: 'spedition_exchange_invoiced_selector',
                    orderable: false,
                    render: function (id) {
                        return '<input class="spedition-exchange-selector-no-invoice" value="'+id+'" type="checkbox"><br><input class="spedition-exchange-selector-invoiced" value="'+id+'" type="checkbox">';
                    },
                },
                {
                    data: 'packages',
                    name: 'packages_sent',
                    searchable: false,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '';
                        $.each(data, function(key, value) {
                            if (value.real_cost_for_company > value.cost_for_company) {
                                 html = '<div style="border: solid red 4px" >'
                            }
                            if ((value.status === 'SENDING' && value.status !== 'CANCELLED') || (value.status === 'DELIVERED' && value.status !== 'CANCELLED')) {
                                html += '<div style="display: flex; align-items: center; flex-direction: column;" > ' +
                                    '<div style="display: flex; align-items: center;">' +
                                    '<p style="margin: 8px 0px 0px 0px;">' + row.orderId + '/' + value.number + '</p>'
                                let name = value.container_type
                                if (value.symbol) {
                                    name = value.symbol;
                                }
                                html += '<p style="margin: 8px 8px 0px 8px;">' + name + '</p> </div> '
                                if (value.letter_number === null) {
                                    html += '<a href="javascript:void()"><p>Brak listu przewozowego</p></a>';
                                }else {
                                    if (value.delivery_courier_name === 'INPOST') {
                                        html += '<a target="_blank" href="/storage/inpost/stickers/sticker' + value.letter_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                    } else if (value.delivery_courier_name === 'DPD') {
                                        html += '<a target="_blank" href="/storage/dpd/protocols/protocol' + value.letter_number + '.pdf"><p>'+value.sending_number+'</p></a>';
                                        html += '<a target="_blank" href="/storage/dpd/stickers/sticker' + value.letter_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                    } else if (value.delivery_courier_name === 'POCZTEX') {
                                        html += '<a target="_blank" href="/storage/pocztex/protocols/protocol' + value.sending_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                    } else if (value.delivery_courier_name === 'JAS') {
                                        html += '<a target="_blank" href="/storage/jas/protocols/protocol' + value.sending_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                        html += '<a target="_blank" href="/storage/jas/labels/label' + value.sending_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                    } else if (value.delivery_courier_name === 'GIELDA') {
                                        html += '<a target="_blank" href="/storage/gielda/stickers/sticker' + value.letter_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                    } else if (value.delivery_courier_name === 'ODBIOR_OSOBISTY') {
                                        html += '<a target="_blank" href="/storage/odbior_osobisty/stickers/sticker' + value.letter_number + '.pdf"><p>'+value.letter_number+'</p></a>';
                                    }
                                }
                                html += '</div>';
                            }
                        });
                        return html;
                    }
                },
                {
                    data: null,
                    name: 'packages_not_sent',
                    searchable: false,
                    orderable: false,
                    render: function ( order, type, row ) {
                        let data = order.packages
                        var html = ''
                        if (data.length != 0) {
                            if (order.otherPackages && order.otherPackages.find(el => el.type == 'not_calculable')) {
                                html = '<div style="border: solid blue 4px" >'
                            } else {
                                html = '<div style="border: solid green 4px" >'
                            }
                        }
                        $.each(data, function (key, value) {
                            if (value.status !== 'SENDING' && value.status !== 'DELIVERED' && value.status !== 'CANCELLED') {
                                html += '<div style="display: flex; align-items: center; flex-direction: column;" > ' +
                                    '<div style="display: flex; align-items: stretch;">' +
                                    '<p style="margin: 8px 0px 0px 0px;"> ' + row.orderId + '/' + value.number + '</p>'
                                let name = value.container_type
                                if (value.symbol) {
                                    name = value.symbol;
                                }
                                html += '<p style="margin: 8px 8px 0px 8px;">' + name + '</p> </div> '

                                if (value.status === 'WAITING_FOR_CANCELLED') {
                                    html += '<p>WYSŁANO DO ANULACJI</p>';
                                }
                                if (value.status === 'REJECT_CANCELLED') {
                                    html += '<p style="color:red;">ANULACJA ODRZUCONA</p>';
                                }
                                if (value.letter_number === null) {
                                    if (value.status !== 'CANCELLED' && value.status !== 'WAITING_FOR_CANCELLED' && value.delivery_courier_name !== 'GIELDA' && value.service_courier_name !== 'GIELDA' && value.delivery_courier_name !== 'ODBIOR_OSOBISTY' && value.service_courier_name !== 'ODBIOR_OSOBISTY') {
                                        html += '<button class="btn btn-success" id="package-' + value.id + '" onclick="sendPackage(' + value.id + ',' + value.order_id + ')">Wyślij</button>';
                                    }
                                } else {
                                    if (value.delivery_courier_name === 'INPOST') {
                                        html += '<a target="_blank" href="/storage/inpost/stickers/sticker' + value.letter_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    } else if (value.delivery_courier_name === 'DPD') {
                                        html += '<a target="_blank" href="/storage/dpd/protocols/protocol' + value.letter_number + '.pdf"><p>' + value.sending_number + '</p></a>';
                                        html += '<a target="_blank" href="/storage/dpd/stickers/sticker' + value.letter_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    } else if (value.delivery_courier_name === 'POCZTEX') {
                                        html += '<a target="_blank" href="/storage/pocztex/protocols/protocol' + value.sending_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    } else if (value.delivery_courier_name === 'JAS') {
                                        html += '<a target="_blank" href="/storage/jas/protocols/protocol' + value.sending_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                        html += '<a target="_blank" href="/storage/jas/labels/label' + value.sending_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    } else if (value.delivery_courier_name === 'GIELDA') {
                                        html += '<a target="_blank" href="/storage/gielda/stickers/sticker' + value.letter_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    } else if (value.delivery_courier_name === 'ODBIOR_OSOBISTY') {
                                        html += '<a target="_blank" href="/storage/odbior_osobisty/stickers/sticker' + value.letter_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    }
                                }
                                html += '</div>';
                            }
                        });
                        return html;
                    }
                },
                {
                    data: 'token',
                    name: 'print',
                    orderable: false,
                    render: function(token, row, data) {
                        let html = '';
                        if(data.print_order == '0') {
                            html = '<a href="/admin/orders/' + token + '/print" target="_blank" class="btn btn-default" id="btn-print">W</a>';
                        } else {
                            html = '<a href="/admin/orders/' + token + '/print" target="_blank" class="btn btn-success">W</a>';
                        }
                        return html;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    defaultContent: ''

                },
                {
                    data: 'orderDate',
                    name: 'orderDate'
                },
                {
                    data: 'orderId',
                    name: 'orderId',
                    render: function(orderId, row, data){
                        if(data.master_order_id == null) {
                            let html = '';
                            if (data.sello_id) {
                                html += ' (A)'
                            }
                            for(let i = 0; i < data.connected.length; i++) {
                                html += '<span style="display: block;">(P)' + data.connected[i].id + '</span>';
                            }
                            return '<a target="_blank" href="/admin/planning/timetable?id=taskOrder-'+orderId+'">(G)'+orderId+'</a>' + html;
                        } else {
                            return '<a target="_blank" href="/admin/planning/timetable?id=taskOrder-'+orderId+'">(P)'+orderId+'</a><span style="display: block;">(G)' + data.master_order_id + '</span>';
                        }

                    }
                },
                {
                    data: 'orderId',
                    name: 'actions',
                    orderable: false,
                    render: function (id) {
                        let html = '';
                        html += '<button id="moveButton-'+id+'" class="btn btn-sm btn-warning edit" onclick="moveData('+id+')">Przenieś dane stąd</button>';
                        html += '<button id="moveButtonAjax-'+id+'" class="btn btn-sm btn-success btn-move edit hidden" onclick="moveDataAjax('+id+')">Przenieś dane tutaj</button>';
                        html += '<a href="{{ url()->current() }}/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                        html += '<i class="voyager-edit"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                        html += '</a>';
                        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord(' + id + ')">';
                        html += '<i class="voyager-trash"></i>';
                        html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                        html += '</button>';
                        @endif

                            return html;
                    }
                },
                {
                    data: 'orderId',
                    name: 'section',
                    orderable: false,
                    render: function() {
                        let html = 'tymczasowy brak';
                        return html;
                    }
                },
                {
                    data: 'statusName',
                    name: 'statusName',
                    render: function(field, type, row) {
                        if(field == null) {
                            return "";
                        }

                        let color = "";

                        switch(field) {
                            case "przyjete zapytanie ofertowe":
                                color = "red";
                                break;
                            case "oferta bez realizacji":
                                color = "#AAB78F";
                                break;
                            case "oferta zakonczona":
                                color = "green";
                                break;
                            case "w trakcie realizacji":
                                color = "blue";
                                break;
                            case "w trakcie analizowania przez konsultanta":
                            case "mozliwa do realizacji":
                            case "mozliwa do realizacji kominy":
                            case "oferta oczekujaca":
                                color = "blue";
                                if (row.remainder_date) {
                                    if (moment(row.remainder_date).isBefore()) {
                                        color = "orange";
                                    }
                                }
                                break;
                        }

                        return "<span style='color: " + color + "'>" + field + "</span>";
                    }
                },
                {

                    data: 'symbol',
                    name: 'symbol',
                    defaultContent: '',
                    render: function (data) {
                        var warehouse = '';
                        if(data !== null){
                            warehouse = '<a href="/admin/warehouses/'+data+'/editBySymbol">'+data+'</a>';
                        } else {
                            warehouse = '';
                        }
                        return warehouse;
                    }
                },
                {
                    data: 'customer_notices',
                    name: 'customer_notices'
                },
                {
                    data: 'consultant_notices',
                    name: 'consultant_notices',
                    render: function (data, type, row){
                        if(data !== null) {
                            var text = data;
                            var shortText = data.substr(0, 49) + "...";
                            $('#customer_notices-'+row.orderId).hover(function () {
                                $('#customer_notices-'+row.orderId).text(text);
                            }, function () {
                                $('#customer_notices-'+row.orderId).text(shortText);
                            });
                            $('#customer_notices-'+row.orderId).text(shortText);
                        }
                        return data;
                    }
                },
                {
                    data: 'clientPhone',
                    name: 'clientPhone',
                    render: function(data, type, row) {
                        var phone = row['clientPhone'];
                        var email = row['clientEmail'];

                        if(email === null){
                            email = 'Brak adresu email.'
                        }

                        let tooltipTitle = email;
                        tooltipTitle += '&#013;';
                        tooltipTitle += '&#013;' + 'Dane do wysylki:';
                        $.each(row.addresses[0],function( index, value){
                            if(index !== 'type' && index !== 'created_at' && index !== 'updated_at') {
                                if(value === null){
                                    tooltipTitle += ' Brak';
                                } else {
                                    tooltipTitle += ' ' + value + ',';
                                }
                            }
                        });
                        tooltipTitle += '&#013;' + 'Dane do faktury:';
                        $.each(row.addresses[1],function( index, value){
                            if(index !== 'type' && index !== 'created_at' && index !== 'updated_at') {
                                if(value === null){
                                    tooltipTitle += ' Brak,';
                                } else {
                                    tooltipTitle += ' ' + value + ',';
                                }
                            }
                        });
                        tooltipTitle += '&#013;';
                        let html = '';

                        if (data) {
                            html += '<button class="btn btn-default btn-xs" onclick="filterByPhone('+data+')">F</button><button class="btn btn-default btn-xs" onclick="clearAndfilterByPhone('+data+')">OF</button>';
                        }

                        html += '<p data-toggle="tooltip" data-html="true" title="' + tooltipTitle +'">' + phone + '</p>';

                        return html;
                    }
                },
                {
                    data: 'clientEmail',
                    name: 'clientEmail',
                    render: function(data, type, row) {
                        var email = row['clientEmail'];

                        let html = email;

                        return html;
                    }
                },
                {
                    data: 'clientFirstname',
                    name: 'clientFirstname',
                    render: function(data, type, row) {
                        var firstname = row['clientFirstname'];

                        let html = firstname;

                        return html;
                    }
                },
                {
                    data: 'clientLastname',
                    name: 'clientLastname',
                    render: function(data, type, row) {
                        var lastname = row['clientLastname'];

                        let html = lastname;

                        return html;
                    }
                },
                {
                    data: 'nick_allegro',
                    name: 'nick_allegro',
                },
                {
                    data: 'orderId',
                    name: 'profit',
                    searchable: false,
                    orderable: false,
                    render: function(date, type, row) {
                        let sumOfSelling = 0;
                        let sumOfPurchase = 0;
                        var items = row['items'];

                        for (let index = 0; index < items.length; index++) {
                            let priceSelling = items[index].net_selling_price_commercial_unit;
                            let pricePurchase = items[index].net_purchase_price_commercial_unit_after_discounts;
                            let quantity = items[index].quantity;

                            if(priceSelling == null) {
                                priceSelling = 0;
                            }
                            if(pricePurchase == null) {
                                pricePurchase = 0;
                            }
                            if(quantity == null) {
                                quantity = 0;
                            }
                            sumOfSelling += parseFloat(priceSelling) * parseInt(quantity);
                            sumOfPurchase += parseFloat(pricePurchase) * parseInt(quantity);
                        }

                        return ((sumOfSelling - sumOfPurchase) * 1.23).toFixed(2);

                    }
                },
                {
                    data: 'weight',
                    name: 'weight',
                },
                {
                    data: 'orderId',
                    name: 'products_value_gross',
                    render: function(date, type, row) {
                        let totalOfProductsPrices = 0;
                        var items = row['items'];

                        for (let index = 0; index < items.length; index++) {
                            let price = items[index].net_selling_price_commercial_unit;
                            let quantity = items[index].quantity;
                            if(price == null) {
                                price = 0;
                            }
                            if(quantity == null) {
                                quantity = 0;
                            }
                            totalOfProductsPrices += parseFloat(price) * parseInt(quantity);
                        }

                        return (totalOfProductsPrices * 1.23).toFixed(2);
                    }
                },
                {
                    data: 'additional_service_cost',
                    name: 'additional_service_cost',
                },
                {
                    data: 'additional_cash_on_delivery_cost',
                    name: 'additional_cash_on_delivery_cost',
                },
                {
                    data: 'shipment_price_for_client',
                    name: 'shipment_price_for_client',
                },
                {
                    data: 'shipment_price_for_us',
                    name: 'shipment_price_for_us',
                },
                {
                    data: 'orderId',
                    name: 'sum_of_gross_values',
                    searchable: false,
                    render: function(date, type, row) {
                        let totalOfProductsPrices = 0;
                        let additionalServiceCost = row['additional_service_cost'];
                        let additionalPackageCost = row['additional_cash_on_delivery_cost'];
                        let shipmentPriceForClient = row['shipment_price_for_client'];
                        if(additionalServiceCost == null) {
                            additionalServiceCost = 0;
                        }
                        if(shipmentPriceForClient == null) {
                            shipmentPriceForClient = 0;
                        }
                        if(additionalPackageCost == null) {
                            additionalPackageCost = 0;
                        }
                        var items = row['items'];

                        for (let index = 0; index < items.length; index++) {
                            let price = items[index].net_selling_price_commercial_unit;
                            let quantity = items[index].quantity;
                            if(price == null) {
                                price = 0;
                            }
                            if(quantity == null) {
                                quantity = 0;
                            }
                            totalOfProductsPrices += parseFloat(price) * parseInt(quantity);
                        }
                        return ((totalOfProductsPrices * 1.23) + parseFloat(shipmentPriceForClient) + parseFloat(additionalServiceCost) + parseFloat(additionalPackageCost)).toFixed(2);
                    }
                },
                {
                    data: 'orderId',
                    name: 'sum_of_payments',
                    searchable: false,
                    render: function(data, type, row) {
                        let totalOfPayments = 0;
                        let totalOfDeclaredPayments = 0;
                        var payments = row['payments'];

                        for (let index = 0; index < payments.length; index++) {
                            if(payments[index].promise != "1") {
                                totalOfPayments += parseFloat(payments[index].amount);
                            } else {
                                totalOfDeclaredPayments += parseFloat(payments[index].amount);
                            }
                        }

                        if(totalOfDeclaredPayments > 0 ) {
                            return '<p>Z: ' + totalOfPayments + '</p><p>D: ' + totalOfDeclaredPayments +'</p>';
                        } else {
                            return '<p>Z: ' + totalOfPayments + '</p>';
                        }

                    }
                },
                {
                    data: 'orderId',
                    name: 'left_to_pay',
                    searchable: false,
                    render: function(date, type, row) {
                        let totalOfProductsPrices = 0;
                        let additionalServiceCost = row['additional_service_cost'];
                        let additionalPackageCost = row['additional_cash_on_delivery_cost'];
                        let shipmentPriceForClient = row['shipment_price_for_client'];
                        if(additionalServiceCost == null) {
                            additionalServiceCost = 0;
                        }
                        if(shipmentPriceForClient == null) {
                            shipmentPriceForClient = 0;
                        }
                        if(additionalPackageCost == null) {
                            additionalPackageCost = 0;
                        }
                        var items = row['items'];

                        for (let index = 0; index < items.length; index++) {
                            let price = items[index].net_selling_price_commercial_unit;
                            let quantity = items[index].quantity;
                            if(price == null) {
                                price = 0;
                            }
                            if(quantity == null) {
                                quantity = 0;
                            }
                            totalOfProductsPrices += parseFloat(price) * parseInt(quantity);
                        }
                        let orderSum = ((totalOfProductsPrices * 1.23) + parseFloat(shipmentPriceForClient) + parseFloat(additionalServiceCost) + parseFloat(additionalPackageCost)).toFixed(2);
                        let totalOfPayments = 0;
                        var payments = row['payments'];

                        for (let index = 0; index < payments.length; index++) {
                            if(payments[index].promise != "1") {
                                totalOfPayments += parseFloat(payments[index].amount);
                            }
                        }

                        return (orderSum - totalOfPayments).toFixed(2);

                    }
                },
                {
                    data: 'transport_exchange_offers',
                    name: 'transport_exchange_offers',
                    searchable: false,
                    orderable: false,
                    render: function(data, option, row)
                    {
                        let html = "";
                        if(!data.length) {
                            return html;
                        }

                        let generateTitleTooltip = function (offer) {
                            return `Nr: ${offer.firm_name} | NIP: ${offer.nip} | Osoba kontaktowa: ${offer.contact_person} | Telefon: ${offer.phone_number} | Email: ${offer.email} | Adres: ${offer.street} ${offer.number}, ${offer.postal_code} ${offer.city} | Uwagi: ${offer.comments} ||| Kierowca: ${offer.driver_first_name} ${offer.driver_last_name} | ${offer.driver_phone_number} | Nr dokumentu: ${offer.driver_document_number} | Nr rej.: ${offer.driver_car_registration_number} | Przybycie: ${offer.driver_arrical_date} ${offer.driver_approx_arrival_time}`;
                        };

                        data.forEach(function (spedition) {
                            let chosenSpeditionClass = "";
                            if (spedition.chosen_spedition) {
                                chosenSpeditionClass = "transport-exchange__spedition-chosen"
                            }
                            html += `<div class='transport-exchange ${chosenSpeditionClass}'>`;

                            html += `<div>Nr: ${spedition.id}</div>`;

                            if (spedition.chosen_spedition) {
                                let title = generateTitleTooltip(spedition.chosen_spedition);
                                html += `<div class="transport-exchange-offer" data-toggle="transport-exchange-tooltip" data-html="true" title="${title}">${spedition.chosen_spedition.firm_name.substr(0, 10)}</div>`;
                            } else if (spedition.spedition_offers.length) {
                                spedition.spedition_offers.forEach(function (offer) {
                                    let title = generateTitleTooltip(offer);
                                    html += `<div class="transport-exchange-offer" onclick="chooseExchangeOffer(${offer.id})" data-toggle="transport-exchange-tooltip" data-html="true" title="${title}">${offer.firm_name.substr(0, 10)}</div>`;
                                });
                            }
                            html += "</div>";
                        });


                        return html;
                    }
                },
                {
                    data: 'production_date',
                    name: 'production_date',
                    searchable: false,
                },
                {
                    data: 'shipment_date',
                    name: 'shipment_date',
                    searchable: false,
                    render: function(shipment_date, option, row)
                    {
                        let date = moment(shipment_date);
                        if (date.isValid()) {
                            let formatedDate = date.format('YYYY-MM-DD');
                            let startDaysVariation = "";
                            if (row.shipment_start_days_variation) {
                                startDaysVariation = "<br>&plusmn; " + row.shipment_start_days_variation + " dni";
                            }
                            return formatedDate + startDaysVariation;
                        }
                        return "";
                    }
                },
                    @foreach($customColumnLabels as $labelGroupName => $label)
                {
                    data: 'labels',
                    name: 'label_{{str_replace(" ", "_", $labelGroupName)}}',
                    searchable: false,
                    orderable: false,
                    render: function(labels, option, row)
                    {
                        let html = '';
                        let currentLabelGroup = "{{ $labelGroupName }}";
                        if (row.closest_label_schedule_type_c && currentLabelGroup == "info dodatkowe") {
                            html += row.closest_label_schedule_type_c.trigger_time;
                        }
                        labels.forEach(function (label) {
                            if(label.length > 0){
                                if (label[0].label_group_id != null) {
                                    if(label[0].label_group[0].name == currentLabelGroup) {
                                        let tooltipContent = label[0].name
                                        if (
                                            label[0].id == 55 ||
                                            label[0].id == 56 ||
                                            label[0].id == 57 ||
                                            label[0].id == 58
                                        ) {
                                            tooltipContent = row.generalMessage;
                                        } else if (
                                            label[0].id == 78 ||
                                            label[0].id == 79 ||
                                            label[0].id == 80 ||
                                            label[0].id == 81
                                        ) {
                                            tooltipContent = row.shippingMessage;
                                        } else if (
                                            label[0].id == 82 ||
                                            label[0].id == 83 ||
                                            label[0].id == 84 ||
                                            label[0].id == 85
                                        ) {
                                            tooltipContent = row.warehouseMessage;
                                        } else if (
                                            label[0].id == 59 ||
                                            label[0].id == 60 ||
                                            label[0].id == 61 ||
                                            label[0].id == 62
                                        ) {
                                            tooltipContent = row.complaintMessage;
                                        }
                                        let comparasion = false
                                        if (row.payment_deadline) {
                                            let d1 = new Date();
                                            let d2 = new Date(row.payment_deadline);
                                            d1.setHours(0,0,0,0)
                                            d2.setHours(0,0,0,0)
                                            comparasion = d1 >= d2
                                        }
                                        if (label[0].id == '{{ env('MIX_LABEL_WAITING_FOR_PAYMENT_ID') }}' && comparasion) {
                                            html += '<div data-toggle="label-tooltip" style="border: solid red 4px" data-html="true" title="' + tooltipContent + '" class="pointer" onclick="removeLabel('+row.orderId+', '+label[0].id+', '+label[0].manual_label_selection_to_add_after_removal+', \''+label[0].added_type+'\');"><span class="order-label" style="color: '+ label[0].font_color +'; display: block; margin-top: 5px; background-color: ' + label[0].color + '"><i class="' + label[0].icon_name + '"></i></span></div>';
                                        } else {
                                            html += '<div data-toggle="label-tooltip" data-html="true" title="' + tooltipContent + '" class="pointer" onclick="removeLabel('+row.orderId+', '+label[0].id+', '+label[0].manual_label_selection_to_add_after_removal+', \''+label[0].added_type+'\');"><span class="order-label" style="color: '+ label[0].font_color +'; display: block; margin-top: 5px; background-color: ' + label[0].color + '"><i class="' + label[0].icon_name + '"></i></span></div>';
                                        }
                                    }
                                }
                            }
                        });

                        return html;
                    }
                },
                    @endforeach
                {
                    data: null,
                    name: 'invoices',
                    render: function(data) {
                        let invoices = data.invoices
                        let html = ''
                        if(invoices !== undefined){
                            invoices.forEach(function(invoice){
                                html += '<a target="_blank" href="/storage/invoices/'+invoice.invoice_name+'" style="margin-top: 5px;">Faktura</a>';
                            });
                            html += '<br />'
                        }
                        html += '<a href="{{env('FRONT_NUXT_URL')}}' + '/magazyn/awizacja/0/0/' + data.orderId + '/wyslij-fakture">Dodaj</a>'
                        return html;
                    }
                },
                {
                    data: null,
                    name: 'invoice_gross_sum',
                    render: function(data, type, row) {
                        let sumOfPurchase = 0;
                        let items = row['items'];

                        for (let index = 0; index < items.length; index++) {
                            let pricePurchase = items[index].net_purchase_price_commercial_unit_after_discounts;
                            let quantity = items[index].quantity;
                            if(pricePurchase == null) {
                                pricePurchase = 0;
                            }
                            if(quantity == null) {
                                quantity = 0;
                            }
                            sumOfPurchase += parseFloat(pricePurchase) * parseInt(quantity);
                        }
                        let totalItemsCost = sumOfPurchase * 1.23;
                        let transportCost = 0

                        let html = 'wartość towaru: <br />' +
                            (totalItemsCost).toFixed(2) + '<br/>';
                        if (data.shipment_price_for_us) {
                           html += 'Koszt tran.: <br/>' +
                            data.shipment_price_for_us + '<br />'
                            transportCost = parseFloat(data.shipment_price_for_us)
                        }
                        html += 'Suma: <br /><b>' + (totalItemsCost + transportCost).toFixed(2) + '<b/>'
                        return html;
                    }
                },
                {
                    data: 'orderId',
                    name: 'icons',
                    render: function() {
                        let html = 'do zrobienia';

                        return html;
                    }
                },
                {
                    data: 'consultant_earning',
                    name: 'consultant_earning',
                },
                {
                    data: 'packages',
                    name: 'real_cost_for_company',
                    render: function(packages) {
                        let html = '';
                        packages.forEach(function(package){
                            html += '<span style="margin-top: 5px;">' + package.real_cost_for_company; + '</span>';
                        });
                        return html;
                    }
                },
                {
                    data: 'shipment_price_for_client',
                    name: 'difference',
                    render: function(data, type, row) {
                        let priceForClient = row['shipment_price_for_client'];
                        let priceForUs = row['shipment_price_for_us'];

                        if(priceForClient == null) {
                            priceForClient = 0;
                        }
                        if(priceForUs == null) {
                            priceForUs = 0;
                        }

                        let price = (parseFloat(priceForClient) - parseFloat(priceForUs)).toFixed(2);
                        let html = '<span style="margin-top: 5px;">' + price  + '</span>';

                        return html;
                    }
                },
                {
                    data: 'correction_amount',
                    name: 'correction_amount',
                },
                {
                    data: 'correction_description',
                    name: 'correction_description',
                },
                {
                    data: 'document_number',
                    name: 'document_number'
                },
                {
                    data: 'id',
                    name: 'search_on_lp',
                    searchable: false,
                    orderable: false,
                    visible: false
                },
            ],
        });
            @foreach($visibilities as $key =>$row)
        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return table.column(x+':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
            // if (typeof table.column(x+':name').index() === "number")
            return table.column(x+':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });
        table.button().add({{1+$key}},{
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
            @endforeach
        let localDatatables = localStorage.getItem('DataTables_dataTable_/admin/orders');
        if(localDatatables != null) {
            let objDatatables = JSON.parse(localDatatables);

            $('#dataTable thead tr th input, #dataTable thead tr th select').each(function (i) {
                let colName = $(this)[0].id;
                if(colName != "") {
                    let colSelector = colName.substring(13, colName.length) + ":name";
                    let index = table.column(colSelector).index();
                    let searched = objDatatables.columns[index].search.search;

                    if (searched != "") {
                        this.value = searched;
                    }
                }
            });
        }
        $('#dataTable thead tr th').each(function (i) {

            $('.dt-button', this).on('click', function(){
                table
                    .order([7, 'desc' ])
                    .draw();
            });

            $('input', this).on('click', function (e) {
                e.stopPropagation();
            });

            $('input', this).on('keydown', function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    $(this).change();
                }
            });

            $('input', this).on('change', function (e) {
                let colName = $(this)[0].id;
                let colSelector = colName.substring(13, colName.length) + ":name";

                if(table.column(colSelector).search() !== this.value) {
                    if(this.value == '') {
                        table
                            .column(colSelector)
                            .search('')
                            .draw();
                    } else {
                        table
                            .column(colSelector)
                            .search(this.value)
                            .draw();
                    }
                }
            });
        });

        $("#columnSearch-packages_sent")
            .click(function (e) {
                e.stopPropagation();
            })
            .change(function () {
                if(table.column("packages_sent:name").search() !== this.value) {
                    if(this.value == '') {
                        table
                            .column("packages_sent:name")
                            .search('')
                            .draw();
                    } else {
                        table
                            .column("packages_sent:name")
                            .search(this.value)
                            .draw();
                    }
                }
            });
        $("#columnSearch-packages_not_sent")
            .click(function (e) {
                e.stopPropagation();
            })
            .change(function () {
                if(table.column("packages_not_sent:name").search() !== this.value) {
                    if(this.value == '') {
                        table
                            .column("packages_not_sent:name")
                            .search('')
                            .draw();
                    } else {
                        table
                            .column("packages_not_sent:name")
                            .search(this.value)
                            .draw();
                    }
                }
            });
        $("#columnSearch-shipment_date")
            .click(function (e) {
                e.stopPropagation();
            })
            .change(function () {
                if(table.column("shipment_date:name").search() !== this.value) {
                    if(this.value == '') {
                        table
                            .column("shipment_date:name")
                            .search('')
                            .draw();
                    } else {
                        table
                            .column("shipment_date:name")
                            .search(this.value)
                            .draw();
                    }
                }
            });
        $('#orderFilter').change(function () {
            if(this.value == 'ALL') {
                table
                    .search( '' )
                    .columns('statusName:name').search( '' )
                    .draw();
                localStorage.setItem("filter", "ALL");
            } else if(this.value == 'WITHOUT_REALIZATION') {
                table
                    .columns('statusName:name')
                    .search( 'bez realizacji', true, false )
                    .draw();
                localStorage.setItem("filter", "WITHOUT_REALIZATION");
            } else if(this.value == 'COMPLETED') {
                table
                    .columns('statusName:name')
                    .search( 'oferta zakonczona', true, false )
                    .draw();
                localStorage.setItem("filter", "COMPLETED");
            } else {
                table
                    .columns('statusName:name')
                    .search( 'przyjete zapytanie ofertowe|w trakcie analizowania przez konsultanta|mozliwa do realizacji|mozliwa do realizacji kominy|w trakcie realizacji|oferta oczekujaca|przekazane konsultantowi do obslugi', true, false )
                    .draw();
                localStorage.setItem("filter", "PENDING");
            }

        });

        $('#dataTable').on( 'column-visibility.dt', function ( e, settings, column, state ) {
            if(state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }
        });
        $('#columnSearch-clientPhone').click(function(){
            var str = $('#columnSearch-clientPhone').val();
            var replaced = str.replace(/-|\s/g,'');
            clearFilters(false);
            $('#columnSearch-clientPhone').val(replaced);
        });
        $('#columnSearch-orderId').click(function(){
            clearFilters(false);
        });
        function filterByPhone(number) {
            $("#columnSearch-clientPhone").val(number).change();
        }

        function clearAndfilterByPhone(number) {
            clearFilters(false);
            filterByPhone(number);
        }

        function filterByWarehouseMegaOlawa() {
            $("#columnSearch-symbol").val('MEGA-OLAWA').change();
        }

        function clearFilters(reload = true) {
            $("#columnSearch-shipment_date").val("all");
            $("#columnSearch-packages_not_sent").val("");
            $("#columnSearch-packages_sent").val("");
            $("#dataTable thead tr input").val("");
            $(".filter-by-labels-in-group__clear button").click();
            $('#searchLP').val('');
            $('#searchOrderValue').val('');
            $('#searchPayment').val('');
            $('#searchLeft').val('');


            table
                .columns()
                .search('');

            table.order([7, "desc"]).draw();

            if (reload) {
                table.draw();
            }
             $('#orderFilter').trigger("change");
        }

        function removeLabel(orderId, labelId, manualLabelSelectionToAdd, addedType) {
            let removeLabelRequest = function () {
                $.ajax({
                    url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
                    method: "POST"
                }).done(function (res) {
                    if(res.error == 'position') {
                        alert('Nie znaleziono pozycji dla produktu: ' + res.product + ' Symbol: ' + res.productName);
                    } else if(res.error == 'exists') {
                        alert('Wywołano już stan magazynowy');
                    }
                    table.ajax.reload(null, false);
                });
            };

            function removeMultiLabel(orderId, labelId, ids) {
                $.ajax({
                    url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
                    method: "POST",
                    data: {
                        labelsToAddIds: ids,
                        manuallyChosen: true
                    }
                }).done(function () {
                    if ($.inArray('47', ids) != -1) {
                        $('#magazine').modal();
                        $('input[name="order_id"]').val(orderId);
                        $('#selectWarehouse').val(16);
                        $('#warehouseSelect').attr('selected', true);
                        $('#selectWarehouse').click();
                    }
                    refreshDtOrReload();
                })
                    .fail((error) => {
                        if (error.responseText === 'warehouse not found') {
                            $('#set-magazine').modal()
                            let form = $('#addWarehouse')
                            form.submit((event) => {
                                event.preventDefault()
                                $.ajax({
                                    method: 'post',
                                    url: '/admin/orders/set-warehouse-and-remove-label',
                                    dataType: 'json',
                                    data: {
                                        order_id: orderId,
                                        warehouse_id: event.target.warehouse_id.value,
                                        label: labelId,
                                        labelsToAddIds: ids
                                    },
                                })
                                $('#set-magazine').modal('hide');
                                refreshDtOrReload()
                            })
                        }
                    });
            }

            let refreshDtOrReload = function () {
                $.ajax({
                    url: '/api/get-labels-scheduler-await/{{ Auth::id() }}'
                }).done(function(res) {
                    if (res.length) {
                        location.reload();
                    } else {
                        table.ajax.reload(null, false);
                    }
                });
            };

            if (manualLabelSelectionToAdd) {
                $.ajax({
                    url: "/admin/labels/"+labelId+"/associated-labels-to-add-after-removal"
                }).done(function(data) {
                    let modal = $('#manual_label_selection_to_add_modal');
                    let input = modal.find("#labels_to_add_after_removal_modal");
                    input.empty();
                    data.forEach(function(item){
                        input.append($('<option>', {
                            value: item.id,
                            text : item.name,
                            selected: true
                        }));
                    });
                    $('#manual_label_selection_to_add_modal').modal('show');

                    modal.find("#labels_to_add_after_removal_modal_ok").off().on('click', function () {
                        let ids = input.val();
                        removeMultiLabel(orderId, labelId, ids);
                    });
                });
            } else {
                let payDateLabelId = '{{ env('MIX_LABEL_ENTER_PAYMENT_DATE_ID') }}'
                if (labelId == payDateLabelId) {
                    let modalSetTime = $('#set_time');
                    modalSetTime.modal('show');
                    $('#set_time').on('shown.bs.modal', function() {
                        $('#invoice-month').focus()
                    })
                    modalSetTime.find("#remove-label-and-set-date").off().on('click', () => {
                        if ($('#invoice-month').val() > 12 || $('#invoice-days').val() > 31) {
                            $('#invoice-date-error').removeAttr('hidden')
                            return
                        }
                        $.ajax({
                            type: "POST",
                            url: '/admin/orders/payment-deadline',
                            data: {
                                order_id: orderId,
                                date: {
                                    year: $('#invoice-years').val(),
                                    month: $('#invoice-month').val(),
                                    day: $('#invoice-days').val(),
                                }
                            },
                        }).done(function (data) {
                                removeLabelRequest();
                                refreshDtOrReload()
                                modalSetTime.modal('hide')
                                $('#invoice-month').val('')
                                $('#invoice-days').val('')
                        }).fail(function (data) {
                            $('#invoice-date-error').removeAttr('hidden')
                            $('#invoice-date-error').text(data.responseText ? data.responseText : 'Nieznany błąd2')

                        });
                    });
                    return;
                } else if (addedType == "{{ \App\Entities\Label::CHAT_TYPE }}") {
                    var url = '{{ route("chat.index", ["all" => 1, "id" => ":id"]) }}';
                    url = url.replace(':id', orderId);
                    window.location.href = url
                    return
                } else if (addedType != "C") {
                    let confirmed = confirm("Na pewno usunąć etykietę?");
                    if (!confirmed) {
                        return;
                    }
                    removeLabelRequest();
                    refreshDtOrReload();
                    return;
                }

                let modalTypeC = $('#added_label_is_type_c_modal');
                modalTypeC.modal('show');
                modalTypeC.find("#confirmed_to_remove_chosen_label").off().on('click', removeLabelRequest);

                modalTypeC.find("#new_date_for_timed_label_type_c_ok").off().on('click', function () {
                    let val = modalTypeC.find("#new_date_for_timed_label_type_c").val();
                    let date = moment(val);
                    if (!date.isValid()) {
                        alert("Nieprawidłowa data");
                        return;
                    }

                    $.ajax({
                        url: "/api/scheduled-time-reset-type-c",
                        method: "POST",
                        data: {
                            order_id: orderId,
                            label_id_to_handle: labelId,
                            trigger_time: val
                        }
                    }).done(function () {
                        removeLabelRequest();
                        refreshDtOrReload();
                    });
                });
            }


        }

        function addLabel() {
            let chosenLabel = $("#choosen-label");
            if(chosenLabel.val() == "") {
                alert("Nie wybrano etykiety");
                return;
            }

            let selectedOrders = $(".order-id-checkbox:checked");
            if(selectedOrders.length == 0) {
                alert("Nie wybrano żadnego zamówienia");
                return;
            }

            let orderIds = [];
            $.each(selectedOrders, function(index, order){
                orderIds.push($(order).val());
            });

            $.ajax({
                url: "/admin/orders/label-addition/"+chosenLabel.val(),
                method: "POST",
                data: { orderIds: orderIds }
            }).done(function() {
                $.ajax({
                    url: '/api/get-labels-scheduler-await/{{ Auth::id() }}'
                }).done(function(res) {
                    if (res.length) {
                        location.reload();
                    } else {
                        table.ajax.reload(null, false);
                    }
                });
            });
        }

        function moveData(id){
            if($('#moveButton-'+id).hasClass('btn-warning')) {
                $('#moveButton-' + id).removeClass('btn-warning').addClass('btn-dark');
                $('.btn-warning').attr('disabled', true);
                $('.btn-move').removeClass('hidden');
            } else if ($('#moveButton-'+id).hasClass('btn-dark')) {
                $('#moveButton-' + id).removeClass('btn-dark').addClass('btn-warning');
                $('.btn-warning').attr('disabled', false);
                $('.btn-move').addClass('hidden');
            }
        }

        function moveDataAjax(id){
            var idToSend = id;
            var buttonId = $('.btn-dark').attr('id');
            var idToGet;
            var res = buttonId.split("-")
            idToGet = res[1];
            if(idToGet != idToSend) {
                $('#order_id_get').text(idToGet);
                $('#order_id_send').text(idToSend);
                $('#order_move_data').modal('show');
            } else {
                $('#order_move_data_error_select').modal('show');
            }
        }

        $('#move-data-ok').on('click', function(){
            var idToGet = $('#order_id_get').text();
            var idToSend = $('#order_id_send').text();
            $.ajax({
                url: '/admin/orders/'+idToGet+'/data/'+idToSend+'/move',
            }).done(function(data) {
                $('#order_move_data_success').modal('show');

                $('#order_move_data_ok').on('click', function(){
                    window.location.href='/admin/orders';
                });
            }).fail(function() {
                $('#order_move_data_error').modal('show');
                $('#order_move_data_ok_error').on('click', function() {
                    window.location.href = '/admin/orders';

                });
            });
        });



        function gerateSpeditionExchangeLink() {
            let orders = $("#spedition-exchange-selected-items").val();

            $.ajax({
                url: "/api/spedition-exchange/generate-link",
                method: "POST",
                data: {data: orders}
            }).done(function(data) {
                $(".spedition-exchange-generated-link").html(data);
            });
        }

        function updateSpeditionExchangeSelectedItem(id, type) {
            let shouldAdd = true;
            let hidden = $("#spedition-exchange-selected-items");
            let val = JSON.parse(hidden.val());

            val.forEach(function(value, index) {
                if (value.id == id && value.type == type) {
                    shouldAdd = false;
                    val.splice(index, 1);
                }
            });

            if (shouldAdd) {
                val.push({
                    id: id,
                    type: type
                });
            }

            $(hidden).val(JSON.stringify(val));
        }

        function chooseExchangeOffer(offerId) {
            $.ajax({
                url: "/api/spedition-exchange/accept-offer/" + offerId,
                method: "GET"
            }).done(function(data) {
                table.ajax.reload(null, false);
            });
        }
    </script>
    <script>
        $(document).ready(function(){
            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = window.location.search.substring(1),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');

                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                    }
                }
            };
            var idFromUrl = getUrlParameter('order_id');
            var planning = getUrlParameter('planning');
            if(idFromUrl !== undefined){
                console.log(planning);
                if(planning !== undefined) {
                    console.log('aaa');
                    setTimeout(function () {
                        $('#columnSearch-orderId').val(idFromUrl);
                        $('#columnSearch-orderId').change();
                    }, 1500);
                } else {
                    setTimeout(function () {
                        $(document).scrollTo('#id-' + idFromUrl);
                        $('#id-' + idFromUrl).css({'background-color': '#DCDCDC'});
                    }, 1500);
                }
            }
        });
        $('#searchLP').change(function(){
            table
                .columns('search_on_lp:name')
                .search( $('#searchLP').val())
                .draw();
        });
        $('#searchOrderValue').change(function(){
            table
                .columns('sum_of_gross_values:name')
                .search( $('#searchOrderValue').val())
                .draw();
        });
        $('#searchPayment').change(function(){
            table
                .columns('sum_of_payments:name')
                .search( $('#searchPayment').val())
                .draw();
        });
        $('#searchLeft').change(function(){
            table
                .columns('left_to_pay:name')
                .search( $('#searchLeft').val())
                .draw();
        });
    </script>
    <script>
        var resource = null;
        $('#selectWarehouse').on('click', function () {
            var id = $(this).val();
            $.ajax({
                url:'/admin/warehouse/' + id
            }).done(function(data){
                $('#selectWarehouse').hide();
                $('#titleModal').hide();
                $('#modalDialog').css({width: "auto !important"});
                document.getElementById('modalDialog').style.width = 'auto';
                let calendarEl = document.getElementById('calendar');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['interaction', 'dayGrid', 'timeGrid', 'resourceTimeline'],
                    now: '{{new Carbon\Carbon()}}',
                    editable: true,
                    aspectRatio: 1.8,
                    scrollTime: '7:00',
                    slotDuration: '0:05',
                    timeZone: 'UTC',
                    minTime: "07:00:00",
                    maxTime: "20:00:00",
                    locale: 'PL',
                    titleFormat: { year: 'numeric', month: 'long', day: '2-digit' },
                    buttonText: {
                        today: 'Dzisiaj',
                        month: 'Miesiąc',
                        week: 'Tydzień',
                        day: 'Dzień',
                        days: 'Lista',
                        resourceTimelineThreeDays: '3 Dni'
                    },
                    slotLabelFormat: [
                        { day: '2-digit', month: 'long', year: 'numeric' },
                        { weekday: 'long' },
                        { hour: '2-digit', minute: '2-digit' }
                    ],
                    header: {
                        left: 'promptResource today prev,next',
                        center: 'title',
                        right: 'resourceTimelineDay,resourceTimelineThreeDays,timeGridWeek,dayGridMonth'
                    },
                    slotWidth: 15,
                    resourceAreaWidth: "10%",
                    customButtons: {
                        promptResource: {
                            text: 'Dodaj godziny pracy',
                            click: function () {
                                let warehouse = null;
                                if ($('#warehouseSelect').is(':selected')) {
                                    warehouse = $('#warehouseSelect').val();
                                }
                                $.ajax({
                                    url: '/admin/planning/timetable/' + warehouse + '/getStorekeepersToModal',
                                }).done(function (data) {
                                    $('#addStorekeeperTime').modal();
                                    let i = 1;
                                    if ($('#storekeepers > tbody > tr').length === 1) {
                                        $.each(data, function (index, value) {
                                            let html = '<tr class="appendRow">';
                                            html += '<td>' + i + '</td>';
                                            html += '<td>' + value.name + '</td>';
                                            html += '<td>' + value.firstname + '</td>';
                                            html += '<td>' + value.lastname + '</td>';
                                            html += '<td><input class="form-control start" type="time" name="start[' + value.id + ']" id="start[' + value.id + ']" value="' + value.user_works[0].start + '"></td>';
                                            html += '<td><input class="form-control end" type="time" name="end[' + value.id + ']" id="end[' + value.id + ']" value="' + value.user_works[0].end + '"></td>';
                                            html += '</tr>';
                                            i++;
                                            $('#storekeepers > tbody').append(html);

                                        });
                                    }
                                }).fail(function () {
                                    $('#addStorekeeperTimeError').modal();
                                });
                            }
                        }
                    },
                    dateClick: function (info) {
                        if (info.view.type !== 'timeGridWeek' && info.view.type !== 'dayGridMonth') {
                            $('#addNewTask').modal();
                            let startDate = new Date(info.dateStr);
                            let firstDate = new Date(startDate.setHours(startDate.getHours() - 1));
                            let startMinutes = firstDate.getMinutes();
                            if (startMinutes < 10) {
                                startMinutes = '0' + startMinutes;
                            }
                            let dateTime = firstDate.getFullYear() + '-' + ( '0' + (firstDate.getMonth() + 1) ).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                            let newDate = new Date(info.dateStr);
                            let endDate = new Date(newDate.setHours(newDate.getHours() + 1 - 2));
                            let minutes = endDate.getMinutes();
                            if (minutes < 10) {
                                minutes = '0' + minutes;
                            }
                            let dateTimeEnd = endDate.getFullYear() + '-' + ( '0' + (endDate.getMonth() + 1) ).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                            let warehouse = null;
                            if ($('#warehouseSelect').is(':selected')) {
                                warehouse = $('#warehouseSelect').val();
                            }
                            let taskGroup = $('#task-group');

                            let html = '';
                            html += '<div id="task-group">'
                            html += '<div class="form-group">';
                            html += '<label for="title">Nazwa</label>';
                            html += '<input type="text" name="title" id="title" value="' + info.resource.title + '" class="form-control" disabled>';
                            html += '<input type="hidden" name="user_id" id="user_id" value="' + info.resource.id + '">';
                            html += '<input type="hidden" name="warehouse_id" id="user_id" value="' + warehouse + '">';
                            html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                            html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="name">Nazwa zadania</label>';
                            html += '<input type="text" name="name" id="name_new" class="form-control" value="" required>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="start_new">Godzina rozpoczęcia</label>';
                            html += '<input type="text" name="start" id="start_new" class="form-control default-date-time-picker-now" value="' + dateTime + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="end">Godzina zakończenia</label>';
                            html += '<input type="text" name="end" id="end" class="form-control default-date-time-picker-now" value="' + dateTimeEnd + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-green">';
                            html += '<input type="radio" name="color" id="color-green" value="#32CD32" required> ';
                            html += 'Zielony(wyprodukowane)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-blue">';
                            html += '<input type="radio" name="color" id="color-blue" value="#194775" checked="checked" required> ';
                            html += 'Niebieski(dopuszczalne przesunięcie terminu dostawy)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-yellow">';
                            html += '<input type="radio" name="color" id="color-yellow" value="#E6C74D" required> ';
                            html += 'Żółty(PILNE - prośba o wysłanie we wskazanym terminie)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-red">';
                            html += '<input type="radio" name="color" id="color-red" value="#FF0000" required> ';
                            html += 'Czerwony(awaria koniecznie to wysłać dzisiaj)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-violet">';
                            html += '<input type="radio" name="color" id="color-violet" value="#9966CC" required> ';
                            html += 'Fioletowy(zamówienie MEGA-OLAWA)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="consultant_value">Koszt obsługi konsultanta</label>';
                            html += '<input type="number" name="consultant_value" id="consultant_value" class="form-control">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="consultant_notice">Opis obsługi konsultanta</label>';
                            html += '<textarea rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice" class="form-control"></textarea>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="warehouse_value">Koszt obsługi magazynu</label>';
                            html += '<input type="number" name="warehouse_value" id="warehouse_value" class="form-control">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="warehouse_notice">Opis obsługi magazynu</label>';
                            html += '<textarea rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice" class="form-control"></textarea>';
                            html += '</div>';
                            html += '</div>';
                            if (taskGroup.length !== 0) {
                                taskGroup.remove();
                            }
                            $('#addTask').append(html);
                            $('.default-date-time-picker-now').datetimepicker({
                                sideBySide: true,
                                format: "YYYY-MM-DD H:mm",
                                stepping: 5,
                            });
                            $('#name_new').val($('input[name="order_id"]').val()+' - '+ firstDate.getUTCDate()+  '-'+ ( '0' + (firstDate.getMonth() + 1) ).slice(-2) + ' - '+$('#warehouse_value').val());
                            $('#name_new').change(function(){
                                var dateObj = new Date($('#start_new').val());
                                var month = ( '0' + (dateObj.getMonth() + 1) ).slice(-2);
                                var day = dateObj.getUTCDate();
                                $('#name_new').val($('input[name="order_id"]').val()+' - '+day+'-'+month+' - '+$('#warehouse_value').val());
                            });
                            $(document).on('focusout','.default-date-time-picker-now',function(){
                                var dateObj = new Date($('#start_new').val());
                                var month = ( '0' + (dateObj.getMonth() + 1) ).slice(-2);
                                var day = dateObj.getUTCDate();
                                $('#name_new').val($('input[name="order_id"]').val()+' - '+day+'-'+month+' - '+$('#warehouse_value').val());
                            });
                            $('#warehouse_value').change(function(){
                                var dateObj = new Date($('#start_new').val());
                                var month = ( '0' + (dateObj.getMonth() + 1) ).slice(-2);
                                var day = dateObj.getUTCDate();
                                $('#name_new').val($('input[name="order_id"]').val()+' - '+day+'-'+month+' - '+$('#warehouse_value').val());
                            });
                        }
                    },
                    defaultView: 'resourceTimelineDay',
                    views: {
                        resourceTimelineThreeDays: {
                            type: 'resourceTimeline',
                            duration: {days: 3},
                            buttonText: '3 days'
                        }
                    },
                    resourceLabelText: 'Magazynierzy',
                    resourceRender: function (arg) {
                        let resource = arg.resource.id;

                        arg.el.addEventListener('click', function () {
                            $.ajax({
                                type: "GET",
                                url: '/admin/planning/tasks/16/getTasksForUser/' + resource,
                            }).done(function (data) {
                                $('#acceptTask').modal();
                                $.each(data, function (index, value) {
                                    if (value.status === 'WAITING_FOR_ACCEPT') {
                                        let startDate = new Date(value.start);
                                        let firstDate = new Date(startDate.setHours(startDate.getHours() - 1));
                                        let startMinutes = firstDate.getMinutes();
                                        if (startMinutes < 10) {
                                            startMinutes = '0' + startMinutes;
                                        }
                                        let dateTime = firstDate.getFullYear() + '-' + ( '0' + (firstDate.getMonth() + 1) ).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                                        let newDate = new Date(value.end);
                                        let endDate = new Date(newDate.setHours(newDate.getHours() - 1));
                                        let minutes = endDate.getMinutes();
                                        if (minutes < 10) {
                                            minutes = '0' + minutes;
                                        }
                                        let dateTimeEnd = endDate.getFullYear() + '-' + ( '0' + (endDate.getMonth() + 1) ).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                                        let html = '<tr class="appendRow">';
                                        html += '<td>' + value.id + '</td>';
                                        html += '<td>' + value.title + '</td>';
                                        html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                                        html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                                        html += '<td><input class="form-control date_start" type="text" name="date_start[' + value.id + ']" id="date_start[' + value.id + ']" value="' + dateTime + '"></td>';
                                        html += '<td><input class="form-control date_end" type="text" name="date_end[' + value.id + ']" id="date_end[' + value.id + ']" value="' + dateTimeEnd + '"></td>';
                                        html += '<td><input class="form-control status" type="text" name="status[' + value.id + ']" id="status[' + value.id + ']" value="Oczekuje na akceptację" disabled></td>';
                                        html += '<td><button type="button" class="btn btn-success" onclick="acceptTask(' + value.id + ')">Akceptuj</button></td>'
                                        html += '<td><button type="button" class="btn btn-danger" onclick="rejectTask(' + value.id + ')">Odrzuć</button></td>'
                                        html += '</tr>';
                                        $('#tasksToAccept > tbody').append(html);
                                    }
                                });

                            }).fail(function () {
                                return false;
                            });
                        });
                    },
                    resources: {
                        url: '/admin/planning/timetable/16/getStorekeepers',
                        method: 'GET'
                    },
                    events: {
                        url: '/admin/planning/tasks/16/getTasks',
                        method: 'GET'
                    },
                    eventRender: function (event) {

                        let id;
                        if (event.event.extendedProps.customOrderId !== null) {
                            id = event.event.extendedProps.customOrderId;
                        } else {
                            id = event.event.extendedProps.customTaskId;
                        }
                        $(event.el).attr('id', id);

                    },
                    eventMouseEnter: function (info) {
                        let startDate = new Date(info.event.start);
                        let firstDate = new Date(startDate.setHours(startDate.getHours() - 1));
                        let startMinutes = firstDate.getMinutes();
                        if (startMinutes < 10) {
                            startMinutes = '0' + startMinutes;
                        }
                        let newDate = new Date(info.event.end);
                        let endDate = new Date(newDate.setHours(newDate.getHours() - 1));
                        let minutes = endDate.getMinutes();
                        if (minutes < 10) {
                            minutes = '0' + minutes;
                        }

                        $(info.el).attr('title', info.event.extendedProps.text);
                    },
                    eventDrop: function (info) {
                        let startDate = new Date(info.event.start);
                        let firstDate = new Date(startDate.setHours(startDate.getHours() - 1));
                        let startMinutes = firstDate.getMinutes();
                        if (startMinutes < 10) {
                            startMinutes = '0' + startMinutes;
                        }
                        let dateTime = firstDate.getFullYear() + '-' + ( '0' + (firstDate.getMonth() + 1) ).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                        let newDate = new Date(info.event.end);
                        let endDate = new Date(newDate.setHours(newDate.getHours() - 1));
                        let minutes = endDate.getMinutes();
                        if (minutes < 10) {
                            minutes = '0' + minutes;
                        }
                        let dateTimeEnd = endDate.getFullYear() + '-' + ( '0' + (endDate.getMonth() + 1) ).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                        $('input[name="view_type"]').val(info.view.type);
                        $('input[name="active_start"]').val(info.view.activeStart);
                        $('#moveTaskForm').attr('action', '/admin/planning/tasks/' + info.event.id + '/moveTask');
                        if (info.newResource !== null) {
                            $('input[name="old_resource"]').val(info.oldResource.id);
                            $('input[name="new_resource"]').val(info.newResource.id);
                            $('#moveTask > div > div > div.modal-header > h4').text('Czy jesteś pewny że chcesz zabrać zadanie użytkownikowi: ' + info.oldResource.title + ', i dodać go użytkownikowi: ' + info.newResource.title + '?')
                        } else {
                            $('#moveTask > div > div > div.modal-header > h4').text('Czy chcesz przesunąć to zadanie?')
                        }
                        $('input[name="start"]').val(dateTime);
                        $('input[name="end"]').val(dateTimeEnd);
                        $('#moveTask').modal();
                    },
                    eventResize: function (info) {
                        $('#editTask').modal();
                        let startDate = new Date(info.event.start);
                        let firstDate = new Date(startDate.setHours(startDate.getHours() - 1));
                        let startMinutes = firstDate.getMinutes();
                        if (startMinutes < 10) {
                            startMinutes = '0' + startMinutes;
                        }
                        let dateTime = firstDate.getFullYear() + '-' + ( '0' + (firstDate.getMonth() + 1) ).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                        let newDate = new Date(info.event.end);
                        let endDate = new Date(newDate.setHours(newDate.getHours() - 1));
                        let minutes = endDate.getMinutes();
                        if (minutes < 10) {
                            minutes = '0' + minutes;
                        }
                        let dateTimeEnd = endDate.getFullYear() + '-' + ( '0' + (endDate.getMonth() + 1) ).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                        let warehouse = null;
                        if ($('#warehouseSelect').is(':selected')) {
                            warehouse = $('#warehouseSelect').val();
                        }
                        let taskGroup = $('#task-group-edit');
                        $('#updateTask').attr('action', '/admin/planning/tasks/' + info.event.id + '/updateTaskTime');
                        let html = '';
                        html += '<div id="task-group-edit">'
                        html += '<div class="form-group">';
                        html += '<label for="title">Nazwa zadania</label>';
                        html += '<input type="text" name="title" id="title" value="' + info.event.title + '" class="form-control" disabled>';
                        html += '<input type="hidden" name="task_id" id="task_id" value="' + info.event.id + '" class="form-control">';
                        html += '<input type="hidden" name="warehouse_id" id="user_id" value="' + warehouse + '" class="form-control">';
                        html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                        html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="start">Godzina rozpoczęcia</label>';
                        html += '<input type="text" name="start" id="start" class="form-control default-date-time-picker-now" value="' + dateTime + '">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="end">Godzina zakończenia</label>';
                        html += '<input type="text" name="end" id="end" class="form-control default-date-time-picker-now" value="' + dateTimeEnd + '">';
                        html += '</div>';
                        html += '</div>';
                        if (taskGroup.length !== 0) {
                            taskGroup.remove();
                        }
                        $('#updateTask').append(html);
                        $('.default-date-time-picker-now').datetimepicker({
                            sideBySide: true,
                            format: "YYYY-MM-DD H:mm",
                            stepping: 5
                        });
                    },
                    eventClick: function (info) {
                        let warehouse = null;
                        if ($('#warehouseSelect').is(':selected')) {
                            warehouse = $('#warehouseSelect').val();
                        }
                        $.ajax({
                            url: '/admin/planning/tasks/' + info.event.id + '/getTask',
                            type: "GET"
                        }).done(function (data) {
                            $('#updateTaskModal').modal();
                            let form = $('#updateTaskDetail').length;
                            if (form === 0) {
                                let html = '';
                                html += '<form id="updateTaskDetail" action="/admin/planning/tasks/' + info.event.id + '/updateTask" method="POST">';
                                html += '{{method_field('put')}}';
                                html += '{{csrf_field()}}';
                                html += '<div id="task-group">'
                                html += '<div class="form-group">';
                                html += '<label for="title">Nazwa</label>';
                                html += '<input type="text" name="title" id="title" value="' + data.user.name + '" class="form-control" disabled>';
                                html += '<input type="hidden" name="user_id" id="user_id" value="' + data.user.id + '" class="form-control">';
                                html += '<input type="hidden" name="warehouse_id" id="user_id" value="' + warehouse + '" class="form-control">';
                                html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                                html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="name">Nazwa zadania</label>';
                                html += '<input type="text" name="name" id="name" class="form-control" required value="' + info.event.title + '">';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="start">Godzina rozpoczęcia</label>';
                                html += '<input type="text" name="start" id="start" class="form-control default-date-time-picker-now" value="' + data.task_time.date_start + '">';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="end">Godzina zakończenia</label>';
                                html += '<input type="text" name="end" id="end" class="form-control default-date-time-picker-now" value="' + data.task_time.date_end + '">';
                                html += '</div>';
                                if (data.order !== undefined && data.order !== null) {
                                    html += '</div>';
                                    html += '<div class="form-group">';
                                    html += '<label for="shipment_date">Data rozpoczęcia nadawania przesyłki</label>';
                                    html += '<input type="text" name="shipment_date" id="shipment_date" class="form-control default-date-picker-now" value="' + data.order.shipment_date + '">';
                                    html += '</div>';
                                }
                                html += '<div class="form-group">';
                                html += '<label for="color-dark-green">';
                                html += '<input type="radio" name="color" id="color-dark-green" value="#008000" required> ';
                                html += 'Ciemnozielony(towar wydany)</label>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="color-green">';
                                html += '<input type="radio" name="color" id="color-green" value="#32CD32" required> ';
                                html += 'Zielony(wyprodukowane)</label>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="color-blue">';
                                html += '<input type="radio" name="color" id="color-blue" value="#194775" checked="checked" required> ';
                                html += 'Niebieski(dopuszczalne przesunięcie terminu dostawy)</label>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="color-yellow">';
                                html += '<input type="radio" name="color" id="color-yellow" value="#E6C74D" required> ';
                                html += 'Żółty(PILNE - prośba o wysłanie we wskazanym terminie)</label>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="color-red">';
                                html += '<input type="radio" name="color" id="color-red" value="#FF0000" required> ';
                                html += 'Czerwony(awaria koniecznie to wysłać dzisiaj)</label>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="color-violet">';
                                html += '<input type="radio" name="color" id="color-violet" value="#9966CC" required> ';
                                html += 'Fioletowy(zamówienie MEGA-OLAWA)</label>';
                                html += '</div>';
                                let consultant_value;
                                let consultant_notice;
                                let warehouse_value;
                                let warehouse_notice
                                if (data.task_salary_detail == null) {
                                    consultant_value = '';
                                    consultant_notice = '';
                                    warehouse_value = '';
                                    warehouse_notice = '';
                                } else {
                                    consultant_value = data.task_salary_detail.consultant_value;
                                    consultant_notice = data.task_salary_detail.consultant_notice;
                                    warehouse_value = data.task_salary_detail.warehouse_value;
                                    warehouse_notice = data.task_salary_detail.warehouse_notice;
                                    if(consultant_notice == null) {
                                        consultant_notice = '';
                                    }
                                    if(warehouse_notice == null){
                                        warehouse_notice = '';
                                    }
                                }
                                html += '<div class="form-group">';
                                html += '<label for="consultant_value">Koszt obsługi konsultanta</label>';
                                html += '<input type="number" name="consultant_value" id="consultant_value" class="form-control" value="' + consultant_value + '">';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="consultant_notice">Opis obsługi konsultanta</label>';
                                html += '<textarea rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice" class="form-control">' + consultant_notice + '</textarea>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="warehouse_value">Koszt obsługi magazynu</label>';
                                html += '<input type="number" name="warehouse_value" id="warehouse_value" class="form-control" value="' + warehouse_value + '">';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="warehouse_notice">Opis obsługi magazynu</label>';
                                html += '<textarea rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice" class="form-control">' + warehouse_notice + '</textarea>';
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<a class="btn btn-success" target="_blank" href="/admin/orders/' + data.order_id + '/edit">Przenieś mnie do edycji zlecenia</a>';
                                html += '<br><a class="btn btn-success" target="_blank" href="/admin/orders?order_id=' + data.order_id + '">Przenieś mnie do zlecenia na liście zleceń</a>';
                                html += '</div>';
                                html += '</div>';
                                html += '</form>';
                                $('#updateTaskModal > div > div > div.modal-body').append(html);
                                $('.default-date-time-picker-now').datetimepicker({
                                    sideBySide: true,
                                    format: "YYYY-MM-DD H:mm",
                                    stepping: 5
                                });
                                $('.default-date-picker-now').datetimepicker({
                                    sideBySide: true,
                                    format: "YYYY-MM-DD",
                                });
                                if (info.event.backgroundColor === '#194775') {
                                    $('#color-blue').attr('checked', true);
                                } else if (info.event.backgroundColor === '#E7A954') {
                                    $('#color-orange').attr('checked', true);
                                } else if (info.event.backgroundColor === '#E6C74D') {
                                    $('#color-yellow').attr('checked', true);
                                } else if (info.event.backgroundColor === '#32CD32') {
                                    $('#color-green').attr('checked', true);
                                } else if (info.event.backgroundColor === '#FF0000') {
                                    $('#color-red').attr('checked', true);
                                } else if (info.event.backgroundColor === '#008000') {
                                    $('#color-dark-green').attr('checked', true);
                                } else if (info.event.backgroundColor === '#9966CC') {
                                    $('#color-violet').attr('checked', true);
                                }
                            }
                        }).fail(function () {
                            $('#errorGetToUpdate').modal();
                        });
                    },
                    eventAllow: function (dropLocation, draggedEvent) {
                        return $.ajax({
                            type: "POST",
                            url: '/admin/planning/tasks/allowTaskMove',
                            data: {
                                id: draggedEvent.id,
                                start: dropLocation.startStr,
                                end: dropLocation.endStr,
                                user_id: dropLocation.resource.id
                            },
                        }).done(function (data) {
                            if (data === true) {
                                return true;
                            } else {
                                return false;
                            }
                        }).fail(function () {
                            return false;
                        });

                    }
                });

                calendar.render();
                $('.fc-license-message').remove();

                $('#start').change(function () {
                    $('.start').val($('#start').val());
                });
                $('#end').change(function () {
                    $('.end').val($('#end').val());
                });

            });
            function acceptTask(id) {
                let dateStart = $('input[name="date_start[' + id + ']"]').val();
                let dateEnd = $('input[name="date_end[' + id + ']"]').val();
                let warehouse = null;
                if ($('#warehouseSelect').is(':selected')) {
                    warehouse = $('#warehouseSelect').val();
                }
                $.ajax({
                    type: "POST",
                    url: '/admin/planning/tasks/acceptTask',
                    data: {
                        id: id,
                        start: dateStart,
                        end: dateEnd,
                        status: 'TO_DO'
                    }
                }).done(function (data) {
                    if(data.status == 'TO_DO') {
                        $('input[name="status[' + data.id + ']"]').val('Zaakceptowano');
                    }
                }).fail(function () {
                    $('#errorUpdate').modal();
                });
            }

            function rejectTask(id) {
                let dateStart = $('input[name="date_start[' + id + ']"]').val();
                let dateEnd = $('input[name="date_end[' + id + ']"]').val();
                let warehouse = null;
                if ($('#warehouseSelect').is(':selected')) {
                    warehouse = $('#warehouseSelect').val();
                }
                $.ajax({
                    type: "POST",
                    url: '/admin/planning/tasks/rejectTask',
                    data: {
                        id: id,
                        start: dateStart,
                        end: dateEnd,
                        status: 'REJECTED'
                    }
                }).done(function (data) {
                    if(data.status == 'REJECTED' && data.message == null) {
                        $('input[name="status[' + data.id + ']"]').val('Odrzucono i przesunięto');
                    } else {
                        $('input[name="status[' + data.id + ']"]').val(data.message);
                    }
                }).fail(function () {
                    $('#acceptTask').hide();
                    $('#errorUpdate').modal();
                });
            }

            $('.fc-license-message').remove();
        });

    </script>
@endsection

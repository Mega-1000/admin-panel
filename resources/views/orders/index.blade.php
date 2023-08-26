@php
    use App\Entities\Label;
    use App\Entities\Warehouse;
    use App\Enums\OrderPaymentsEnum;
    use App\Enums\ProductStockError;
    use App\User; @endphp

@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('orders.title')
    </h1>
    <link rel="stylesheet" href="{{ URL::asset('css/views/orders/index.css') }}">
@endsection

@section('table')
    <a href="{{ route('shipping-payin-report') }}" class="btn btn-primary">
        Raport płatności za wysyłki
    </a>

    <div class="modal fade" tabindex="-1" id="changePackageCostModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zmień wartości paczki</h4>
                </div>
                <div class="modal-body">
                    <form id="changePackageCostForm" method="POST"
                          action="{{ route('order_packages.changePackageCost') }}">
                        @csrf
                        {{method_field('put')}}
                        <label for="packageTemplatesList">@lang('order_packages.form.choose_template')</label>
                        <select required name="templateList" class="form-control text-uppercase"
                                id="packageTemplatesList"
                                form="changePackageCostForm">
                            <option value="" selected="selected"></option>
                            @foreach($templateData as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-group">
                            <label for="cost_for_client">@lang('order_packages.form.cost_for_client')</label>
                            <input id="cost_for_client" name="cost_for_client" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="cost_for_company">@lang('order_packages.form.cost_for_company')</label>
                            <input id="cost_for_company" name="cost_for_company" type="text" class="form-control">
                        </div>
                        <input type="hidden" name="changePackageCostId" id="changePackageCost">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="changePackageCostForm"
                            class="btn btn-success pull-right">@lang('order_packages.form.buttons.change')
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="add-custom-task" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">@lang('orders.additional_task')</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" target="_blank" id="create-new-task"
                          action="{{ route('planning.tasks.store') }}">
                        @csrf()
                        <div class="form-group">
                            <label for="user_id">@lang('orders.task_performing_employee')</label>
                            <select name="user_id" required class="form-control">
                                <option value="" selected="selected">wybierz użytkownika</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{$user->id}}">{{$user->name}} {{$user->firstname}} {{$user->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="task-name">Podaj nazwę zadania</label>
                            <input class="form-control" required name="name" id="task-name" type="text">
                        </div>
                        <input type="hidden" name="warehouse_id"
                               value="{{ Warehouse::OLAWA_WAREHOUSE_ID }}">
                        <input type="hidden" name="quickTask" value="1">
                        <input type="hidden" name="color" value="008000">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="create-new-task" class="btn btn-success pull-right">Wyślij
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="print-package-group" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('orders.task_performing_employee')</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" target="_blank" id="print-package-form"
                          action="{{ route('orders.findPackage') }}">
                        @csrf()
                        <div class="form-group">
                            <label for="user_id">Pracownik realizujący zadanie</label>
                            <select name="user_id" required class="form-control">
                                <option value="" selected="selected">wybierz użytkownika</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{$user->id}}">{{$user->name}} {{$user->firstname}} {{$user->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="task_id">Identyfikator zadania</label>
                            <select name="task_id" class="form-control" data-size="5">
                                <option value="" selected="selected">Kolejne zadanie</option>
                            </select>
                        </div>
                        <input name="package_type" id="print-package-type" type="hidden">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="print-package-form" class="btn btn-success pull-right">Wyślij
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="mark-as-created" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">@lang('orders.task_realized')</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="finish-task-form"
                          action="{{ route('planning.tasks.produceOrdersRedirect') }}">
                        @csrf()
                        <div class="form-group">
                            <label for="select-user-for-finish-task">Wybierz użytkownika</label>
                            <select onchange="fetchUsersTasks(this, '#select-task-for-finish')"
                                    id="select-user-for-finish-task" name="user_id" required
                                    class="form-control">
                                <option value="" selected="selected">brak</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{$user->id}}">{{$user->name}} {{$user->firstname}} {{$user->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select-task-for-finish">Wybierz zadanie</label>
                            <select
                                onchange="taskSelected(this, '#warehouse-done-notice', '#warehouse-done-notice-input')"
                                name="id" id="select-task-for-finish" required class="form-control">
                                <option value="" selected="selected">brak</option>
                            </select>
                        </div>
                        <div class="form-group" id="warehouse-done-notice">
                            <label for="warehouse_notice">Podaj nazwę zadania</label>
                            <input id="warehouse-done-notice-input" class="form-control" name="warehouse_notice"
                                   type="text">
                        </div>
                    </form>
                    <div id="error-finish-task-form"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="finish-task-form" id="check-finish-task"
                            class="btn btn-success pull-right">Zakończ
                    </button>
                    <button id="check-finish-task-refresh" class="btn btn-primary pull-right">Odświez</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="quantity-in-stock-list" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">@lang('orders.task_realized')</h4>
                </div>
                <div class="modal-body">
                    <div class="error-finish-task-form"></div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="mark-as-created-desc" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">@lang('orders.task_realized')</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="finish-task-form2"
                          action="{{ route('planning.tasks.produceOrdersRedirect') }}">
                        @csrf()
                        <div class="form-group">
                            <label for="select-user-for-finish-task2">Wybierz użytkownika</label>
                            <select onchange="fetchUsersTasks(this, '#select-task-for-finish2')"
                                    id="select-user-for-finish-task2" name="user_id" required
                                    class="form-control">
                                <option value="" selected="selected">brak</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{$user->id}}">{{$user->name}} {{$user->firstname}} {{$user->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select-task-for-finish2">Wybierz zadanie</label>
                            <select
                                onchange="taskSelected(this, '#warehouse-done-notice2', '#warehouse-done-notice-input2')"
                                name="id" id="select-task-for-finish2" required class="form-control">
                                <option value="" selected="selected">brak</option>
                            </select>
                        </div>
                        <div class="form-group" id="warehouse-done-notice2">
                            <label for="warehouse_notice">Podaj nazwę zadania</label>
                            <input id="warehouse-done-notice-input2" class="form-control" name="warehouse_notice"
                                   type="text">
                        </div>
                        <div class="form-group" id="task-description">
                            <label for="description">Opis</label>
                            <textarea id="task-description-input" class="form-control" name="description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="finish-task-form2" class="btn btn-success pull-right">Zakończ
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="mark-as-denied" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="deny-task-form" action="{{ route('planning.tasks.deny') }}">
                        @csrf()
                        <div class="form-group">
                            <label for="select-user-for-deny-task">Wybierz użytkownika</label>
                            <select onchange="fetchUsersTasks(this, '#select-task-for-deny')"
                                    id="select-user-for-deny-task" name="user_id" required
                                    class="form-control">
                                <option value="" selected="selected">brak</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{$user->id}}">{{$user->name}} {{$user->firstname}} {{$user->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select-task-for-deny">Wybierz zadanie</label>
                            <select name="task_id" id="select-task-for-deny" required class="form-control">
                                <option value="" selected="selected">brak</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deny-task-description">Notatka</label>
                            <input class="form-control" type="text" required id="deny-task-description"
                                   name="description">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="deny-task-form" class="btn btn-success pull-right">Zakończ
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="magazine" role="dialog">
        <div class="modal-dialog modal-dialog-timesheet" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Proszę wybrac magazyn: </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select id="selectWarehouse" class="form-control">
                            <option value="0" selected="selected">Wybierz magazyn</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}" class="warehouseSelect"
                                        id="warehouseSelect">{{$warehouse->symbol}}</option>
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
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Proszę wybrac magazyn: </h4>
                </div>
                <div class="modal-body">
                    <form id="addWarehouse" class="form-group">
                        <select required name="warehouse_id" class="form-control">
                            <option value="" selected="selected">Wybierz magazyn</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}"
                                        class="warehouseSelect">{{$warehouse->symbol}}</option>
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
    <div class="modal fade" tabindex="-1" id="upload-payments" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Wybierz dostawcę i jego plik w formacie .csv: </h4>
                </div>
                <div class="modal-body">
                    <form id="updateTransportPayment" action="{{ route('transportPayment.update_pricing') }}"
                          method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        Dostawca:
                        <br/>
                        <select required name="delivererId" class="form-control text-uppercase">
                            <option value="" selected="selected"></option>
                            @foreach($deliverers as $deliver)
                                <option value="{{ $deliver->id }}">{{ $deliver->name }}</option>
                            @endforeach
                        </select>
                        <br/>
                        Plik:
                        <br/>
                        <input type="file" name="file"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="updateTransportPayment" class="btn btn-success pull-right">Wyślij
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="add-new-sell-invoice" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Dodaj nowy plik:</h4>
                </div>
                <div class="modal-body">
                    <form id="addNewSellInvoiceToOrder"
                          action="{{ route('invoices.addInvoice') }}"
                          method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        Plik:
                        <br/>
                        <input accept=".pdf,image/*" type="file" name="file"/>
                        <input type="hidden" value="sell" name="type"/>
                        <input type="hidden" id="new-sell-invoice-order-id" name="order_id"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="addNewSellInvoiceToOrder" class="btn btn-success pull-right">Wyślij
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="add-new-file" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Dodaj nową fakturę sprzedaży:</h4>
                </div>
                <div class="modal-body">
                    <form id="addNewFileToOrder"
                          method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        Plik:
                        <br/>
                        <input accept=".pdf,image/*" type="file" name="file"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="addNewFileToOrder" class="btn btn-success pull-right">Wyślij
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="upload-allegro-payments" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Dołącz plik płatności od allegro w formacie .csv: </h4>
                </div>
                <div class="modal-body">
                    <form id="updateAllegroPayment" action="{{ route('orders.allegroPayments') }}"
                          method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        Plik:
                        <br/>
                        <input type="file" name="file"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="updateAllegroPayment" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="upload-allegro-commission-modal" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Dołącz plik prowizji od allegro w formacie .csv: </h4>
                </div>
                <div class="modal-body">
                    <form id="uploadAllegroComission" action="{{ route('orders.allegroCommission') }}"
                          method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        Plik:
                        <br/>
                        <input type="file" name="file"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="uploadAllegroComission" class="btn btn-success pull-right">Wyślij
                    </button>
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
    <div class="modal fade" tabindex="-1" id="createSimilarPackage" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wybierz szablon paczki</h4>
                </div>
                <div class="modal-body">
                    <form id="createSimilarPackForm" method="POST">
                        @csrf
                        <select required name="templateList" class="form-control text-uppercase" id='templates'
                                form="createSimilarPackForm">
                            <option value="" selected="selected"></option>
                            @foreach($templateData as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="createSimilarPackForm" class="btn btn-success pull-right">Utwórz
                    </button>
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


    <div class="modal fade" tabindex="-1" id="break-down-pack-modal" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Rozbij zadanie</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="finish-breakDownTask"
                          action="{{ route('planning.tasks.breakDownTask') }}">
                        @csrf()
                        <div class="form-group">
                            <label for="select-task-with-child-for-finish">Wybierz zadanie</label>
                            <select
                                onchange="fetchChildren(this,'.form-group-checkobox')"
                                name="task" id="select-task-with-child-for-finish" required class="form-control">
                                <option value="" selected="selected">brak</option>
                            </select>
                        </div>
                        <div class="form-group-checkobox">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="finish-breakDownTask" class="btn btn-success pull-right">Rozbij
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="add-withdraw-task" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Potwierdź</h4>
                </div>
                <div class="modal-body">
                    <p>Dany klient ma juz wyprodukowaną ofertę/grupę ofert, która lezy na tym magazynie</p>
                    <p>Zalecane sprawdznie czy jest mozliwośc dołączenia do paczek juz istnijących.</p>
                    <form method="POST" id="addToPlanner"
                          action="{{ url('admin/planning/tasks/store/planner') }}">
                        @csrf()
                        <input type="hidden" id="add-withdraw-task-delivery_warehouse" name="delivery_warehouse"
                               value="">
                        <input type="hidden" id="add-withdraw-task-order_id" name="order_id" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="withdrawTask" id="withdrawTaskButton" class="btn btn-danger">Wycofaj,
                        chcę sprawdzic
                    </button>
                    <button type="submit" form="addToPlanner" class="btn btn-success pull-right">Dodaj
                    </button>
                </div>
            </div>
        </div>
    </div>



    @include('orders.buttons')
    <form action="{{route('change.order.limit')}}" method="post" class="card warning">
        @csrf
        <div class="card-body">
            <h4 class="card-title">Ustawienia limitów wyświetlanych danych</h4>
            <div class="form-inline">

                <input type="checkbox" name="isActive" class="form-check" value="1"
                       @if( \Illuminate\Support\Facades\Cookie::get('activeOrderLimit', '1') === '1')checked="checked"@endif />
                <label class="form-label">pokaż</label>
                <input type="number" size="2" class="form-control ml-1 mr-1" style="width:70px"
                       value="{{ \Illuminate\Support\Facades\Cookie::get('orderLimitInDays', 30)  }}"
                       name="daysLimit"/>
                <label class="form-label">ostatnich dni</label>
                <input type="submit" value="Zapisz ustawienia limitów" class="btn btn-warning">
            </div>

        </div>
    </form>

    <div>
        <form method="post" action="{{ route('differenceInShipmentCostCookies') }}">
            @csrf
            RWKTAP:
            <br>
            <br>
            Większe lub mniejsze od:
            <select name="gratherOrLess" class="form-control">
                <option value="<" {{ cookie('gratherOrLess') === '<' ? 'selected' : '' }}><</option>
                <option value=">" {{ cookie('gratherOrLess') === '>' ? 'selected' : '' }}>></option>
            </select>

            Ilość:
            <input class="form-control" id="differenceInShipmentCost" name="differenceInShipmentCost" value="{{ \Illuminate\Support\Facades\Cookie::get('differenceInShipmentCost') }}" type="number" >
            <button id="difference-shipment-button" class="btn btn-primary">
                Zapisz
            </button>
        </form>
    </div>

    <div>
        <form method="post" action="{{ route('shipmentCostFilterCookie') }}">
            @csrf

            Całkowity koszt transportu
            <br>
            <br>

            @php
                $shipmentCostFilter = json_decode(\Illuminate\Support\Facades\Cookie::get('shipment_cost_filter'));

                $shipmentCostFrom = $shipmentCostFilter->from ?? null;
                $shipmentCostTo = $shipmentCostFilter->to ?? null;
            @endphp

            <input placeholder="od" class="form-control" id="shipmentCostFrom" name="from" value="{{ $shipmentCostFrom }}" type="number" >
            <input placeholder="do" class="form-control" id="shipmentCostTo" name="to" value="{{ $shipmentCostTo }}" type="number" >

            <button class="btn btn-primary">
                Pokaż
            </button>
        </form>
    </div>


    <button name="selectOnlyWrongInvoiceBilansOrders" id="selectOnlyWrongInvoiceBilansOrders"
            class="btn btn-primary"></button>
    <table id="dataTable" class="table table-hover spacious-container ordersTable">
        <thead>
        <tr>
            <th></th>
            <th>@lang('orders.table.spedition_exchange_invoiced_selector')</th>
            <th>
                <div style="height: 20px"><span>@lang('orders.table.packages_sent')</span></div>
                <div style="height: 40px">
                    <input type="text" class="delivery_cost_balance form-control"
                           style="margin-bottom: 5px; margin-top: 5px">
                </div>
                <div class="d-flex" style="height: 20px">
                    <button class="badge badge-success positive-balance">+</button>
                    <button class="badge badge-danger negative-balance">-</button>
                    <button class="badge badge-primary clear-balance">All</button>
                </div>
                <div class="input_div">
                    <select class="columnSearchSelect" id="columnSearch-packages_sent">
                        <option value="">Wszystkie</option>
                        @foreach($couriers as $courier)
                            <option
                                value="{{$courier->delivery_courier_name}}">{{$courier->delivery_courier_name}}</option>
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
                            <option
                                value="{{$courier->delivery_courier_name}}">{{$courier->delivery_courier_name}}</option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>@lang('orders.table.production_date')</th>
            <th>
                <div style="width: 120px;"><span>@lang('orders.table.dates')</span></div>
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
                    <span title="KLIKNIECIE TEGO POLA USUWA FILTRY"><input type="text"
                                                                           id="columnSearch-orderId"/></span>
                </div>
            </th>
            <a href="{{ route('admin-order-TWSU.create') }}" class="btn btn-primary">
                Stwórz zamówienie TWSU
            </a>
            <th>
                <span id="columnSearch-actions">@lang('orders.table.actions')</span>
            </th>
            <th>@lang('orders.table.section')</th>
            <th>
                <div><span>@lang('orders.table.statusName')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-statusName"/>
                    <button class="btn btn-default" id="set_outdate" onclick="
                        let is_filter = !$(this).hasClass('btn-success');
                        if (is_filter) {
                            $(this).addClass('btn-success')
                        } else {
                            $(this).removeClass('btn-success')
                        }
                        $('#columnSearch-remainder_date').val(is_filter)
                        event.stopPropagation();
                                table
                                    .column('remainder_date:name')
                                .search(is_filter)
                                .draw()">Przedawnione
                    </button>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.symbol')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-symbol"/>
                    <button class="btn btn-default" id="filterByWarehouseMegaOlawa"
                            onclick="event.stopPropagation(); filterByWarehouseMegaOlawa()">M
                    </button>
                </div>
            </th>
            <th>@lang('orders.form.warehouse_notice')
                <div class="input_div">
                    <input type="text" id="columnSearch-warehouse_notice"/>
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
                    <span title="KLIKNIJ PONOWNIE ABY USUNĄĆ FILTRY"><input type="text" id="columnSearch-clientPhone"/></span>
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
                <div><span>@lang('orders.table.values_data')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-values_data"/>
                </div>
            </th>
            <th>
                <div><span>@lang('orders.table.shipment_price_for_us')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-shipment_price_for_us"/>
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
                    <button class="btn btn-success" id="difference-button">Pokaż +/- 2 zł</button>
                    <input type="text" id="columnSearch-left_to_pay"/>
                </div>
            </th>
            <th>@lang('orders.table.transport_exchange_offers')</th>
            <th>@lang('orders.table.invoices')</th>
            <th>@lang('orders.table.invoice_gross_sum')</th>
            <th>@lang('orders.table.icons')</th>
            <th>@lang('orders.table.consultant_earning')</th>
            <th>@lang('orders.table.real_cost_for_company')</th>
            <th>@lang('orders.table.difference')</th>
            <th>@lang('orders.table.correction_amount')</th>
            <th>@lang('orders.table.correction_description')</th>
            <th>@lang('orders.table.document_number')</th>
            <th>
                <div><span>@lang('orders.table.payment_id')</span></div>
                <div class="input_div">
                    <input type="text" id="columnSearch-sello_payment"/>
                </div>
            </th>
            <th>@lang('orders.table.allegro_deposit_value')</th>
            <th>@lang('orders.table.allegro_operation_date')</th>
            <th>@lang('orders.table.allegro_additional_service')</th>
            <th>@lang('orders.table.payment_channel')</th>
            <th>
                <div><span>@lang('orders.form.remainder_date')</span></div>
                <div class="input_div">
                    <input hidden type="text" id="columnSearch-remainder_date" value="false"/>
                </div>
            </th>
            <th>@lang('orders.form.sell_invoices')</th>
            <th>@lang('orders.form.allegro_form_id')</th>
            <th>@lang('orders.form.allegro_commission')</th>
            <th></th>
        </tr>
        </thead>
    </table>
    <div class="vue-components">
        <tracker :enabled="true" :user="{{ Auth::user()->id }}"/>
    </div>
    @include('bonus.modal')
@endsection

@section('datatable-scripts')
    <script src="//cdn.jsdelivr.net/npm/jquery.scrollto@2.1.2/jquery.scrollTo.min.js"></script>
    <script src="{{ URL::asset('js/views/orders/taksWithChildren.js') }}"></script>
    <script>
        let getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };

        var available = [
            @php
                echo $allWarehousesString;
            @endphp
        ];

        const customerId = '{{ $customerId }}';

        $(document).ready(() => {
            $('#difference-shipment-button', (e) => {
                e.preventDefault();
                $('#difference-shipment-button').attr('disabled', true);

                // set cookies
                // gratherOrLess and differenceInShipmentCost
                let gratherOrLess = $('#gratherOrLess').val();
                let differenceInShipmentCost = $('#differenceInShipmentCost').val();

                // create or update cookies
                window.cookies.set('gratherOrLess', gratherOrLess, { expires: 365 });
                window.cookies.set('differenceInShipmentCost', differenceInShipmentCost, { expires: 365 });
            });

            let nof = getUrlParameter('nof');
            if (nof) {
                filterByPhone(nof);
            }
        });

        @can('create-bonus')
        $(document).on('click', '.penalty', (e) => {
            let orderId = $(e.target).attr('data-order');
            $('#add_bonus_modal').modal('show');
            $('#order_id').val(orderId);
            $('#consultant-name').html('<i class="fas fa-spinner fa-spin  fa-fw"></i>');
            $('#select-consultant').attr('disabled', true);
            $('#warehouse-name').html('<i class="fas fa-spinner fa-spin  fa-fw"></i>');
            $('#select-warehouse').attr('disabled', true);

            $.get('{{ route('bonus.users') }}/' + orderId, (data) => {
                $('#consultant-name').html(data['consultant']);
                $('#warehouse-name').html(data['warehouse']);

                if (data['consultant'] != 'BRAK') {
                    $('#select-consultant').removeAttr('disabled');
                }
                if (data['warehouse'] != 'BRAK') {
                    $('#select-warehouse').removeAttr('disabled');
                }
            });
        });
        @endcan

        @if (session('stock-response'))
        let stockResponse = JSON.parse('{!! json_encode(session('stock-response')) !!}');
        $('#position__errors').empty();
        $('#quantity__errors').empty();
        $('#exists__errors').empty();
        stockResponse.forEach((error) => {
            if (error.error == '{{ ProductStockError::POSITION }}') {
                $('#position__errors').append(`<h5>{{ __('product_stocks.form.missing_position_for_product') }} <span class="modal__product">${error.productName}</span>. {{ __('product_stocks.form.go_to_create_position') }} <a href="/admin/products/stocks/${error.product}/positions/create" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
            }
            if (error.error == '{{ ProductStockError::QUANTITY }}') {
                $('#quantity__errors').append(`<h5>{{ __('product_stocks.form.missing_product_quantity') }} <span class="modal__position">${error.position.position_quantity}</span>. {{ __('product_stocks.form.go_to_move_between_positions') }}<a href="/admin/products/stocks/${error.product}/edit?tab=positions" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
            }
            if (error.error == '{{ ProductStockError::EXISTS }}') {
                $('#exists__errors').append(`<h5>{{ __('product_stocks.form.for_product') }} <span class="modal__product">${error.productName}</span> {{ __('product_stocks.form.stock_already_performed') }} {{ __('product_stocks.form.go_to_order') }} <a href="/admin/orders/${error.order_id}/edit" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
            }
        })
        $('#stock_modal').modal('show');
        @endif
        function taskSelected(select, input, control) {
            let selectedOption = select.options[select.selectedIndex];
            if (selectedOption.dataset.order === '') {
                $(input).show();
            } else {
                $(input).hide();
                $(control).val('');
            }
        }

        function fetchUsersTasks(user, select) {
            $(select).empty();
            $(select).append('<option value="" selected="selected">brak</option>');
            let id = user.value
            let url = "{{route('planning.tasks.getForUser', ['id' => '%%'])}}"
            $.ajax(url.replace('%%', id))
                .done(response => {
                    if (response.errors) {
                        alert('Wystąpił błąd pobierania danych')
                        return;
                    }
                    response.forEach(task => $(select).append(`<option data-order="${task.order_id ?? ''}" class="temporary-option" value="${task.id}">${task.name}</option>`))
                })
        }

        function fetchTasksWithChildren(select) {
            $(select).empty();
            $(select).append('<option value="" selected="selected">brak</option>');
            let url = "{{route('planning.tasks.getTasksWithChildren')}}"
            $.ajax(url)
                .done(response => {
                    if (response.errors) {
                        alert('Wystąpił błąd pobierania danych')
                        return;
                    }
                    response.forEach(task => $(select).append(`<option data-order="${task.order_id ?? ''}" class="temporary-option" value="${task.id}">${task.name}</option>`))
                })
        }

        fetchTasksWithChildren('#select-task-with-child-for-finish');

        function fetchChildren(task, div) {
            $(div).empty();
            let id = task.value
            let url = "{{route('planning.tasks.getChildren', ['taskId' => '%%'])}}"
            $.ajax(url.replace('%%', id))
                .done(response => {
                    if (response.errors) {
                        alert('Wystąpił błąd pobierania danych')
                        return;
                    }
                    response.forEach(task => $(div).append(`<div><label><input type="hidden" name="task[${task.id}]" value="${task.id}"><input type="checkbox" name="task[${task.id}]" data-order="${task.order_id ?? ''}" value="0"> ${task.order_id}</label></div>`))
                })
        }

        function showPackageCostModal(packageId, dataTemplate, costForClient, costForCompany) {
            $('#changePackageCost').val(packageId);
            $('#packageTemplatesList option').prop('selected', '');
            if (!isNaN(dataTemplate)) {
                $('#packageTemplatesList option[value="' + dataTemplate + '"]').prop('selected', 'selected');
            } else {
                $("#packageTemplatesList option:contains('" + dataTemplate + "')").prop('selected', 'selected');
            }
            $('#cost_for_client').val(costForClient);
            $('#cost_for_company').val(costForCompany);
            $('#changePackageCostModal').modal('show');
        }

        $('#accept-pack').click(event => {
            $("#mark-as-created").modal('show');
        });
        $('#accept-pack-desc').click(event => {
            $("#mark-as-created-desc").modal('show');
        });
        $('#break-down-pack').click(event => {
            $("#break-down-pack-modal").modal('show');
        });
        $('#deny-pack').click(event => $('#mark-as-denied').modal('show'));
        $('#create-new-task-button').click(event => {
            $('#add-custom-task').modal('show');
        })
        $('form#print-package-form').on('submit', function (event) {
            let optionLabel = $('#print-package-group').find('[name="task_id"] option:selected').text();
            let orderId = optionLabel.split(' - ').shift();
            $('#columnSearch-orderId').val(orderId);
            $('#columnSearch-orderId').change();
            setTimeout(function () {
                let ofButton = $('tr#id' + '-' + orderId).find('button:contains("OF")');
                ofButton.trigger('click');
                $('#print-package-group').modal('hide');
            }, 5000);
        });

        $('.print-group .print-list').click(function () {
            $('#print-package-type').val($(this).attr('name'));
            let opt = $(this).data('couriertasks');
            let select = $('#print-package-group').find('[name="task_id"]');
            select.find('optgroup').remove();
            // sort by first word is number so filter by this id

            $.each(opt, function (key, value) {
                value = value.sort(function (a, b) {
                    let aId = a.name.split(' - ').shift();
                    let bId = b.name.split(' - ').shift();
                    return aId - bId;
                });

                if (value.length > 0) {
                    let group = $('<optgroup label="' + key + '" />');
                    $.each(value, function () {
                        $('<option />').html(this.name).val(this.id).appendTo(group);
                    });
                    group.appendTo(select);
                }
            });
            $('#print-package-group').modal('show');
        })
        $(".send_courier_class").click(event => {
            if (event.currentTarget.id == "filtered-packages") {
                return;
            }
            if (confirm("Na pewno zamówić kurierów?")) {
                $(".send_courier_class").each((id, item) => item.setAttribute("disabled", true));
                window.location.href = event.currentTarget.getAttribute("href");
            }
        })

        $("#filtered-packages").click(event => {
            if (!confirm("Na pewno zamówić kurierów?")) {
                return;
            }
            $(".send_courier_class").each((id, item) => item.setAttribute("disabled", true));
            $.post("{{route('orders.sendVisibleCouriers')}}", table.ajax.params())
                .done((data) => {
                    if (!data.errors) {
                        alert('Wysłano wszystkie paczki')
                    } else {
                        console.log(data.errors);

                        data.errors.forEach(error => {
                            console.log(error);
                            $('#packages_errors').append(`<p>${error}</p>`);
                        })
                    }
                });
        });

        $('.protocol_datepicker').datepicker({dateFormat: "dd/mm/yy"});

        $("#delivery_warehouse").autocomplete({
            source: available
        });
        $(function () {
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

        function createSimilar(id, orderId) {
            let action = "{{ route('order_packages.duplicate',['packageId' => '%id']) }}"
            action = action.replace('%id', id)
            $.ajax({
                url: action,
                method: 'POST'
            }).done(function (data) {
                table.ajax.reload(null, false);
            }).fail(function () {
                alert('Coś poszło nie tak')
            });
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
                    data: {
                        'redirect': false
                    }
                }).done(function (data) {
                    table.ajax.reload(null, false);
                }).fail(function () {
                    alert('Coś poszło nie tak')
                });
            }
        }

        function sendPackage(id, orderId) {
            $('#package-' + id).attr("disabled", true);
            $('#order_courier > div > div > div.modal-header > h4 > span').remove();
            $('#order_courier > div > div > div.modal-header > span').remove();
            $.ajax({
                url: '/admin/orders/' + orderId + '/package/' + id + '/send',
            }).done(function (data) {
                $('#order_courier').modal('show');
                if (data.message == 'Kurier zostanie zamówiony w przeciągu kilku minut' || data.message == null) {
                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Kurier zostanie zamówiony w przeciągu kilku minut</span>');
                } else {
                    $('#order_courier > div > div > div.modal-header > h4').append('<span>Jedno z wymaganych pól nie zostało zdefiniowane:</span>');
                    $('#order_courier > div > div > div.modal-header').append('<span style="color:red;">' + data.message.message + '</span><br>');
                }
                $('#package-' + id).attr("disabled", false);
                $('#success-ok').on('click', function () {
                    setTimeout(() => {
                        table.ajax.reload(null, false);
                    }, 500);
                });
            }).fail(function () {
                $('#package-' + id).attr("disabled", false);
                $('#order_courier_problem').modal('show');
                $('#problem-ok').on('click', function () {
                    table.ajax.reload(null, false);
                });
            });
        }

        // DataTable
        let datatable = function (ajaxParams = null) {
            return $('#dataTable').DataTable({
                language: {!! json_encode( __('voyager.datatable'), true) !!},
                searchDelay: 350,
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
                        'targets': 15,
                        'createdCell': function (td, cellData, rowData, row, col) {
                            $(td).attr('id', 'action-' + rowData.orderId);
                        }
                    },
                    {
                        'targets': 21,
                        'createdCell': function (td, cellData, rowData, row, col) {
                            $(td).attr('id', 'consultant_notices-' + rowData.orderId);
                            $(td).attr('class', 'hoverable');
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
                    data: function (d) {
                        if (ajaxParams !== null) {
                            d.dateFrom = ajaxParams.dateFrom;
                            d.dateTo = ajaxParams.dateTo;
                            d.dateColumn = ajaxParams.dateColumn;
                            d.same = ajaxParams.same;
                        }
                        d.customerId = customerId;
                        d.selectOnlyWrongInvoiceBilansOrders = localStorage.getItem('selectOnlyWrongInvoiceBilansOrders');
                        let differenceMode = localStorage.getItem('differenceMode');
                        if (differenceMode !== null) d.differenceMode = localStorage.getItem('differenceMode');
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                rowCallback: function (row, data, index) {
                    if (data.login == 'magazyn-olawa@mega1000.pl') {
                        $('td', row).css('background-color', 'rgb(0, 0, 255, 0.1)');
                    }
                },
                drawCallback: function (settings) {
                    setTimeout(function () {
                        $('.ui-tooltip-content').parent().remove()

                        $('[data-toggle="tooltip"]').tooltip();
                        $('[data-toggle="transport-exchange-tooltip"]').tooltip();
                        $('[data-toggle="label-tooltip"]').tooltip();

                        // make all tooltips font bigger
                        $('.tooltip-inner').css('font-size', '11.2em');
                        $('.tooltip-inner').css('font-weight', 'bold');

                        setDeleteEventListeners();
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
                            return '<input class="order-id-checkbox" value="' + id + '" type="checkbox">';
                        },
                    },
                    {
                        data: 'orderId',
                        name: 'spedition_exchange_invoiced_selector',
                        orderable: false,
                        render: function (id) {
                            return '<input class="spedition-exchange-selector-no-invoice" value="' + id + '" type="checkbox"><br><input class="spedition-exchange-selector-invoiced" value="' + id + '" type="checkbox">';
                        },
                    },
                    {
                        data: 'packages',
                        name: 'packages_sent',
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row) {
                            var html = '';
                            $.each(data, function (key, value) {
                                let isProblem = Math.abs((value.real_cost_for_company ?? 0) - (value.cost_for_company ?? 0)) > 2
                                if (isProblem) {
                                    html += '<div style="border: solid red 4px" >'
                                }
                                if (value.status === 'SENDING' || value.status === 'DELIVERED') {
                                    html += '<div style="display: flex; align-items: center; flex-direction: column;" > ' +
                                        '<div style="display: flex; align-items: center;">' +
                                        '<p style="margin: 8px 0 0 0;">' + row.orderId + '/' + value.number + '</p>'
                                    let name = value.container_type
                                    if (value.symbol) {
                                        name = value.symbol;
                                    }
                                    html += '<p style="margin: 8px 8px 0 8px;">' + name + '</p> </div> '
                                    html += value.sumOfCosts ? value.sumOfCosts.sum + ' zł' : '';
                                    if (value.delivery_cost_balance !== 0) {
                                        let color = '';
                                        if (value.delivery_cost_balance >= 0) {
                                            color = 'green';
                                        } else if (value.delivery_cost_balance < 0) {
                                            color = 'red';
                                        }
                                        html += '<p style="color:' + color + '">Bilans: ' + value.delivery_cost_balance + '</p>'
                                    }
                                    if (value.letter_number === null) {
                                        html += '<a href="javascript:void()"><p>Brak listu przewozowego</p></a>';
                                    } else {
                                        let color = '';
                                        switch (value.status) {
                                            case 'DELIVERED':
                                                color = '#87D11B';
                                                break;
                                            case 'SENDING':
                                                color = '#4DCFFF';
                                                break;
                                            case 'WAITING_FOR_SENDING':
                                                color = '#5537f0';
                                                break;
                                        }
                                        if (value.service_courier_name === 'INPOST' || value.service_courier_name === 'ALLEGRO-INPOST') {
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                            html += '<div>';
                                            if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                                html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                            }
                                            html += '<a target="_blank" style="color: green; font-weight: bold; color: #FFFFFF; display: inline-block; margin-top: 5px; margin-left: 5px; padding: 5px; background-color:' + color + '" href="https://inpost.pl/sledzenie-przesylek?number=' + value.letter_number + '"<i class="fas fa-shipping-fast"></i></a>';
                                            html += '</div>';
                                        } else if (value.delivery_courier_name === 'DPD') {
                                            html += '<p style="margin-bottom: 0px;">' + value.sending_number + '</p>';
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                            html += '<div>';
                                            if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                                html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                            }
                                            html += '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:' + color + '" href="https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=' + value.letter_number + '"><i class="fas fa-shipping-fast"></i></a>';
                                            html += '</div>';
                                        } else if (value.delivery_courier_name === 'POCZTEX') {
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                            html += '<div>';
                                            if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                                html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                            }
                                            html += '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:' + color + '" href="http://www.pocztex.pl/sledzenie-przesylek/?numer=' + value.letter_number + '"><i class="fas fa-shipping-fast"></i></a>';
                                            html += '</div>';
                                        } else if (value.delivery_courier_name === 'JAS') {
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                            if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                                html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                            }

                                            html += '<a target="_blank" href="/storage/jas/labels/label' + value.sending_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                        } else if (value.delivery_courier_name === 'GIELDA') {
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                        } else if (value.delivery_courier_name === 'ODBIOR_OSOBISTY') {
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                        } else if (value.delivery_courier_name === 'GLS') {
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}`;
                                            html += value.letter_number ? value.letter_number : 'wygeneruj naklejkę';
                                            html += '<div>';
                                            if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                                html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                            }
                                            html += '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; padding: 5px; margin-top: 5px;margin-left: 5px; background-color:' + color + '" href="https://gls-group.eu/PL/pl/sledzenie-paczek?match=' + value.letter_number + '"><i class="fas fa-shipping-fast"></i></a>';
                                            html += '</p></a>';
                                            html += '</div>';
                                        } else if (value.delivery_courier_name === 'DB') {
                                            html += '<a target="_blank" href="/storage/db_schenker/protocols/protocol' + value.sending_number + '.pdf"><p>LP: ' + value.sending_number + '</p></a>';
                                            html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>KP: ${value.letter_number}</p></a>`;
                                        }

                                    }
                                    html += `<button class="btn btn-primary" onclick="showPackageCostModal('${value.id}', '${value.chosen_data_template}', '${value.cost_for_client}', '${value.cost_for_company}')">@lang('order_packages.form.buttons.changePackageCost')</button>`;
                                    html += '</div>';
                                } else if (value.delivery_courier_name === 'DB' && value.status !== 'NEW') {
                                    html += '<a target="_blank" href="/storage/db_schenker/protocols/protocol' + value.sending_number + '.pdf"><p>LP: ' + value.sending_number + '</p></a>';
                                    html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>KP: ${value.letter_number}</p></a>`;
                                    html += '</div>';
                                }
                                if (isProblem) {
                                    html += '</div>'
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
                        render: function (order, type, row) {
                            let data = order.packages
                            let html = '';
                            if (data.length !== 0) {
                                if (order.otherPackages && order.otherPackages.find(el => el.type == 'not_calculable')) {
                                    html = '<div style="border: solid blue 4px" >'
                                } else {
                                    html = '<div style="border: solid green 4px" >'
                                }
                            }
                            let cancelled = 0;
                            $.each(data, function (key, value) {
                                let color = '';
                                switch (value.status) {
                                    case 'DELIVERED':
                                        color = '#87D11B';
                                        break;
                                    case 'SENDING':
                                        color = '#4DCFFF';
                                        break;
                                    case 'WAITING_FOR_SENDING':
                                        color = '#5537f0';
                                        break;
                                }
                                if (value.status === 'CANCELLED') {
                                    cancelled++;
                                }
                                if (value.status !== 'SENDING' && value.status !== 'DELIVERED' && value.status !== 'CANCELLED') {
                                    html += '<div style="display: flex; align-items: center; flex-direction: column;" > ' +
                                        '<div style="display: flex; align-items: stretch;">' +
                                        '<p style="margin: 8px 0 0 0;"> ' + row.orderId + '/' + value.number + '</p>'
                                    let name = value.container_type
                                    if (value.symbol) {
                                        name = value.symbol;
                                    }
                                    html += '<p style="margin: 8px 8px 0 8px;">' + name + '</p> </div> '

                                    if (value.status === 'WAITING_FOR_CANCELLED') {
                                        html += '<p>WYSŁANO DO ANULACJI</p>';
                                    }
                                    if (value.status === 'REJECT_CANCELLED') {
                                        html += '<p style="color:red;">ANULACJA ODRZUCONA</p>';
                                    }
                                    if (value.letter_number === null) {
                                        if (value.status !== 'CANCELLED' && value.status !== 'WAITING_FOR_CANCELLED' && value.delivery_courier_name !== 'GIELDA' && value.service_courier_name !== 'GIELDA' && value.delivery_courier_name !== 'ODBIOR_OSOBISTY' && value.service_courier_name !== 'ODBIOR_OSOBISTY') {
                                            html += '<div style="display: flex;">'
                                            html += '<button class="btn btn-success" id="package-' + value.id + '" onclick="sendPackage(' + value.id + ',' + value.order_id + ')">Wyślij</button>';
                                            html += '<button class="btn btn-danger" onclick="deletePackage(' + value.id + ', ' + value.order_id + ')">Usuń</button>'
                                            html += '<button class="btn btn-info" onclick="createSimilar(' + value.id + ', ' + value.order_id + ')">Podobna</button>'
                                            html += '</div>'
                                        }
                                    }
                                    if (value.service_courier_name === 'INPOST' || value.service_courier_name === 'ALLEGRO-INPOST') {
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                        html += '<div>';
                                        if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                            html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                        }
                                        html += '<a target="_blank" style="color: green; font-weight: bold; color: #FFFFFF; display: inline-block; margin-top: 5px; margin-left: 5px; padding: 5px; background-color:' + color + '" href="https://inpost.pl/sledzenie-przesylek?number=' + value.letter_number + '"<i class="fas fa-shipping-fast"></i></a>';
                                        html += '</div>';
                                    } else if (value.delivery_courier_name === 'DPD') {
                                        html += '<a target="_blank" href="/storage/dpd/protocols/protocol' + value.letter_number + '.pdf"><p>' + value.sending_number + '</p></a>';
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                        html += '<div>';
                                        if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                            html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                        }
                                        html += '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:' + color + '" href="https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=' + value.letter_number + '"><i class="fas fa-shipping-fast"></i></a>';
                                        html += '</div>';
                                    } else if (value.delivery_courier_name === 'POCZTEX') {
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                        html += '<div>';
                                        if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                            html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                        }
                                        html += '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:' + color + '" href="http://www.pocztex.pl/sledzenie-przesylek/?numer=' + value.letter_number + '"><i class="fas fa-shipping-fast"></i></a>';
                                        html += '</div>';
                                    } else if (value.delivery_courier_name === 'JAS') {
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                        html += '<a target="_blank" href="/storage/jas/labels/label' + value.sending_number + '.pdf"><p>' + value.letter_number + '</p></a>';
                                    } else if (value.delivery_courier_name === 'GIELDA') {
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                    } else if (value.delivery_courier_name === 'ODBIOR_OSOBISTY') {
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                    } else if (value.delivery_courier_name === 'GLS') {
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}`;
                                        html += value.letter_number ? value.letter_number : 'wygeneruj naklejkę';
                                        html += '<div>';
                                        if (value.cash_on_delivery !== null && value.cash_on_delivery > 0) {
                                            html += '<span>' + value.cash_on_delivery + ' zł</span>';
                                        }
                                        html += '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; padding: 5px; margin-top: 5px;margin-left: 5px; background-color:' + color + '" href="https://gls-group.eu/PL/pl/sledzenie-paczek?match=' + value.letter_number + '"><i class="fas fa-shipping-fast"></i></a>';
                                        html += '</p></a>';
                                        html += '</div>';
                                    } else if (value.delivery_courier_name === 'DB') {
                                        html += '<a target="_blank" href="/storage/db_schenker/protocols/protocol' + value.sending_number + '.pdf"><p>LP: ' + value.sending_number + '</p></a>';
                                        html += `<a target="_blank" href="/admin/orders/packages/${value.id}/sticker"><p>${value.letter_number}</p></a>`;
                                    }
                                    html += '<div style="display: flex;">'
                                    html += '<button class="btn btn-danger" onclick="cancelPackage(' + value.id + ', ' + value.order_id + ')">Anuluj</button>'
                                    html += '<button class="btn btn-info" onclick="createSimilar(' + value.id + ', ' + value.order_id + ')">Podobna</button>'
                                    html += '</div>'
                                }

                                html += '</div>';
                            });
                            if (cancelled > 0) {
                                let url = "{{ route('orders.editPackages', ['id' => '%%']) }}";
                                url = url.replace('%%', order.orderId)
                                html += `<a target=+_blank href="${url}">Anulowano: ${cancelled}</a>`;
                            }

                            return html;
                        }
                    },
                    {
                        data: 'production_date',
                        name: 'production_date',
                        searchable: false,
                        render: (production_date, option, row) => ((production_date ?? '') + ' ' + (row.taskUserFirstName ?? '') + ' ' +
                            (row.taskUserLastName ?? ''))
                    },
                    {
                        data: 'shipment_date',
                        name: 'shipment_date',
                        searchable: false,
                        render: function (shipment_date, option, row) {
                            let html = '';
                            let date_from = moment(row.consultant_shipment_date_from);
                            let date_to = moment(row.consultant_shipment_date_to);
                            let shipmentDateFrom = date_from.isValid() ? date_from.format('YYYY-MM-DD') : null;
                            let shipmentDateTo = date_to.isValid() ? date_to.format('YYYY-MM-DD') : null;

                            const datesObject = {
                                'Od': shipmentDateFrom,
                                'Do': shipmentDateTo,
                            }

                            for (const [key, value] of Object.entries(datesObject)) {
                                if (value != null) {
                                    html += `${key}: <br/> ${value} <br/>`
                                }
                            }

                            return html;
                        }
                    },
                        @foreach($customColumnLabels as $labelGroupName => $label)
                    {
                        data: null,
                        name: 'label_{{str_replace(" ", "_", $labelGroupName)}}',
                        searchable: false,
                        orderable: false,
                        render: function (order, option, row) {
                            let labels = order.labels;
                            let html = '';
                            let currentLabelGroup = "{{ $labelGroupName }}";
                            if (row.closest_label_schedule_type_c && currentLabelGroup == "info dodatkowe") {
                                html += row.closest_label_schedule_type_c.trigger_time;
                            }
                            if (currentLabelGroup === "info dodatkowe") {
                                html += '<a href="#" class="add__file"' + 'onclick="addNewFile(' + order.orderId + ')">Dodaj</a>'
                                html += '<br />'
                                let url = "{{ route('orders.getFile', ['id' => '%%', 'file_id' => 'QQ']) }}";
                                let files = order.files;
                                if (files.length > 0) {
                                    let orderUrl = url.replace('%%', order.orderId);
                                    files.forEach(function (file) {
                                        let href = orderUrl.replace('QQ', file.hash);
                                        html += `<a target="_blank" href="${href}" style="margin-top: 5px;">${file.file_name}</a>`;
                                        html += '<br />'
                                    });
                                    html += '<a href="#" class="remove__file"' + 'onclick="getFilesList(' + order.orderId + ')">Usuń</a>'
                                }
                            }

                            labels.forEach(function (label) {
                                if (label.length > 0) {
                                    if (label[0].label_group_id != null) {
                                        if (label[0].label_group[0].name == currentLabelGroup) {
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
                                                d1.setHours(0, 0, 0, 0)
                                                d2.setHours(0, 0, 0, 0)
                                                comparasion = d1 >= d2
                                            }
                                            if (label[0].id == '{{ env('MIX_LABEL_WAITING_FOR_PAYMENT_ID') }}' && comparasion) {
                                                html += `<div data-toggle="label-tooltip" style="border: solid red 4px" data-html="true" title="${tooltipContent + ' ' + row.created_at}" class="pointer" onclick="removeLabel(${row.orderId}, ${label[0].id}, ${label[0].manual_label_selection_to_add_after_removal}, '${label[0].added_type}', '${label[0].timed}');">
                                                        <span class="order-label" style="color: ${label[0].font_color}'; display: block; margin-top: 5px; background-color: ${label[0].color}">
                                                            <i class="${label[0].icon_name}"></i>
                                                        </span>
                                                     </div>`;
                                            } else {
                                                html += `<div data-toggle="label-tooltip" data-html="true" title="${tooltipContent + ' ' + ((label[0].id === '66') ? row.production_date : row.created_at)}" class="pointer" onclick="removeLabel(${row.orderId}, ${label[0].id}, ${label[0].manual_label_selection_to_add_after_removal}, '${label[0].added_type}', '${label[0].timed}');">
                                                        <span class="order-label" style="color: ${label[0].font_color}; display: block; margin-top: 5px; background-color: ${label[0].color}">
                                                            <i class="${label[0].icon_name}"></i>
                                                        </span>
                                                     </div>`;
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
                        data: 'token',
                        name: 'print',
                        orderable: false,
                        render: function (token, row, data) {
                            let html = '';
                            if (data.print_order == '0') {
                                html += '<a href="/admin/orders/' + token + '/print" target="_blank" class="btn btn-default" id="btn-print">W</a>';
                            } else {
                                html += '<a href="/admin/orders/' + token + '/print" target="_blank" class="btn btn-success">W</a>';
                            }
                            if (data.items.length) {
                                if (data.orderId != null) {
                                    html += '<a href="/admin/orderReturn/' + data.orderId + '/print" target="_blank" class="btn btn-danger">WP</a>';
                                } else {
                                    html += '<a href="/admin/orderReturn/' + token + '/print" target="_blank" class="btn btn-danger">WP</a>';
                                }
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
                        render: function (orderId, row, data) {
                            let html = '';
                            if (data.master_order_id == null) {
                                if (data.allegro_form_id) {
                                    html += ' (A)'
                                }
                                for (let i = 0; i < data.connected.length; i++) {
                                    html += '<span style="display: block;">(P)' + data.connected[i].id + '</span>';
                                }
                                html = '<a target="_blank" href="/admin/planning/timetable?id=taskOrder-' + orderId + '">(G)' + orderId + '</a>' + html;
                            } else {
                                html = '<a target="_blank" href="/admin/planning/timetable?id=taskOrder-' + orderId + '">(P)' + orderId + '</a><span style="display: block;">(G)' + data.master_order_id + '</span>';
                            }
                            let array = {{ json_encode(Label::NOT_SENT_YET_LABELS_IDS) }};
                            let batteryId = {{ Label::ORDER_ITEMS_REDEEMED_LABEL }};
                            let hasHammerOrBagLabel = data.labels.filter(label => {
                                return array.includes(parseInt(label[0].id));
                            }).length > 0;
                            let isNotProducedYet = data.labels.filter(label => {
                                return parseInt(label[0].id) === batteryId;
                            }).length == 0;
                            if (hasHammerOrBagLabel && isNotProducedYet) {
                                html += data.history.reduce((acu, order) => {
                                    if (order.id === orderId) {
                                        return acu;
                                    }
                                    let hasChildHammerOrBagLabel = order.labels.filter(label => {
                                        return array.includes(parseInt(label.id));
                                    }).length > 0;
                                    let isChildNotProducedYet = order.labels.filter(label => {
                                        return parseInt(label.id) === batteryId;
                                    }).length === 0;
                                    if (hasChildHammerOrBagLabel && isChildNotProducedYet) {
                                        let url = "{{ route('orders.edit', ['order_id' => ':id:']) }}"
                                        return acu += '<a target="_blank" href="' + url.replace(":id:", order.id) + `"> (D)${order.id}</a>`
                                    }
                                    return acu;
                                }, '');

                            }
                            return html;
                        }
                    },
                    {
                        data: 'orderId',
                        name: 'actions',
                        orderable: false,
                        render: function (id, row, data) {
                            let html = '';
                            html += '<button id="moveButton-' + id + '" class="btn btn-sm btn-warning edit" onclick="moveData(' + id + ')">Przenieś</button>';
                            html += '<button id="moveButtonAjax-' + id + '" class="btn btn-sm btn-success btn-move edit hidden" onclick="moveDataAjax(' + id + ')">Przenieś dane tutaj</button>';
                            html += '<a href="{{ url()->current() }}/' + id + '/edit" class="btn btn-sm btn-primary edit">';
                            html += '<i class="voyager-edit"></i>';
                            html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.edit')</span>';
                            html += '</a>';
                            if (data.items.length) {
                                html += '<a href="/admin/orderReturn/' + id + '" class="btn btn-sm btn-danger edit">';
                                html += '<i class="glyphicon glyphicon-share-alt"></i>';
                                html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.return')</span>';
                                html += '</a>';

                                // allegro return button
                                html += `<a href="/admin/allegro/return-payment/${id}" class="btn btn-sm btn-danger edit">
                                    <i class="glyphicon glyphicon-share-alt"></i>
                                    <span class="hidden-xs hidden-sm"> @lang('voyager.generic.allegro_return')</span>
                                </a>`;
                            }
                            @if((Auth::user()->role_id == 1 || Auth::user()->role_id == 2) && Auth::user()->id === User::ORDER_DELETE_USER)
                                html += '<button class="btn btn-sm btn-danger delete delete-record" onclick="deleteRecord(' + id + ')">';
                            html += '<i class="voyager-trash"></i>';
                            html += '<span class="hidden-xs hidden-sm"> @lang('voyager.generic.delete')</span>';
                            html += '</button>'
                            html += '<button data-order="' + id + '"';
                            html += ' class="btn penalty btn-danger btn-sm edit"><i class="fas fa-minus"></i> Potrącenie</button>';
                            html += '<a href="{{ route('transactions.index') }}?email=' + data.email + '" class="btn edit btn-sm btn-success">Transakcje</a>';
                            @endif
                                html += `<a target="_blank" class="btn btn-sm btn-primary" href="/admin/create-package-product-order/${id}">Stwórz produkt pakowy</a>`;
                            if (data.is_buying_admin_side) {
                                html += `<a class="btn btn-primary" href="/admin/accept-products/${id}" target="__blank">Przyjmij na stany magazynowe</a>`
                            }

                            return html;
                        }
                    },
                    {
                        data: 'orderId',
                        name: 'section',
                        orderable: false,
                        render: function () {
                            return 'tymczasowy brak';
                        }
                    },
                    {
                        data: 'statusName',
                        name: 'statusName',
                        render: function (field, type, row) {
                            if (field == null) {
                                return "";
                            }

                            let color = "";

                            function checkReminder(row, color) {
                                if (row.remainder_date) {
                                    if (moment(row.remainder_date).isBefore()) {
                                        color = "orange";
                                    }
                                }
                                return color
                            }

                            switch (field) {
                                case "przyjete zapytanie ofertowe":
                                    color = "red";
                                    break;
                                case "oferta bez realizacji":
                                    color = "#AAB78F";
                                    break;
                                case "oferta zakonczona":
                                    color = checkReminder(row, "green");
                                    break;
                                case "w trakcie realizacji":
                                case "w trakcie analizowania przez konsultanta":
                                case "mozliwa do realizacji":
                                case "mozliwa do realizacji kominy":
                                case "oferta oczekujaca":
                                    color = checkReminder(row, "blue");
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
                            if (data !== null) {
                                warehouse = '<a class="warehouse-symbol" href="/admin/warehouses/' + data + '/editBySymbol">' + data + '</a>';
                            } else {
                                warehouse = '';
                            }
                            return warehouse;
                        }
                    },
                    {
                        data: 'warehouse_notice',
                        name: 'warehouse_notice'
                    },
                    {
                        data: 'customer_notices',
                        name: 'customer_notices'
                    },
                    {
                        data: 'consultant_notices',
                        name: 'consultant_notices',
                        render: function (data, type, row) {
                            if (data !== null) {
                                var text = data;
                                var shortText = data.substr(0, 49) + "...";
                                $('#consultant_notices-' + row.orderId).hover(function () {
                                    $('#consultant_notices-' + row.orderId).text(text);
                                }, function () {
                                    $('#consultant_notices-' + row.orderId).text(shortText);
                                });
                                $('#consultant_notices-' + row.orderId).text(shortText);
                            }
                            return data;
                        }
                    },
                    {
                        data: 'clientPhone',
                        name: 'clientPhone',
                        render: function (data, type, row) {
                            var phone = row['clientPhone'];
                            var email = row['clientEmail'];

                            if (email === null) {
                                email = 'Brak adresu email.'
                            }

                            let tooltipTitle = email;
                            tooltipTitle += '&#013;';
                            tooltipTitle += '&#013;' + 'Dane do wysylki:';
                            $.each(row.addresses[0], function (index, value) {
                                if (index !== 'type' && index !== 'created_at' && index !== 'updated_at') {
                                    if (value === null) {
                                        tooltipTitle += ' Brak';
                                    } else {
                                        tooltipTitle += ' ' + value + ',';
                                    }
                                }
                            });
                            tooltipTitle += '&#013;' + 'Dane do faktury:';
                            $.each(row.addresses[1], function (index, value) {
                                if (index !== 'type' && index !== 'created_at' && index !== 'updated_at') {
                                    if (value === null) {
                                        tooltipTitle += ' Brak,';
                                    } else {
                                        tooltipTitle += ' ' + value + ',';
                                    }
                                }
                            });
                            tooltipTitle += '&#013;';
                            let html = '';

                            if (data) {
                                html += '<button class="btn btn-default btn-xs" onclick="filterByPhone(' + data + ')">F</button><button class="btn btn-default btn-xs" onclick="clearAndfilterByPhone(' + data + ')">OF</button>';
                            }
                            html += "<a style='width:100%' target='_blank' href='/admin/orders?nof=" + data + "' class='btn btn-success'>NOF</a>";

                            html += '<p data-toggle="tooltip" data-html="true" title="' + tooltipTitle + '">' + phone + '</p>';

                            return html;
                        }
                    },
                    {
                        data: 'clientEmail',
                        name: 'clientEmail',
                        render: function (data, type, row) {
                            var email = row['clientEmail'];

                            let html = email;

                            return html;
                        }
                    },
                    {
                        data: 'clientFirstname',
                        name: 'clientFirstname',
                        render: function (data, type, row) {
                            var firstname = row['clientFirstname'];

                            let html = firstname;

                            return html;
                        }
                    },
                    {
                        data: 'clientLastname',
                        name: 'clientLastname',
                        render: function (data, type, row) {
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
                        render: function (date, type, row) {
                            let RKTBO = row.rc, PSIK = 0, PSW = 0, WAC = 0, ZP = 0;

                            let sumOfSelling = 0;
                            let sumOfPurchase = 0;
                            const items = row['items'];

                            for (let index = 0; index < items.length; index++) {
                                let priceSelling = items[index].gross_selling_price_commercial_unit;
                                let pricePurchase = items[index].net_purchase_price_commercial_unit_after_discounts;
                                let quantity = items[index].quantity;

                                if (priceSelling == null) {
                                    priceSelling = 0;
                                }
                                if (pricePurchase == null) {
                                    pricePurchase = 0;
                                }
                                if (quantity == null) {
                                    quantity = 0;
                                }
                                sumOfSelling += parseFloat(priceSelling) * parseInt(quantity);
                                sumOfPurchase += parseFloat(pricePurchase) * parseInt(quantity);
                            }

                            const RKTBOOperationDetails = [
                                'Allegro Paczkomaty Inpost',
                                'DPD - Kurier opłaty dodatkowe',
                                'Inpost - dopłaty dodatkowe',
                                'Przesyłka DPD',
                                'UPS Operator - opłaty podstawowe',
                            ];
                            const PSIKOperationDetails = [
                                'Prowizja od sprzedaży',
                                'Jednostkowa opłata transakcyjna',
                                'Opłata za udpstępnienie metody płatnośći Allegro Pay',
                            ];
                            const PSWOperationDetails = [
                                'Prowizja od sprzedaży oferty wyróżnionej',
                            ];
                            const WACOperationDetails = [
                                'Wyrównanie w programie Allegro Ceny',
                            ];
                            const ZPOperationDetails = [
                                'Zwrot kosztów',
                            ];

                            computeSumDetails(row['items']);
                            computeExpenses(row.allegroGeneralExpenses);

                            function computeSumDetails(items) {
                                let sumOfSelling = 0;
                                let sumOfPurchase = 0;

                                for (let item of items) {
                                    sumOfSelling += getItemTotal(item.gross_selling_price_commercial_unit, item.quantity);
                                    sumOfPurchase += getItemTotal(item.net_purchase_price_commercial_unit_after_discounts, item.quantity);
                                }

                                return {
                                    sumOfSelling: sumOfSelling,
                                    sumOfPurchase: sumOfPurchase
                                };
                            }

                            function computeExpenses(expenses) {
                                for (let expense of expenses) {
                                    PSIK += checkDetail(expense, PSIKOperationDetails);
                                    PSW += checkDetail(expense, PSWOperationDetails);
                                    WAC += checkDetail(expense, WACOperationDetails);
                                    ZP += checkDetail(expense, ZPOperationDetails);
                                }
                            }

                            function getItemTotal(price, quantity) {
                                price = price || 0;
                                quantity = quantity || 0;
                                return parseFloat(price) * parseInt(quantity);
                            }

                            function checkDetail(expense, details) {
                                if (details.includes(expense.operation_type)) {
                                    return parseFloat(expense.debit === '0' ? expense.credit : expense.debit);
                                }

                                return 0;
                            }

                            const Z = (sumOfSelling + parseFloat(row.additional_cash_on_delivery_cost ?? 0) - (sumOfPurchase * 1.23)).toFixed(2);

                            const BZO = (parseInt(Z) - parseInt(RKTBO) + parseInt(PSIK) - parseInt(PSW) + parseInt(WAC) + parseFloat(ZP)).toFixed(2);

                            return `
                                <p><span title="Zysk">Z: ${Math.round(Z * 100) / 100}</p>
                                <p>RKTBO: ${Math.round(RKTBO * 100) / 100}</p>
                                <p>BT: ${(Math.round(row.shipment_price_for_client - RKTBO * 100) / 100)}</p>
                                <p>PSIK: ${Math.round(PSIK * 100) / 100}</p>
                                <p>PSW: ${Math.round(PSW * 100) / 100}</p>
                                <p>WAC: ${Math.round(WAC * 100) / 100}</p>
                                <p>ZP: ${Math.round(ZP * 100) / 100}</p>
                                <p>BZO: ${Math.round(BZO * 100) / 100}</p>
                                <p><a href="/admin/allegro-billing?order-id=${row.orderId}" class="btn btn-primary" target="_blank">pokaz na liście</a></p>
                            `;
                        }
                    },
                    {
                        data: 'weight',
                        name: 'weight',
                    },
                    {
                        data: 'values_data',
                        name: 'values_data',
                        render: function (data, type, row) {

                            return '<p><span title="Wartość Zamówienia">WO: ' + row['values_data']['sum_of_gross_values'] + '</p>\n\
                        <p><span title="Wartość Towaru">WT: ' + row['values_data']['products_value_gross'] + '</p>\n\
                        <p><span title="Koszt Transportu Dla Klienta">KT: ' + row['values_data']['shipment_price_for_client'] + '</p>\n\
                        <p><span title="Dodatkowy Koszt Pobrania">DKP: ' + row['values_data']['additional_cash_on_delivery_cost'] + '</p>\n\
                        <p><span title="Dodatkowy Koszt Obsługi">DKO: ' + row['values_data']['additional_service_cost'] + '</p>'
                        }
                    },
                    {
                        data: 'shipment_price_for_us',
                        name: 'shipment_price_for_us',
                    },
                    {
                        data: 'orderId',
                        name: 'sum_of_payments',
                        searchable: false,
                        render: function (data, type, row) {
                            let totalOfPayments = 0;
                            let totalOfDeclaredPayments = 0;
                            let bilans = 0;
                            let totalOfReturns = 0;
                            let text = '';
                            const settledDeclared = [];
                            const payments = row['payments'];
                            let returnedValue = 0;
                            let kwonPayments = 0;
                            let WPFZ = 0;

                            payments.forEach(payment => {
                                const {amount, declared_sum, status} = payment;
                                const parsedAmount = parseFloat(amount);
                                const parsedDeclaredAmount = parseFloat(declared_sum);

                                if (payment.operation_type === "Zwrot towaru") {
                                    returnedValue += parsedAmount;
                                }

                                if (payment.deleted_at !== null) {
                                    return;
                                }

                                if (payment.operation_type === '{{ OrderPaymentsEnum::KWON_STATUS }}') {
                                    kwonPayments += parsedAmount;
                                }

                                if (payment.operation_type === 'Wartość pobrania przez firmę zewnętrzną') {
                                    WPFZ += parsedDeclaredAmount;
                                    return;
                                }

                                if (parsedAmount < 0 && payment.operation_type !== "Zwrot towaru") {
                                    totalOfReturns -= parsedAmount ?? parsedDeclaredAmount;
                                } else if (parsedAmount && parsedAmount > 0 && payment.operation_type !== '{{ OrderPaymentsEnum::KWON_STATUS }}') {
                                    totalOfPayments += parsedAmount;
                                } else if (!parsedAmount && parsedDeclaredAmount > 0) {
                                    totalOfDeclaredPayments += status === 'Rozliczona deklarowana' ? 0 : parsedDeclaredAmount;

                                    if (status === 'Rozliczona deklarowana') {
                                        settledDeclared.push(parsedDeclaredAmount);
                                    }
                                }
                            });

                            bilans = totalOfPayments - totalOfReturns + totalOfDeclaredPayments;
                            let offerFinanceBilans = row['values_data']['sum_of_gross_values'] - bilans + returnedValue - WPFZ - kwonPayments;

                            text += `<p> Z: ${Math.round(totalOfPayments * 100) / 100} </p>`;
                            text += `<p> ZW: ${Math.round(totalOfReturns * 100) / 100} </p>`;
                            text += `<p> D: ${Math.round(totalOfDeclaredPayments * 100) / 100} </p>`;
                            text += `<p> ZT: ${Math.round(returnedValue * 100) / 100} </p>`;
                            text += `<p> WTON: ${Math.round(kwonPayments * 100) / 100} </p>`;
                            text += `<p> BIF: ${Math.round(bilans * 100) / 100} </p>`;
                            text += `<p> CBO: ${Math.round(offerFinanceBilans * 100) / 100} </p>`;
                            text += `<p> WPFZ: ${Math.round(WPFZ * 100) / 100} </p>`;

                            settledDeclared.forEach((amount) => {
                                text += `<p> RD: ${amount} </p>`;
                            });

                            return text;
                        }
                    },
                    {
                        data: 'id',
                        name: 'left_to_pay',
                        searchable: false,
                        render(data, type, row) {
                            let html = "";
                            let SBWF = 0;

                            row.invoiceValues.forEach((invoice) => {
                                SBWF += parseFloat(invoice.value);

                                html += `
                                    ${Math.round(invoice.value * 100) / 100} -
                                    ${invoice.invoice_number} -
                                    ${invoice.issue_date} -
                                    <a class="btn btn-danger" id="delete" href="orders/invoice-value-delete/${invoice.id}">Usuń</a>
                                    <br>
                                `;
                            });

                            return `
                                ${html} <br>
                                SBWF: ${Math.round(SBWF * 100) / 100} <br>
                            `;
                        }
                    },
                    {
                        data: 'transport_exchange_offers',
                        name: 'transport_exchange_offers',
                        searchable: false,
                        orderable: false,
                        render(data, option, row) {
                            let html = "";

                            if (!data.length) {
                                return html;
                            }

                            const generateTitleTooltip = (offer) => {
                                const {
                                    firm_name,
                                    nip,
                                    contact_person,
                                    phone_number,
                                    email,
                                    street,
                                    number,
                                    postal_code,
                                    city,
                                    comments,
                                    driver_first_name,
                                    driver_last_name,
                                    driver_phone_number,
                                    driver_document_number,
                                    driver_car_registration_number,
                                    driver_arrival_date,
                                    driver_approx_arrival_time,
                                } = offer;

                                return `Nr: ${firm_name} | NIP: ${nip} | Osoba kontaktowa: ${contact_person} | Telefon: ${phone_number} | Email: ${email} | Adres: ${street} ${number}, ${postal_code} ${city} | Uwagi: ${comments} ||| Kierowca: ${driver_first_name} ${driver_last_name} | ${driver_phone_number} | Nr dokumentu: ${driver_document_number} | Nr rej.: ${driver_car_registration_number} | Przybycie: ${driver_arrival_date} ${driver_approx_arrival_time}`;
                            };

                            data.forEach((spedition) => {
                                let chosenSpeditionClass = "";
                                if (spedition.chosen_spedition) {
                                    chosenSpeditionClass = "transport-exchange__spedition-chosen";
                                }

                                html += `<div class='transport-exchange ${chosenSpeditionClass}'>`;
                                html += `<div>Nr: ${spedition.id}</div>`;

                                if (spedition.chosen_spedition) {
                                    const title = generateTitleTooltip(spedition.chosen_spedition);
                                    html += `<div class="transport-exchange-offer" data-toggle="transport-exchange-tooltip" data-html="true" title="${title}">${spedition.chosen_spedition.firm_name.substr(0, 10)}</div>`;
                                } else if (spedition.spedition_offers.length) {
                                    spedition.spedition_offers.forEach((offer) => {
                                        const title = generateTitleTooltip(offer);
                                        html += `<div class="transport-exchange-offer" onclick="chooseExchangeOffer(${offer.id})" data-toggle="transport-exchange-tooltip" data-html="true" title="${title}">${offer.firm_name.substr(0, 10)}</div>`;
                                    });
                                }

                                html += "</div>";
                            });

                            return html;
                        }
                    },
                    {
                        data: null,
                        name: 'invoices',
                        render: function (data) {
                            let invoices = data.invoices;
                            let html = '';

                            if (invoices !== undefined) {
                                invoices.forEach((invoice) => {
                                    if (invoice.invoice_type !== 'buy') {
                                        return;
                                    }

                                    html += `<a target="_blank" href="/storage/invoices/${invoice.invoice_name}" style="margin-top: 5px;">Faktura</a>`;

                                    if (invoice.is_visible_for_client) {
                                        html += '<p class="invoice__visible">Widoczna</p>';
                                    } else {
                                        html += '<p class="invoice__invisible">Niewidoczna</p>';
                                    }

                                    html += `<a href="#" class="change__invoice--visibility" onclick="changeInvoiceVisibility(${invoice.id})">Zmień widoczność</a>`;
                                });

                                html += '<br />';
                                html += `<a href="#" class="remove__invoices" onclick="getInvoicesList(${data.orderId})">Usuń</a>`;
                            }

                            html += `<a href="{{rtrim(config('app.front_nuxt_url') ,'/')}}/magazyn/awizacja/0/0/${data.orderId}/wyslij-fakture">Dodaj</a>`;

                            return html;
                        }
                    },
                    {
                        data: null,
                        name: 'invoice_gross_sum',
                        render: function (data, type, row) {
                            let sumOfPurchase = 0;
                            let items = row['items'];

                            for (let index = 0; index < items.length; index++) {
                                let pricePurchase = items[index].net_purchase_price_commercial_unit_after_discounts;
                                let quantity = items[index].quantity;
                                if (pricePurchase == null) {
                                    pricePurchase = 0;
                                }
                                if (quantity == null) {
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
                            return totalItemsCost + transportCost;
                            html += 'Suma: <br /><b>' + (totalItemsCost + transportCost).toFixed(2) + '<b/>'
                            return html;
                        }
                    },
                    {
                        data: 'orderId',
                        name: 'icons',
                        render: function () {
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
                        render: function (packages) {
                            return `
                                <span style="margin-top: 5px;">
                                    ${packages.reduce((prev, next) => (prev + parseFloat(next.real_cost_for_company ?? 0)), 0)}
                                </span>
                            `;
                        }
                    },
                    {
                        data: 'shipment_price_for_client',
                        name: 'difference',
                        render: function (data, type, row) {
                            let priceForClient = row['shipment_price_for_client'];
                            let priceForUs = row['shipment_price_for_us'];

                            if (priceForClient == null) {
                                priceForClient = 0;
                            }
                            if (priceForUs == null) {
                                priceForUs = 0;
                            }

                            let price = (parseFloat(priceForClient) - parseFloat(priceForUs)).toFixed(2);

                            return '<span style="margin-top: 5px;">' + price + '</span>';
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
                        data: null,
                        name: 'sello_payment',
                        render: (data, type, row) => (row.allegro_payment_id ?? data.sello_payment)
                    },
                    {
                        data: 'allegro_deposit_value',
                        name: 'allegro_deposit_value',
                        searchable: false,
                    },
                    {
                        data: 'allegro_operation_date',
                        name: 'allegro_operation_date',
                        searchable: false,
                    },
                    {
                        data: 'allegro_additional_service',
                        name: 'allegro_additional_service',
                        searchable: false,
                    },
                    {
                        data: 'payment_channel',
                        name: 'payment_channel',
                        searchable: false,
                    },
                    {
                        data: 'remainder_date',
                        name: 'remainder_date',
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'invoices',
                        render: function (data) {
                            let invoices = data.invoices
                            let html = ''
                            if (invoices !== undefined) {
                                invoices.forEach(function (invoice) {
                                    if (invoice.invoice_type !== 'sell') {
                                        return;
                                    }
                                    html += '<a target="_blank" href="/storage/invoices/' + invoice.invoice_name + '" style="margin-top: 5px;">Faktura</a>';
                                });
                                let jsonInvoices = JSON.stringify(invoices);
                                html += '<br />'
                                html += '<a href="#" class="remove__invoices"' + 'onclick="getInvoicesList(' + data.orderId + ')">Usuń</a>'

                            }
                            html += '<a href="#" onclick="addNewSellInvoice(' + data.orderId + ')" style="margin-top: 5px;">Dodaj</a>';

                            return html;
                        }
                    },
                    {
                        data: 'sello_form',
                        name: 'sello_form',
                        searchable: false,
                    },
                    {
                        data: 'allegro_commission',
                        name: 'allegro_commission',
                        searchable: false,
                    },
                    {
                        data: 'id',
                        name: 'search_on_lp',
                        searchable: false,
                        orderable: false,
                        visible: false
                    },
                ],
            })
        };

        window.table = table = datatable();

        window.table.on('draw', function () {

            $('.order-id-checkbox').on('click', e => {
                if (e.shiftKey && lastChecked) {
                    let start = checkboxes.index(e.target);
                    let end = checkboxes.index(lastChecked);
                    checkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).each((index, item) => {
                        item.checked = lastChecked.checked;
                    });
                }
                lastChecked = e.target;

            });
            $('#selectAllOrders').on('click', e => {
                checkboxes.each((index, item) => {
                    item.checked = e.target.checked
                });
            })
            var checkboxes = $('.order-id-checkbox');
            var lastChecked = null;
        });
        @foreach($visibilities as $key =>$row)
        var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function (x) {
            // if (typeof table.column(x+':name').index() === "number")
            return table.column(x + ':name').index();
        });
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
            return el != null;
        });

        var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function (x) {
            // if (typeof table.column(x+':name').index() === "number")
            return table.column(x + ':name').index();
        });
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
            return el != null;
        });
        table.button().add({{1+$key}}, {
            extend: 'colvisGroup',
            text: '{{$row->display_name}}',
            show: {{'show'.$row->name}},
            hide: {{'hidden'.$row->name}}
        });
        @endforeach
        let localDatatables = localStorage.getItem('DataTables_dataTable_/admin/orders');
        if (localDatatables != null) {
            let objDatatables = JSON.parse(localDatatables);

            $('#dataTable thead tr th input, #dataTable thead tr th select').each(function (i) {
                let colName = $(this)[0].id;
                if (colName != "") {
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

            $('.dt-button', this).on('click', function () {
                table
                    .order([7, 'desc'])
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

                if (table.column(colSelector).search() !== this.value) {
                    if (this.value == '') {
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

        $('.positive-balance').click(function (e) {
            table
                .column('packages_sent:name')
                .search('plus ' + $('.delivery_cost_balance')[0].value)
                .draw();
        });

        $('.negative-balance').click(function (e) {
            table
                .column('packages_sent:name')
                .search('minus ' + $('.delivery_cost_balance')[0].value)
                .draw();
        });

        $('.clear-balance').click(function (e) {
            $('.delivery_cost_balance')[0].value = '';
            table
                .column('packages_sent:name')
                .search('')
                .draw();
        });

        $("#columnSearch-packages_sent")
            .click(function (e) {
                e.stopPropagation();
            })
            .change(function () {
                if (table.column("packages_sent:name").search() !== this.value) {
                    if (this.value == '') {
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
                if (table.column("packages_not_sent:name").search() !== this.value) {
                    if (this.value == '') {
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
        $('#orderFilter').change(function () {
            if (this.value == 'ALL') {
                table
                    .search('')
                    .columns('statusName:name').search('')
                    .draw();
                localStorage.setItem("filter", "ALL");
            } else if (this.value == 'WITHOUT_REALIZATION') {
                table
                    .columns('statusName:name')
                    .search('bez realizacji', true, false)
                    .draw();
                localStorage.setItem("filter", "WITHOUT_REALIZATION");
            } else if (this.value == 'COMPLETED') {
                table
                    .columns('statusName:name')
                    .search('oferta zakonczona', true, false)
                    .draw();
                localStorage.setItem("filter", "COMPLETED");
            } else {
                table
                    .columns('statusName:name')
                    .search('przyjete zapytanie ofertowe|w trakcie analizowania przez konsultanta|mozliwa do realizacji|mozliwa do realizacji kominy|w trakcie realizacji|oferta oczekujaca|przekazane konsultantowi do obslugi', true, false)
                    .draw();
                localStorage.setItem("filter", "PENDING");
            }

        });

        $('#dataTable').on('column-visibility.dt', function (e, settings, column, state) {
            if (state == true) {
                $("#columnSearch" + column).parent().show();
            } else {
                $("#columnSearch" + column).parent().hide();
            }
        });
        $('#columnSearch-clientPhone').on('input', function () {
            var str = $('#columnSearch-clientPhone').val();
            var replaced = str.replace(/-|\s/g, '');
            $('#columnSearch-clientPhone').val(replaced);
            filterByPhone(replaced);
        });
        $('#columnSearch-clientPhone').click(function () {
            clearFilters(false);
        });
        $('#columnSearch-orderId').click(function () {
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

        $('#showTable').click(function () {
            if ($('#labelTable').hasClass("hidden")) {
                $('#labelTable').removeClass("hidden");
                $('#showTable').html('Schowaj Tabelkę z Etykietami Pracownika');
            } else {
                $('#labelTable').addClass("hidden");
                $('#showTable').html('Pokaż Tabelkę z Etykietami Pracownika');
            }
        });

        function removeTimedLabel(orderId, labelId) {
            $.ajax({
                url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
                method: "POST",
                data: {time: $('#time_label_removal').val()}
            }).done(function (res) {
                table.ajax.reload(null, false);
            });
        }

        function removeLabel(orderId, labelId, manualLabelSelectionToAdd, addedType, timed = null, skipTimed = true) {
            if (timed == '1' && skipTimed) {
                $('#timed_label_removal').modal('show');
                $('#time_label_removal_ok').on('click', () => {
                    removeTimedLabel(orderId, labelId)
                })
                $('#time_label_removal_cancel').on('click', () => {
                    removeLabel(orderId, labelId, manualLabelSelectionToAdd, addedType, timed, false)
                })
                return;
            }

            if (labelId == '49') {
                let checkQuantity = checkOrderQuantityInStock(orderId);

                if (checkQuantity == 1) {
                    $('#quantity-in-stock-list').modal('show');
                    return;
                }
            }

            let removeLabelRequest = function () {
                $.ajax({
                    url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
                    method: "POST"
                }).done(function (res) {
                    $('#position__errors').empty();
                    $('#quantity__errors').empty();
                    $('#exists__errors').empty();
                    res.forEach((error) => {
                        if (error.error == '{{ ProductStockError::POSITION }}') {
                            $('#position__errors').append(`<h5>{{ __('product_stocks.form.missing_position_for_product') }} <span class="modal__product">${error.productName}</span>. {{ __('product_stocks.form.go_to_create_position') }} <a href="/admin/products/stocks/${error.product}/positions/create" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
                        }
                        if (error.error == '{{ ProductStockError::QUANTITY }}') {
                            $('#quantity__errors').append(`<h5>{{ __('product_stocks.form.missing_product_quantity') }} <span class="modal__position">${error.position.position_quantity}</span>. {{ __('product_stocks.form.go_to_move_between_positions') }}<a href="/admin/products/stocks/${error.product}/edit?tab=positions" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
                        }
                        if (error.error == '{{ ProductStockError::EXISTS }}') {
                            $('#exists__errors').append(`<h5>{{ __('product_stocks.form.for_product') }} <span class="modal__product">${error.productName}</span> {{ __('product_stocks.form.stock_already_performed') }} {{ __('product_stocks.form.go_to_order') }} <a href="/admin/orders/${error.order_id}/edit" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
                        }
                    })
                    $('#stock_modal').modal('show');
                });
            };

            function removeMultiLabel(orderId, labelId, ids, delivery_warehouse = null) {
                $.ajax({
                    url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
                    method: "POST",
                    data: {
                        labelsToAddIds: ids,
                        manuallyChosen: true
                    }
                }).done(function () {
                    if ($.inArray('47', ids) != -1) {
                        $('input[name="order_id"]').val(orderId);
                        $('#selectWarehouse').val(16);
                        $('#warehouseSelect').attr('selected', true);
                        $('#selectWarehouse').click();
                        addingTaskToPlanner(orderId, delivery_warehouse);
                        refreshDtOrReload();
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
                    url: '/api/get-labels-scheduler-await/{{ \Illuminate\Support\Facades\Auth::user()->id }}'
                }).done(function (res) {
                    if (res.length) {
                        location.reload();
                    } else {
                        table.ajax.reload(null, false);
                    }
                });
            };

            function addingTaskToPlanner(orderId, delivery_warehouse) {
                $.ajax({
                    method: 'post',
                    url: '{{route('planning.tasks.addingTaskToPlanner')}}',
                    dataType: 'json',
                    data: {
                        order_id: orderId,
                        delivery_warehouse: delivery_warehouse
                    },
                }).done(function (data) {
                    if (data.status === 'ERROR') {
                        let modal = $('#add-withdraw-task');
                        let input_delivery_warehouse = modal.find("#add-withdraw-task-delivery_warehouse");
                        let input_order_id = modal.find("#add-withdraw-task-order_id");
                        input_delivery_warehouse.val(data.delivery_warehouse);
                        input_order_id.val(data.id);
                        let order_ids = [data.id];
                        let clickCount = 0;
                        modal.modal();
                        $('#withdrawTaskButton').on('click', () => {
                            if (clickCount > 0) {
                                return false;
                            } else {
                                $.ajax({
                                    url: "/admin/orders/label-addition/45",
                                    method: "POST",
                                    data: {
                                        orderIds: order_ids
                                    }
                                }).done(function () {
                                    modal.modal('hide');
                                    table.ajax.reload(null, false);
                                    return false;
                                });

                                clickCount++;
                            }
                        })
                    } else {
                        window.open('/admin/planning/timetable', '_blank');
                    }
                });
            }

            const showSelectWarehouseTemplate = (modal, orderId) => {
                const row = $('#id-' + orderId);
                const warehouseEl = row.find('.warehouse-symbol');
                const warehouse = warehouseEl.text();

                $('.warehouse-template').remove();

                let warehouseTemplate = `
                <div class="error" style="display: none">
                    <div class="alert alert-danger" role="alert">
                    </div>
                </div>
                <div class="warehouse-template">
                <p>Magazyn nie został przypisany, przypisz magazyn przed wysłaniem</p>
                <div class="form-group" style="width: 15%; padding: 5px;">
                    <label for="delivery_warehouse2">Magazyn obsługujący</label>
                    <input type="text" class="form-control" id="delivery_warehouse2" name="delivery_warehouse2" value="${warehouse}">
                </div><br>
                </div>`;

                const modalBody = modal.find('.modal-body');
                const modalOk = modal.find('#labels_to_add_after_removal_modal_ok');

                if (!warehouse) modalOk.attr('disabled', 'disabled');

                modalBody.prepend(warehouseTemplate);
                $("#delivery_warehouse2").autocomplete({
                    source: available,
                    classes: {
                        'ui-autocomplete': 'z-index-max',
                    },
                    select: async (e, ui) => {
                        modalOk.attr('disabled', 'disabled');
                        await $.ajax({
                            url: "/admin/orders/set-warehouse/" + orderId,
                            method: "POST",
                            data: {
                                warehouse: ui.item.value
                            }
                        }).done(res => {
                            if (res) {
                                warehouseEl.text(ui.item.value);
                                modalOk.removeAttr('disabled');
                            }
                        });
                    },
                });
            }

            if (manualLabelSelectionToAdd) {
                $.ajax({
                    url: "/admin/labels/" + labelId + "/associated-labels-to-add-after-removal"
                }).done(async function (data) {

                    let modal = $('#manual_label_selection_to_add_modal');
                    let input = modal.find("#labels_to_add_after_removal_modal");
                    input.empty();
                    data.forEach(function (item) {
                        input.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    $('#manual_label_selection_to_add_modal').modal('show');

                    if (labelId == 45) showSelectWarehouseTemplate(modal, orderId);

                    modal.find("#labels_to_add_after_removal_modal_ok").off().on('click', function () {
                        let ids = [];
                        ids.push(input.val());
                        if (modal.find("#delivery_warehouse2").val() == '') {
                            modal.find(".error").show();
                            modal.find(".error .alert").text('Wybierz magazyn');
                            return false;
                        }
                        if (!ids) {
                            modal.find(".error").show();
                            modal.find(".error .alert").text('Wybierz przynajmniej jeden etykietę');
                            return false;
                        }
                        delivery_warehouse = modal.find("#delivery_warehouse2").val();
                        removeMultiLabel(orderId, labelId, ids, delivery_warehouse);
                        modal.modal('hide');
                    });
                });
            } else {
                let payDateLabelId = '{{ env('MIX_LABEL_ENTER_PAYMENT_DATE_ID') }}'
                if (labelId == payDateLabelId) {
                    let modalSetTime = $('#set_time');
                    modalSetTime.modal('show');
                    $('#set_time').on('shown.bs.modal', function () {
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
                } else if (addedType == "{{ Label::CHAT_TYPE }}") {
                    var url = '{{ route("chat.index", ["all" => 1, "id" => ":id"]) }}';
                    url = url.replace(':id', orderId);
                    window.location.href = url
                    return
                } else if (addedType == "{{ Label::BONUS_TYPE }}") {
                    let url = '{{ route("bonus.order-chat", ['id' => ":id"]) }}';
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

        function printAll() {
            $.post("{{route('orders.printAll')}}", table.ajax.params())
                .done((data) => {
                    if (!data.error) {
                        window.open(data);
                    } else {
                        alert('Trwa przygotowywanie listy')
                    }
                });
        }

        function showProductsStockChangesModal() {
            $('#products-stocks-changes-modal').modal('show');
        }

        function findPage() {
            let id = $('#searchById').val()
            let url = "{{route('orders.findPage', ['id' => '%%'])}}"
            if (id.length !== 0) {
                $.post(url.replace('%%', id), table.ajax.params())
                    .done((data) => {
                        window.table.page(Math.floor(data)).draw('page')
                    });
            }
        }

        function findSite() {
            let id = $('#searchBySite').val();
            window.table.page(parseInt(id) - 1).draw('page');
        }

        function findByDates() {
            let dateColumn = $('#columnSearch-choose_date').val();
            let dateFrom = $('#dates_from').val();
            let dateTo = $('#dates_to').val();
            let ajaxParams = {};

            ajaxParams['dateFrom'] = dateFrom;
            ajaxParams['dateTo'] = dateTo;
            ajaxParams['dateColumn'] = dateColumn;

            if (dateFrom == dateTo) ajaxParams['same'] = true;

            window.table.destroy();
            window.table = table = datatable(ajaxParams);
        }

        let chosenLabel = '';
        let orderIds = [];

        function addLabel() {
            chosenLabel = $("#choosen-label");
            let timed = chosenLabel[0].selectedOptions[0].dataset.timed;

            if (chosenLabel.val() === "") {
                alert("Nie wybrano etykiety");
                return;
            }

            let selectedOrders = $(".order-id-checkbox:checked");
            if (selectedOrders.length == 0) {
                alert("Nie wybrano żadnego zamówienia");
                return;
            }

            orderIds = [];
            $.each(selectedOrders, function (index, order) {
                orderIds.push($(order).val());
            });

            if (timed == 1) {
                $('#timed_label').modal('show');
            } else {
                addLabelAjax();
            }
        }

        $('body').on('click', '#time_label_ok', () => {
            addLabelAjax(true);
        });

        function addLabelAjax(timed = false) {
            let data = '';
            let url = "{{ route('orders.label-addition', ['labelId' => ':id'])}}"
            url = url.replace(':id', chosenLabel.val());
            let schedulerUrl = "{{ route('api.labels.get-labels-scheduler-await', ['userId' => ':id']) }}"
            schedulerUrl = schedulerUrl.replace(':id', {{ \Illuminate\Support\Facades\Auth::user()->id }});
            if (timed == true) {
                data = {orderIds, time: $('#time_label').val()};
            } else {
                data = {orderIds}
            }
            $.ajax({
                url: url,
                method: "POST",
                data: data
            }).done(function () {
                $.ajax({
                    url: schedulerUrl
                }).done(function (res) {
                    if (res.length) {
                        location.reload();
                    } else {
                        table.ajax.reload(null, false);
                    }
                });
            });
        }

        function moveData(id) {
            if ($('#moveButton-' + id).hasClass('btn-warning')) {
                $('#moveButton-' + id).removeClass('btn-warning').addClass('btn-dark');
                $('.btn-warning').attr('disabled', true);
                $('.btn-move').removeClass('hidden');
            } else if ($('#moveButton-' + id).hasClass('btn-dark')) {
                $('#moveButton-' + id).removeClass('btn-dark').addClass('btn-warning');
                $('.btn-warning').attr('disabled', false);
                $('.btn-move').addClass('hidden');
            }
        }

        function getInvoicesList(id) {
            $.ajax({
                url: '/admin/orders/' + id + '/invoices',
            }).done(function (data) {
                $('#order_invoices_delete').modal('show');
                if (data === null) {
                    return;
                }
                $('#invoice__list').remove();
                let parent = document.getElementById("invoice__container");
                let invoiceSelect = document.createElement("SELECT");
                invoiceSelect.id = "invoice__list";
                parent.appendChild(invoiceSelect);
                data.forEach((invoice) => {
                    let option = document.createElement("option");
                    option.value = invoice.id;
                    option.text = invoice.invoice_name;
                    invoiceSelect.appendChild(option);
                })
            })
        }

        function changeInvoiceVisibility(invoiceId) {
            $.ajax({
                type: 'PATCH',
                url: laroute.route('orders.changeInvoiceVisibility', {id: invoiceId})
            }).done((data) => {
                document.getElementById('invoice_name').innerText = data.invoice_name;
                $('#order_invoices_change_visibility').modal('show');
            })
        }

        function addNewFile(id) {
            let url = "{{ route('orders.fileAdd', ['id' => '%%']) }}"
            $('#addNewFileToOrder').attr('action', url.replace('%%', id));
            $('#add-new-file').modal('show');
        }

        function addNewSellInvoice(id) {
            $('#new-sell-invoice-order-id').val(id);
            $('#add-new-sell-invoice').modal('show');
        }

        function getFilesList(id) {
            let url = "{{ route('orders.getFiles', ['id' => '%%']) }}"
            $.ajax({
                url: url.replace('%%', id)
            }).done(function (data) {
                $('#order_files_delete').modal('show');
                if (data === null) {
                    return;
                }
                $('#files__list').remove();
                let parent = document.getElementById("files__container");
                let filesSelect = document.createElement("SELECT");
                filesSelect.id = "files__list";
                parent.appendChild(filesSelect);
                data.forEach((file) => {
                    let option = document.createElement("option");
                    option.value = file.id;
                    option.text = file.file_name;
                    filesSelect.appendChild(option);
                })
            })
        }

        function moveDataAjax(id) {
            var idToSend = id;
            var buttonId = $('.btn-dark').attr('id');
            var idToGet;
            var res = buttonId.split("-")
            idToGet = res[1];
            if (idToGet != idToSend) {
                $('#order_id_get').text(idToGet);
                $('#order_id_send').text(idToSend);
                $('#order_move_data').modal('show');
            } else {
                $('#order_move_data_error_select').modal('show');
            }
        }

        $('#remove-selected-invoice').on('click', () => {
            let invoiceId = $('#invoice__list option:selected').val();
            $.ajax({
                url: '/admin/invoice/' + invoiceId + '/delete'
            }).done(function (data) {
                $('#invoice_delete_success').modal('show');

                $('#invoice-delete-ok').on('click', function () {
                    location.reload();
                });
            })
        })

        $('#remove-selected-file').on('click', () => {
            let fileId = $('#files__list option:selected').val();
            let url = "{{ route('orders.fileDelete', ['file_id' => '%%']) }}"
            $.ajax({
                url: url.replace('%%', fileId)
            }).done(function (data) {
                $('#invoice_delete_success').modal('show');

                $('#invoice-delete-ok').on('click', function () {
                    location.reload();
                });
            })
        })

        $('#move-data-ok').on('click', function () {
            var idToGet = $('#order_id_get').text();
            var idToSend = $('#order_id_send').text();
            $.ajax({
                url: '/admin/orders/' + idToGet + '/data/' + idToSend + '/move',
            }).done(function (data) {
                $('#order_move_data_success').modal('show');

                $('#order_move_data_ok').on('click', function () {
                    table.ajax.reload(null, false);
                });
            }).fail(function () {
                $('#order_move_data_error').modal('show');
                $('#order_move_data_ok_error').on('click', function () {
                    table.ajax.reload(null, false);
                });
            });
        });


        function gerateSpeditionExchangeLink() {
            let orders = $("#spedition-exchange-selected-items").val();

            $.ajax({
                url: "/api/spedition-exchange/generate-link",
                method: "POST",
                data: {data: orders}
            }).done(function (data) {
                $(".spedition-exchange-generated-link").html(data);
            });
        }

        function updateSpeditionExchangeSelectedItem(id, type) {
            let shouldAdd = true;
            let hidden = $("#spedition-exchange-selected-items");
            let val = JSON.parse(hidden.val());

            val.forEach(function (value, index) {
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
            }).done(function (data) {
                table.ajax.reload(null, false);
            });
        }

        var renderCalendar = function (minTime = "07:00:00", maxTime = "20:00:00") {
            $('#calendar').empty();
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid', 'resourceTimeline'],
                now: '{{new Carbon\Carbon()}}',
                editable: true,
                aspectRatio: 1.8,
                scrollTime: '7:00',
                slotDuration: '0:05',
                timeZone: 'UTC',
                minTime: minTime,
                maxTime: maxTime,
                locale: 'PL',
                titleFormat: {year: 'numeric', month: 'long', day: '2-digit'},
                buttonText: {
                    today: 'Dzisiaj',
                    month: 'Miesiąc',
                    week: 'Tydzień',
                    day: 'Dzień',
                    days: 'Lista',
                    resourceTimelineThreeDays: '3 Dni'
                },
                slotLabelFormat: [
                    {day: '2-digit', month: 'long', year: 'numeric'},
                    {weekday: 'long'},
                    {hour: '2-digit', minute: '2-digit'}
                ],
                header: {
                    left: 'promptResource today prev,next',
                    center: 'title changeTimeLine',
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
                    },
                    changeTimeLine: {
                        text: 'Zmień zakres czasu',
                        click: () => renderCalendar("06:00:00", "23:00:00")
                    }
                },
                dateClick: function (info) {
                    if (info.view.type !== 'timeGridWeek' && info.view.type !== 'dayGridMonth') {
                        $('#addNewTask').modal();
                        const startDate = new Date(info.dateStr);
                        let startMinutes = startDate.getUTCMinutes();
                        if (startMinutes < 10) {
                            startMinutes = '0' + startMinutes;
                        }

                        let dateTime = startDate.getUTCFullYear() + '-' + ('0' + (startDate.getUTCMonth() + 1)).slice(-2) + '-' + startDate.getUTCDate() + ' ' + startDate.getUTCHours() + ':' + startMinutes;
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
                        html += '<p>Dodaj czas zakończenia:</p>'
                        html += '<button class="add-end-time" value="5">+5</button>';
                        html += '<button class="add-end-time" value="10">+10</button>';
                        html += '<button class="add-end-time" value="15">+15</button>';
                        html += '<button class="add-end-time" value="20">+20</button>';
                        html += '<button class="add-end-time" value="25">+25</button>';
                        html += '<button class="add-end-time" value="30">+30</button>';
                        html += '<button class="add-end-time" value="40">+40</button>';
                        html += '<button class="add-end-time" value="50">+50</button>';
                        html += '<button class="add-end-time" value="60">+60</button>';
                        html += '<button class="add-end-time" value="70">+70</button>';
                        html += '<button class="add-end-time" value="80">+80</button>';
                        html += '<button class="add-end-time" value="90">+90</button>';
                        html += '<br />';
                        html += '<label for="end">Godzina zakończenia</label>';
                        html += '<input type="text" required name="end" id="end" class="time-to-finish-task form-control default-date-time-picker-now">';
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
                        html += '<textarea disabled rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice" class="form-control"></textarea>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="warehouse_value">Koszt obsługi magazynu</label>';
                        html += '<input type="number" name="warehouse_value" id="warehouse_value" class="form-control">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="warehouse_notice">Opis obsługi magazynu</label>';
                        html += '<textarea disabled rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice" class="form-control"></textarea>';
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
                        $('.add-end-time').click(event => {
                            event.preventDefault();
                            let start = new Date($("#start_new").val());
                            let end = new Date(start.getTime() + event.target.value * 60000);
                            end.setHours(end.getHours() + (end.getTimezoneOffset() * -1) / 60);
                            let startMinutes = end.getUTCMinutes();
                            if (startMinutes < 10) {
                                startMinutes = '0' + startMinutes;
                            }
                            let dateTime = end.getUTCFullYear() + '-' + ('0' + (end.getUTCMonth() + 1)).slice(-2) + '-' + end.getUTCDate() + ' ' + end.getUTCHours() + ':' + startMinutes;
                            $(".time-to-finish-task").val(dateTime);
                        })
                        $('#name_new').val($('input[name="order_id"]').val() + ' - ' + startDate.getUTCDate() + '-' + ('0' + (startDate.getUTCMonth() + 1)).slice(-2) + ' - ' + $('#warehouse_value').val());
                        $('#name_new').change(function () {
                            var dateObj = new Date($('#start_new').val());
                            var month = ('0' + (dateObj.getUTCMonth() + 1)).slice(-2);
                            var day = dateObj.getUTCDate();
                            $('#name_new').val($('input[name="order_id"]').val() + ' - ' + day + '-' + month + ' - ' + $('#warehouse_value').val());
                        });
                        $(document).on('focusout', '.default-date-time-picker-now', function () {
                            var dateObj = new Date($('#start_new').val());
                            var month = ('0' + (dateObj.getUTCMonth() + 1)).slice(-2);
                            var day = dateObj.getUTCDate();
                            $('#name_new').val($('input[name="order_id"]').val() + ' - ' + day + '-' + month + ' - ' + $('#warehouse_value').val());
                        });
                        $('#warehouse_value').change(function () {
                            var dateObj = new Date($('#start_new').val());
                            var month = ('0' + (dateObj.getUTCMonth() + 1)).slice(-2);
                            var day = dateObj.getUTCDate();
                            $('#name_new').val($('input[name="order_id"]').val() + ' - ' + day + '-' + month + ' - ' + $('#warehouse_value').val());
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
                                    let firstDate = new Date(value.start);
                                    let startMinutes = firstDate.getUTCMinutes();
                                    if (startMinutes < 10) {
                                        startMinutes = '0' + startMinutes;
                                    }
                                    let dateTime = firstDate.getUTCFullYear() + '-' + ('0' + (firstDate.getUTCMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getUTCHours() + ':' + startMinutes;
                                    let newDate = new Date(value.end);
                                    let endDate = new Date(newDate.setUTCHours(newDate.getUTCHours() - 1));
                                    let minutes = endDate.getUTCMinutes();
                                    if (minutes < 10) {
                                        minutes = '0' + minutes;
                                    }
                                    let dateTimeEnd = endDate.getUTCFullYear() + '-' + ('0' + (endDate.getUTCMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getUTCHours() + ':' + minutes;
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
                    $(info.el).attr('title', info.event.extendedProps.text);
                },
                eventDrop: function (info) {
                    let firstDate = new Date(info.event.start);
                    let startMinutes = firstDate.getUTCMinutes();
                    if (startMinutes < 10) {
                        startMinutes = '0' + startMinutes;
                    }
                    let dateTime = firstDate.getUTCFullYear() + '-' + ('0' + (firstDate.getUTCMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getUTCHours() + ':' + startMinutes;
                    let newDate = new Date(info.event.end);
                    let endDate = new Date(newDate.setUTCHours(newDate.getUTCHours() - 1));
                    let minutes = endDate.getUTCMinutes();
                    if (minutes < 10) {
                        minutes = '0' + minutes;
                    }
                    let dateTimeEnd = endDate.getUTCFullYear() + '-' + ('0' + (endDate.getUTCMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getUTCHours() + ':' + minutes;
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
                    let firstDate = new Date(info.event.start);
                    let startMinutes = firstDate.getUTCMinutes();
                    if (startMinutes < 10) {
                        startMinutes = '0' + startMinutes;
                    }
                    let dateTime = firstDate.getUTCFullYear() + '-' + ('0' + (firstDate.getUTCMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getUTCHours() + ':' + startMinutes;
                    let newDate = new Date(info.event.end);
                    let endDate = new Date(newDate.setUTCHours(newDate.getUTCHours() - 1));
                    let minutes = endDate.getUTCMinutes();
                    if (minutes < 10) {
                        minutes = '0' + minutes;
                    }
                    let dateTimeEnd = endDate.getUTCFullYear() + '-' + ('0' + (endDate.getUTCMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getUTCHours() + ':' + minutes;
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
                                if (consultant_notice == null) {
                                    consultant_notice = '';
                                }
                                if (warehouse_notice == null) {
                                    warehouse_notice = '';
                                }
                            }
                            html += '<div class="form-group">';
                            html += '<label for="consultant_value">Koszt obsługi konsultanta</label>';
                            html += '<input type="number" name="consultant_value" id="consultant_value" class="form-control" value="' + consultant_value + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="consultant_notice">Opis obsługi konsultanta</label>';
                            html += '<textarea disabled rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice" class="form-control">' + consultant_notice + '</textarea>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="warehouse_value">Koszt obsługi magazynu</label>';
                            html += '<input type="number" name="warehouse_value" id="warehouse_value" class="form-control" value="' + warehouse_value + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="warehouse_notice">Opis obsługi magazynu</label>';
                            html += '<textarea disabled rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice" class="form-control">' + warehouse_notice + '</textarea>';
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
        }

        $("#check-finish-task-refresh").hide();
        $("#check-finish-task").click(function () {
            task = $('#select-task-for-finish').val();
            if (task) {
                var checkQuantity = checkQuantityInStock(task);
                if (checkQuantity == 1) {
                    $('#finish-task-form').hide();
                    $("#check-finish-task").hide();
                    $("#check-finish-task-refresh").show();
                    return false;
                } else {
                    $("#error-finish-task-form").html('');
                    return true;
                }
            }
        });
        $("#check-finish-task-refresh").click(function () {
            task = $('#select-task-for-finish').val();
            var checkQuantity = checkQuantityInStock(task);
            if (checkQuantity == 1) {
                $('#finish-task-form').hide();
            } else {
                $("#error-finish-task-form").html('');
                $("#check-finish-task").show();
                $("#check-finish-task-refresh").hide();
            }
            return false;
        });

        function checkQuantityInStock(task) {
            html = '';
            let status = 0;
            $.ajax({
                type: "GET",
                url: '/admin/planning/tasks/' + task + '/checkQuantityInStock',
                async: false
            }).done(function (data) {
                if (data.status == 200) {
                    if (Object.keys(data.data).length > 0) {
                        status = 1;
                    }
                    $.each(data.data, function (index, value) {

                        html += '<h3>oferta ' + index + '</h3>';
                        html += '<table class="table">';
                        html += '<tr class="appendRow">';
                        html += '<td style="width: 200px;">Nazwa</td>';
                        html += '<td style="width: 100px;">Symbol</td>';
                        html += '<td style="width: 50px;">Ilość potrzebna</td>';
                        html += '<td style="width: 50px;">Na magazynie/Ilość na pozycji</td>';
                        html += '<td>#</td>';
                        html += '</tr>';
                        $.each(value, function (index, value) {
                            html += '<tr class="appendRow">';
                            html += '<td>' + value.product_name + '</td>';
                            html += '<td>' + value.product_symbol + '</td>';
                            html += '<td>' + value.quantity + '</td>';
                            html += '<td>' + value.stock_quantity + '/' + value.first_position_quantity + '</td>';
                            html += '<td><a href="/admin/products/stocks/' + value.product_stock_id + '/edit" target="_blank">Przenieś</a></td>';
                            html += '</tr>';
                        });
                        html += '</table>';
                    });
                    $('#error-finish-task-form').html(html);
                } else {
                    status = 1;
                }
            }).fail(function () {
                status = 1;
            });

            return status;
        }

        function checkOrderQuantityInStock(orderId) {
            let html = '';
            let status = 0;
            $.ajax({
                type: 'GET',
                url: `/admin/planning/tasks/${orderId}/checkOrderQuantityInStock`,
                async: false
            }).done(function (data) {
                if (data.status !== 200) {
                    return 1;
                }
                if (Object.keys(data.data).length > 0) {
                    status = 1;
                }
                $.each(data.data, function (index, value) {
                    html += `
                    <h3>oferta ${index}</h3>
                    <table class="table">
                            <tr class="appendRow">
                            <td style="width: 200px;">Nazwa</td>
                            <td style="width: 100px;">Symbol</td>
                            <td style="width: 50px;">Ilość potrzebna</td>
                            <td style="width: 50px;">Na magazynie/Ilość na pozycji</td>
                            <td>#</td>
                        </tr>`;
                    $.each(value, function (index, value) {
                        html += `
                        <tr class="appendRow">
                            <td>${value.product_name}</td>
                            <td>${value.product_symbol}</td>
                            <td>${value.quantity}</td>
                            <td>${value.stock_quantity}/${value.first_position_quantity}</td>
                            <td><a href="/admin/products/stocks/${value.product_stock_id}/edit" target="_blank">Przenieś</a></td>
                        </tr>`;
                    });
                    html += '</table>';
                });
                $('#quantity-in-stock-list .error-finish-task-form').html(html);
            }).fail(function () {
                status = 1;
            });

            return status;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{{ URL::asset('js/helpers/render-calendar.js') }}"></script>
    <script>
        const setDeleteEventListeners = () => {
            const deleteLinks = document.querySelectorAll('#delete');
            deleteLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');

                    swal.fire({
                        title: 'Jesteś pewien usuwania?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'OK',
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = href;
                        }
                    });
                });
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            localStorage.removeItem('differenceMode');
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
            if (idFromUrl !== undefined) {
                if (planning !== undefined) {
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
        $('#searchLP').change(function () {
            table
                .columns('search_on_lp:name')
                .search($('#searchLP').val())
                .draw();
        });
        $('#searchOrderValue').change(function () {
            table
                .columns('sum_of_gross_values:name')
                .search($('#searchOrderValue').val())
                .draw();
        });
        $('#searchPayment').change(function () {
            table
                .columns('sum_of_payments:name')
                .search($('#searchPayment').val())
                .draw();
        });
        $('#searchLeft').change(function () {
            table
                .columns('left_to_pay:name')
                .search($('#searchLeft').val())
                .draw();
        });
        $('#packageTemplatesList').on('change', () => {
            $.ajax({
                url: laroute.route('package_templates.getPackageTemplate', {id: $('#packageTemplatesList').val()})
            }).done((data) => {
                $('#cost_for_client').val(data.approx_cost_client);
                $('#cost_for_company').val(data.approx_cost_firm);
            }).catch((error) => {
                console.error(error);
            });
        })

    </script>
    <script>
        var resource = null;

        $('#selectWarehouse').on('click', function () {
            var id = $(this).val();
            $.ajax({
                url: '/admin/warehouse/' + id
            }).done(function (data) {
                $('#selectWarehouse').hide();
                $('#titleModal').hide();
                $('#modalDialog').css({width: "auto !important"});
                document.getElementById('modalDialog').style.width = 'auto';
                renderCalendar();
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
                    if (data.status == 'TO_DO') {
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
                    if (data.status == 'REJECTED' && data.message == null) {
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

        $('#difference-button').click(() => {
            localStorage.setItem('differenceMode', true);
            alert('Włączono pokazywanie +/- 2 zł');
        })

        $('#findByDates').on('click', () => {
            findByDates();
        })

        $('#columnSearch-shipment_date').on('change', () => {
            let value = $('#columnSearch-shipment_date').val();
            const today = new Date();
            const yesterday = (d => new Date(d.setDate(d.getDate() - 1)))(new Date);
            const tomorrow = (d => new Date(d.setDate(d.getDate() + 1)))(new Date);

            switch (value) {
                case 'all':
                    $('#dates_from').data("DateTimePicker").date(null);
                    $('#dates_to').data("DateTimePicker").date(null);
                    break;
                case 'today':
                    $('#dates_from').data("DateTimePicker").date(today);
                    $('#dates_to').data("DateTimePicker").date(today);
                    break;
                case 'yesterday':
                    $('#dates_from').data("DateTimePicker").date(yesterday);
                    $('#dates_to').data("DateTimePicker").date(yesterday);
                    break;
                case 'tomorrow':
                    $('#dates_from').data("DateTimePicker").date(tomorrow);
                    $('#dates_to').data("DateTimePicker").date(tomorrow);
                    break;
                case 'tomorrow':
                    $('#dates_from').data("DateTimePicker").date(tomorrow);
                    $('#dates_to').data("DateTimePicker").date(tomorrow);
                    break;
                case 'from_tomorrow':
                    $('#dates_from').data("DateTimePicker").date(tomorrow);
                    $('#dates_to').data("DateTimePicker").date(null);
                    break;
            }
        })

        const selectOnlyWrongInvoiceBilansOrders = () => {
            $('#selectOnlyWrongInvoiceBilansOrders').text(localStorage.getItem('selectOnlyWrongInvoiceBilansOrders') === 'true' ? 'Wybierz wszystkie' : 'Wybierz tylko z błędami');
        }

        document.querySelector('#selectOnlyWrongInvoiceBilansOrders').addEventListener('click', () => {
            localStorage.setItem('selectOnlyWrongInvoiceBilansOrders', localStorage.getItem('selectOnlyWrongInvoiceBilansOrders') === 'true' ? 'false' : 'true');
            selectOnlyWrongInvoiceBilansOrders();
            table.ajax.reload();
        });

        setDeleteEventListeners();
    </script>

    <script>
        $(document).ready(function () {
            const now = new Date();
            const dateFrom = document.querySelector('#protocol_datepicker_from');
            const dateTo = document.querySelector('#protocol_datepicker_to');

            const day = ("0" + now.getDate()).slice(-2);
            const month = ("0" + (now.getMonth() + 1)).slice(-2);

            const today = (day) + "/" + (month) + "/" + now.getFullYear();

            dateFrom.value = today;
            dateTo.value = today;

            selectOnlyWrongInvoiceBilansOrders();
        });
    </script>

@endsection

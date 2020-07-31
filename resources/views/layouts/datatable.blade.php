@extends('layouts.app')

@section('app-content')
    <div class="browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            @yield('table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager.generic.delete_question_2') }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="position_change" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager.generic.delete_question_2') }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ csrf_field() }}
                        <div></div>
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="order_courier" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success pull-right" id="success-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="order_courier_problem" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas zamówienia kuriera</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="order_move_data" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Czy jesteś pewny że chcesz przenieść dane zamówienia <span id="order_id_get"></span> do zamówienia <span id="order_id_send"></span>?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="move-data-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="payment_move_data" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Czy jesteś pewny że chcesz przenieść dane zamówienia <span id="order_id_get"></span> do zamówienia <span id="order_id_send"></span>?</h4>
                    <div class="form-group">
                        <label for="new_date_for_timed_label_type_c">Kwota</label>
                        <input type="number" id="payment__amount" name="payment__amount" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="payment-move-data-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="payment_move_surplus" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Czy jesteś pewny że chcesz przenieść nadpłatę na konto klienta?</h4>
                    <div class="form-group">
                        <label for="new_date_for_timed_label_type_c">Kwota</label>
                        <input type="number" id="surplus__amount" name="surplus__amount" class="form-control">
                        <input type="hidden" name="surplus__order--id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="payment-move-surplus-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="order_invoices_delete" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="invoice__container">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wybierz fakturę do usunięcia</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="remove-selected-invoice" data-dismiss="modal">Usuń wybraną fakturę.</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="order_files_delete" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="files__container">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wybierz plik do usunięcia</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="remove-selected-file" data-dismiss="modal">Usuń wybrany plik</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="order_move_data_success" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Udało się przenieść dane.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="payment-move-data-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="order_move_data_error" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Nie udało się przenieść danych.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="order_move_data_ok_error"  data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="invoice_delete_success" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Pomyślnie usunięto fakturę.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="invoice-delete-ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="order_move_data_error_select" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Nie możesz wybrać tego samego zamówienia do przeniesienia danych.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="order_move_data_ok_error"  data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="payment_move_data_error_select" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Nie możesz wybrać zamówienia, które nie ma nadpłaty lub jest takie samo.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="order_move_data_ok_error"  data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="manual_label_selection_to_add_modal" role="dialog">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Etykiety do dodania</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="labels_to_add_after_removal_modal">@lang('labels.form.labels_to_add_after_removal')</label>
                        <select multiple class="form-control text-uppercase" id="labels_to_add_after_removal_modal" name="labels_to_add_after_removal_modal[]" size="6">
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-danger" id="labels_to_add_after_removal_modal_ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="added_label_is_type_c_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Usunięcie lub ponowne zaplanowanie dodania etykiety</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_date_for_timed_label_type_c">Ustaw ponową datę</label>
                        <input type="text" id="new_date_for_timed_label_type_c" name="new_date_for_timed_label_type_c" class="form-control default-date-time-picker-now">
                        <button type="button" class="btn btn-info" id="new_date_for_timed_label_type_c_ok" data-dismiss="modal">Zamień</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-danger" id="confirmed_to_remove_chosen_label" data-dismiss="modal">Usuń etykietę</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="set_time" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Ustaw termin płatnośći</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="years">Ustaw termin płatności: </label>
                        <input data-index="1" value="{{date("Y")}}" type="text" size="4" maxlength="4" id="invoice-years">

                        <input data-index="2" autofocus value="" type="text" size="2" maxlength="2" id="invoice-month">

                        <input data-index="3" value="" type="text" size="2" maxlength="2" id="invoice-days" >
                        <p id="invoice-date-error" hidden style="color: red">Błędny format daty</p>
                        <p id="set-date-error" hidden style="color: red">Błędny format daty</p>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Anuluj</button>
                    <button data-index="4" type="button" class="btn btn-danger" id="remove-label-and-set-date">Usuń etykietę</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="stock_modal" role="dialog">
        <div class="modal-dialog" id="stock_modal_dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Ustaw termin płatnośći</h4>
                </div>
                <div class="modal-body">
                    <h3>Błędy - ilość</h3>
                    <div id="quantity__errors">

                    </div>
                    <h3>Błędy - pozycje</h3>
                    <div id="position__errors">

                    </div>
                    <h3>Błędy - stan magazynowy</h3>
                    <div id="exists__errors">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Anuluj</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="move_position_quantity_error" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Nie możesz wybrać tego samego zamówienia do przeniesienia danych.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="move_position_quantity_ok_error"  data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="move_position_quantity" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>

                    <h4 class="modal-title">Czy jesteś pewny że chcesz przenieść okresloną ilość towaru z pozycji <span id="order_id_get"></span> na pozycję <span id="order_id_send"></span>?</h4>
                    <div class="form-group">
                        <label for="quantity__move">Ilość towaru do przeniesienia</label><input id="quantity__move" name="quantity__move" type="number" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success pull-right" id="move_position_quantity_ok" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('js/customSearchDataTable.js') }}"></script>
    <script>
        $('#set_time').on('keydown', 'input', function (event) {
            if (event.which == 13) {
                event.preventDefault();
                var $this = $(event.target);
                var index = parseFloat($this.attr('data-index'));
                $('[data-index="' + (index + 1).toString() + '"]').focus();
            }
        });
    </script>
    @yield('datatable-scripts')
@endsection

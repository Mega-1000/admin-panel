@extends('layouts.datatable')

@section('app-header')
    <style>
        th {
            position: relative;
        }

        th::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 8px;
            cursor: ew-resize;
        }

        body.resizing th::after {
            display: none;
        }

        .resizing {
            pointer-events: none;
        }
    </style>
    @livewireStyles
@endsection

@section('table')
    <livewire:order-datatable.order-datatable-index />

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
                        <select class="form-control text-uppercase" id="labels_to_add_after_removal_modal" name="labels_to_add_after_removal_modal[]" size="6">
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success" id="labels_to_add_after_removal_modal_ok" data-dismiss="modal">Zatwierdź</button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/datatable/labels-filter.js') }}"></script>
    <script src="{{ asset('js/datatable/set-delete-event-listeners.js') }}"></script>
    <script src="{{ asset('js/datatable/package-managment.js') }}"></script>
    <script src="{{ asset('js/datatable/labels-deletion.js') }}"></script>
@endsection

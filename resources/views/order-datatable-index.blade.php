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

        .loading {
            position: fixed;
            z-index: 999;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .loading::after {
            content: '';
            display: block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 4px solid #3498db; /* Change the color as needed */
            border-top: 4px solid transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #bs-select-1 {
            width: 80vw !important;
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

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/datatable/labels-filter.js') }}"></script>
    <script src="{{ asset('js/datatable/set-delete-event-listeners.js') }}"></script>
    <script src="{{ asset('js/datatable/package-managment.js') }}"></script>
    <script src="{{ asset('js/datatable/labels-deletion.js') }}"></script>
    <script>
        const uploadFile = (id) => {
            let url = "{{ route('orders.fileAdd', ['id' => '%%']) }}"
            $('#addNewFileToOrder').attr('action', url.replace('%%', id));
            $('#add-new-file').modal('show');
        }
    </script>
    <script>
        Livewire.on('orderMoved', () => {
            Swal.fire('success', 'Zamówienie zostało przeniesione', 'success');
        });

        document.querySelectorAll('#finalize-order-moving').forEach((item) => {
            item.style.display = 'none';
        })

        Livewire.on('orderToMoveSet', () => {
            document.querySelectorAll('#finalize-order-moving').forEach((item) => {
                item.style.display = 'block';
            })
        });

        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('component.initialized', (component) => {
                $('.selectpicker').selectpicker();
                $('.selectpicker').selectpicker('render');
            })
        });

        const addLabelsForCheckedOrders = async () => {
            const label = document.getElementById('choosen-label').value;

            await Livewire.emit('addLabelsForCheckedOrders', label);
            await Swal.fire('success', 'Etykiety zostały dodane', 'success');
        }

        const resetFilters = () => {
            const filterInputs = document.querySelectorAll('#filter');

            filterInputs.forEach(input => {
                input.value = '';
            });

            Livewire.emit('resetFilters');
        }

        const selectAllOrders = () => {
            const checkBoxes = document.querySelectorAll('input[type="checkbox"]')

            checkBoxes.forEach(checkBox => {
                checkBox.checked = true;
            });

            Livewire.emit('selectAllOrders');
        }
    </script>
    <script>
        function showLabelName(element, labelName) {
            const labelPopup = element.querySelector('.label-popup');
            labelPopup.innerHTML = labelName;
            labelPopup.style.display = 'block';
        }

        function hideLabelName(element) {
            const labelPopup = element.querySelector('.label-popup');
            labelPopup.style.display = 'none';
        }
    </script>
    <script>
        const showPhoneInformations = (id) => {
            const phoneInformations = document.getElementById('tooltip-phone-info-' + id);
            phoneInformations.style.display = 'block';
        }

        const hidePhoneInformations = (id) => {
            const phoneInformations = document.getElementById('tooltip-phone-info-' + id);
            phoneInformations.style.display = 'none';
        }

        // if there is query param of customer_addresses_0_phone
        if (window.location.href.includes('customer.addresses.0.phone')) {
            setTimeout(() => {
                // redirect to this url without query params
                window.location = window.location.href.split('?')[0];
            }, 1000);
        }

        const getFilesList = (id) => {
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

        $('#remove-selected-file').on('click', () => {
            let fileId = $('#files__list option:selected').val();
            let url = "<?php echo e(route('orders.fileDelete', ['file_id' => '%%'])); ?>"
            $.ajax({
                url: url.replace('%%', fileId)
            }).done(function (data) {
                $('#invoice_delete_success').modal('show');

                $('#invoice-delete-ok').on('click', function () {
                    location.reload();
                });
            })
        })

    </script>
@endsection
<script src="{{ asset('js/datatable/drag-and-drop.js') }}"></script>

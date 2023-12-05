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

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/datatable/labels-filter.js') }}"></script>
    <script>
        const setDeleteEventListeners = () => {
            setTimeout(() => {
                const deleteLinks = document.querySelectorAll('#delete');
                console.log(deleteLinks);
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
            }, 1000);
        }

        setDeleteEventListeners();
    </script>
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

    <script>
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
        const labelActionMapping = {
            45: () => showSelectWarehouseTemplate(),
        }

        const removeLabel = (labelId, orderId) => {
            const action = labelActionMapping[labelId];
            if (action) {
                action();
                return;
            }

            swal.fire({
                title: 'Jesteś pewien usuwania?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'OK',
            }).then(function (result) {
                if (result.isConfirmed) {
                    Livewire.emit('removeLabel', labelId, orderId);
                }
            });
        }
    </script>

@endsection

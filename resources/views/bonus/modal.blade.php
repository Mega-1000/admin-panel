@can('create-bonus')
    <div class="modal fade" tabindex="-1" id="add_bonus_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Dodaj potrącenie</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bonus.create') }}" id="add_new_bonus_form" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <div class="form-check">
                                <input name="user_id" class="form-check-input" type="radio" name="exampleRadios" id="select-consultant" value="-1">
                                <label class="form-check-label" for="select-consultant">
                                    Konsultant (<span id="consultant-name"></span>)
                                </label>
                            </div>
                            <div class="form-check">
                                <input name="user_id" class="form-check-input" type="radio" name="exampleRadios" id="select-warehouse" value="-2">
                                <label class="form-check-label" for="select-warehouse">
                                    Magazynier (<span id="warehouse-name"></span>)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nr zamówienia</label>
                            <input readonly="readonly" id="order_id" value="" class="form-control" name="order_id">
                        </div>
                        <div class="form-group">
                            <label>Kwota</label>
                            <input class="form-control" max="0" required name="amount" type="number" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Powód</label>
                            <input class="form-control" required name="cause" type="text">
                        </div>
                        <div class="form-group">
                            <label>Data nadania</label>
                            <input
                                value="{{ Carbon\Carbon::now()->format("Y-m-d") }}"
                                class="date-picker-bonus form-control" required name="date">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="add_new_bonus_form" class="btn btn-success pull-right">Utwórz
                    </button>
                    <button type="button" class="btn btn-default pull-right"
                            data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-success" onclick="$('#add_bonus_modal').modal('show')">Dodaj nową</button>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@endcan

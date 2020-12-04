@extends('layouts.datatable')
@section('table')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{route('transportPayment.update', ['delivererId' => $deliverer->id])}}">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="name">
                Nazwa
            </label>
            <input class="form-control" id="name" name="name" value="{{$deliverer->name}}">
        </div>

        @if ($importRules = $deliverer->importRules()->get())
            @foreach ($importRules as $rule)
                <div class="row rule">
                    <div class="col-md-2">
                        <label for="action">Rodzaj akcji</label>
                        <select name="action[]" id="action" class="form-control action">
                            <option value="">--wybierz--</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action->value }}"
                                    @if ($action->value === $rule->action)
                                        selected="selected"
                                    @endif
                                >{{ $action->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="value">Wartość</label>
                        <input type="text" class="form-control" id="value" name="value[]" value="{{ $rule->value }}" />
                    </div>
                    <div class="col-md-2">
                        <label for="columnName">Nazwa kolumny w bazie</label>
                        <select name="columnName[]" id="columnName" class="form-control">
                            <option value="">--wybierz--</option>
                            @foreach ($columns as $column)
                                <option value="{{ $column }}"
                                    @if ($column === $rule->db_column_name)
                                        selected="selected"
                                    @endif
                                >{{ $column }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="columnNumber">Nr kolumny w pliku CSV</label>
                        <select name="columnNumber[]" id="columnNumber" class="form-control">
                            <option value="">--wybierz--</option>
                            @foreach ($csvColumnsNumbers as $number)
                                <option value="{{ $number }}"
                                    @if ($number === $rule->import_column_number)
                                        selected="selected"
                                    @endif
                                >{{ $number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="value">Zamień na</label>
                        <input type="text" class="form-control" id="value" name="changeTo[]" value="{{ $rule->change_to }}" />
                    </div>
                    <div class="col-md-1 manage-rule">
                        <a href="#" class="addNewRule inline-block">Dodaj +</a>
                    </div>
                </div>
            @endforeach
        @endif
        <button type="submit" class="btn btn-primary">Zapisz</button>
    </form>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('.addNewRule').click((event) => {
            event.preventDefault();

            copyRuleSchema();
        });

        $('.removeRule').click((event) => {
            event.preventDefault();

            $(this).closest('.rule').remove();
        });

        prepareRules();

        function copyRuleSchema() {
            const countRules = $('.rule').length;
            const newRule = $('.rule:first').clone(true);
            const id = Math.random().toString(36).substring(7);

            if (countRules >= 2) {
                newRule.find('.action option[value="searchCompare"]').remove();
                newRule.find('.action option[value="searchRegex"]').remove();
            }

            newRule.attr('id', id);
            newRule.find('.manage-rule').append(() => {
                return $('<a href="#" class="removeRule inline-block">Usuń +</a>').click(() => {
                    $("#" + id).remove();
                });
            });

            $('.rule:last').after(newRule);
        }

        function prepareRules() {
            const $rules = $('.rule');

            if ($rules.length) {
                $rules.each(function (index) {
                    if (index >= 2) {
                        $(this).find('.action option[value="searchCompare"]').remove();
                        $(this).find('.action option[value="searchRegex"]').remove();
                    }

                    const ruleId = Math.random().toString(36).substring(7);
                    $(this).attr('id', ruleId);

                    $(this).find('.manage-rule').append(() => {
                        return $('<a href="#" class="removeRule inline-block">Usuń +</a>').click(() => {
                            $("#" + ruleId).remove();
                        });
                    });
                });
            }
        }
    </script>
@endsection

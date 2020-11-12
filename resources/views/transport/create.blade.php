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
    <form method="POST" action="{{route('transportPayment.store')}}">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="nazwa">
                Nazwa
            </label>
            <input class="form-control" name="name" value="{{ old('name') }}" />
        </div>

        <div class="row rule">
            <div class="col-md-2">
                <label for="action">Rodzaj akcji</label>
                <select name="action[]" id="action" class="form-control action">
                    <option value="">--wybierz--</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action->value }}">{{ $action->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="value">Wartość</label>
                <input type="text" class="form-control" id="value" name="value[]" value="" />
            </div>
            <div class="col-md-2">
                <label for="columnName">Nazwa kolumny w bazie</label>
                <select name="columnName[]" id="columnName" class="form-control">
                    <option value="">--wybierz--</option>
                    @foreach ($columns as $column)
                        <option value="{{ $column }}">{{ $column }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="columnNumber">Nr kolumny w pliku CSV</label>
                <select name="columnNumber[]" id="columnNumber" class="form-control">
                    <option value="">--wybierz--</option>
                    @foreach ($csvColumnsNumbers as $number)
                        <option value="{{ $number }}">{{ $number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="value">Zamień na</label>
                <input type="text" class="form-control" id="value" name="changeTo[]" value="" />
            </div>
            <div class="col-md-1 manage-rule">
                <a href="#" class="addNewRule inline-block">Dodaj +</a>
            </div>
        </div>

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
            console.log('remove');
            event.preventDefault();

            $(this).closest('.rule').remove();
        });

        function copyRuleSchema() {
            const countRules = $('.rule').length;

            const newRule = $('.rule:first').clone(true);
            const id = Math.random().toString(36).substring(7);

            if (countRules >= 2) {
                newRule.find('.action option[value="search"]').remove();
                newRule.find('.action option[value="searchByParse"]').remove();
            }

            newRule.attr('id', id);
            newRule.find('.manage-rule').append(() => {
                return $('<a href="#" class="removeRule inline-block">Usuń +</a>').click(() => {
                    $("#" + id).remove();
                });
            });

            $('.rule:last').after(newRule);
        }
    </script>
@endsection

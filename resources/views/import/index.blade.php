@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-move"></i> @lang('import.title')
    </h1>
@endsection

@section('app-content')
    <div class="browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if (Session::get('success'))
                    <div class='alert alert-success'>Ceny zostały przeliczone</div>
                @endif
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <div class="form-group">
                                <label for="import" class="text-body">@lang('import.last_import')</label>
                                <input type="text" class="form-control default-date-time-picker-now" disabled
                                       value="{{$import->last_import}}">
                            </div>
                            <div class="form-group">
                                <label for="import" class="text-body">@lang('import.last_import_done')</label>
                                <input type="text" class="form-control default-date-time-picker-now" disabled
                                       value="{{$importDone->last_import}}">
                            </div>
                            <div class="form-group">
                                <label for="import" class="text-body"></label>
                                <button href="#" class="btn btn-primary" id="import"
                                   name="import" @if($import->processing == 1) disabled @endif> @lang('import.do_import')</button>
                                <label for="recalculate-prices" class="text-body"></label>
                                <a href="{{ route('job.recalculatePrices') }}" class="btn btn-primary">
                                    Przelicz ceny
                                </a>
                                <a href="{{ route('job.generateJpgs') }}" class="btn btn-primary">
                                    Wygeneruj tabele i reklamówki
                                </a>
                            </div>
                            <form action="{{ route('import.store') }}" enctype="multipart/form-data" method="POST">
                                {{ csrf_field() }}
                                Wgraj plik do importu ( może to potrwać do 20-30min w zależnosci do szybkości łącza )
                                <br />
                                <input type="file" name="importFile" />
                                <br /><br />
                                <input type="submit" value=" Wgraj " />
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="modal_import" role="dialog">
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
@endsection
@section('scripts')
    <script>
        $('#import').on('click', function () {
            $.ajax({
                url: '{{route('import.do')}}',
            }).done(function (data) {
                $('#modal_import').modal('show');
                if(data.message !== null) {
                    $('#modal_import > div > div > div.modal-header > h4').append('<span>'+data.message+'</span>');
                } else {
                    $('#modal_import > div > div > div.modal-header > h4').append('<span>'+data.message+'</span>');
                }
                $('.btn-success').on('click', function(){
                    location.reload();
                });
            }).fail(function(){
                $('#modal_import').modal('show');
                $('#modal_import > div > div > div.modal-header > h4').append('<span>Wystąpił błąd podczas importu</span>');
                $('.btn-success').on('click', function(){
                    location.reload();
                });
            });
        });
    </script>
@endsection
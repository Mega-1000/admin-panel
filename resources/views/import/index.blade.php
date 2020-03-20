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
                @if ($import->processing)
                    <div class='alert alert-info'>
                        Plik jest aktualnie przetwarzany. Przetwarzanie trwa już {{ round((time() - strtotime($import->last_import)) / 60) }} minut.<br>
                        W przypadku nieoczekiwanego błędu plik zostanie automatycznie usunięty po 30 minutach.<br>
                        Prosimy <span style='font-weight: bold'>nie wgrywać</span> pliku przez FTP w trakcie trwania importu.
                        Wgrywanie przez Voyager jest w tej chwili zablokowane.
                    </div>
                @endif
                @if (Session::get('flash-message'))
                    <div class='alert alert-{{ Session::get('flash-message')['type'] }}'>{{ Session::get('flash-message')['message'] }}</div>
                @endif
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <div class="form-group">
                                <label class="text-body">@lang('import.last_import')</label>
                                <input type="text" class="form-control default-date-time-picker-now" disabled value="{{$import->last_import}}">
                            </div>
                            <div class="form-group">
                                <label class="text-body">@lang('import.last_import_done')</label>
                                <input type="text" class="form-control default-date-time-picker-now" disabled value="{{$importDone->last_import}}">
                            </div>
                            <div class="form-group">
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
@endsection

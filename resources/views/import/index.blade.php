@extends('layouts.datatable')

@section('app-header')
<h1 class="page-title">
    <i class="voyager-move"></i> @lang('import.title')
</h1>
@endsection

@section('app-content')
<div class="browse container-fluid">
    <a class="btn btn-primary" href="{{ route('fast-response.index') }}">
        Centrum zarządzania szybkimi odpowiedziami
    </a>

    <a class="btn btn-primary" href="/admin/set-logs-permissions">
        Permisje logów
    </a>

    <a class="btn btn-primary" href="/admin/email-reports">
        Raporty maili
    </a>

    <a class="btn btn-primary" href="{{ route('orderPayments.recalculateAllOrders') }}">
        Przelicz wszystkie zamówienia pod względem bilansu
    </a>

    <a href="{{ route('newsletter_messages.create') }}" class="btn btn-primary">
        Zarządzanie newsletterem
    </a>

    <a href="{{ route('low-quantity-alerts.index') }}" class="btn btn-primary">
        Zarzadzanine automatycznymi powiadomieniami na e maila
    </a>

    <a href="{{ route('form-creator.create') }}" class="btn btn-primary">
        Kreator formularzy
    </a>

    <a href="{{ route('product-packets.index') }}" class="btn btn-primary">
        Kreator pakietów
    </a>

    <a href="{{ route('newsletter.index') }}" class="btn btn-primary">
        Zarządzanie gazetką
    </a>

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
                            <a name="actualization-price" target="_blank" class="btn btn-primary" href="products/getPrice">Pobierz csv z nowymi cenami produktów</a>
                            <a name="actualization-price" target="_blank" class="btn btn-primary" href="orders/getCosts">Pobierz realne wartości zleceń paczek ze specyfikacji firm spedycyjnych</a>
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
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        Wczytywanie pliku kontrolnego dla NEXO
                    </div>
                </div>
                <div class="panel-body">
                    <form action="{{ route('import.storeNexoController') }}" enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <div class="col-md-12">
                            <input type="date" name="nexoStartDate" value="{{ $currentDate }}" id="nexoStartDate">
                        </div>
                        <div class="col-md-12">
                            <div class="input-group-file">
                                <input id="file" name="importFile" class="btn btn-file" type="file" aria-describedby="file">
                            </div>
                            <button type="submit" class="btn btn-success">Wczytaj</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        Wczytywanie faktur
                    </div>
                </div>
                <div class="panel-body">
                    <form action="{{ route('uploadInvoice') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <input type="file" name="files[]" multiple>
                        <button type="submit" class="btn btn-success">Wczytaj</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        Wczytywanie etykiet
                    </div>
                </div>
                <div class="panel-body">
                    <form action="{{ route('add-labels-from-csv-file') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <label for="labels_to_add">Etykiety do dodania</label>
                        <input type="text" name="labels_to_add" id="labels_to_add" class="form-control" placeholder="np. 123, 456, 789">

                        <label for="labels_to_delete">Etykiety do usunięcia</label>
                        <input type="text" name="labels_to_delete" id="labels_to_delete" class="form-control" placeholder="np. 123, 456, 789">

                        <input type="file" name="file">
                        <button type="submit" class="btn btn-success">Wczytaj</button>
                    </form>
                </div>
            </div>
        </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        Import danych - billing allegro
                    </div>
                </div>
                <div class="panel-body">
                    <form method="post" action="{{ route('import-allegro-billing') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file">

                        <button class="btn btn-primary">Importuj billing allegro</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        MODUŁ ZACZYTYWANIA FAKTUR Z SUBIEKTA ( modul ten dokonuje wpisu wszystkich fakur zaczytywanych z pliku csv na gridzie do kolumny bilans faktur subiekt, plik csv powstaje przez skopiowanie z subiekta spisu wszystkich faktur i wklejenie go do exela i nastepnie przetworzenie go do csv )
                    </div>
                </div>
                <div class="panel-body">
                    <select name="invoice-kind">
                        <option>faktury sprzedazy</option>
                        <option>faktury zakupu</option>
                    </select>

                    <form method="post" action="{{ route('controll-subject-invoices') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file">

                        <button class="btn btn-primary">Kontroluj</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        {{ \App\ChatStatus::first()->is_active === 0 ? 'Konsultanci dla czatu/duskusja są dostępni' : 'Konsultanci dla czatu/duskusja są nie dostępni' }}
                    </div>
                </div>
                <div class="panel-body">
                    <form method="post" action="{{ route('change-chat-visibility') }}">
                        @csrf
                        tekst na czacie
                        <textarea type="text" class="form-control" name="message-value">{{ \App\ChatStatus::first()->message }}</textarea>

                        <button class="{{ \App\ChatStatus::first()->is_active === 0 ? 'btn btn-success' : 'btn btn-danger' }}">
                            Zmień informację o dostępności konsultantów
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                        Przelicz etykiety płatności w zamówieniach
                    </div>
                </div>
                <div class="panel-body">
                    <form action="{{ route('recalculate-labels-in-orders-based-on-period') }}" method="post">
                        @csrf
                        Data od
                        <input type="date" class="form-control" name="time-from">
                        Data do
                        <input type="date" class="form-control" name="time-to">
                        <br>
                        Licz jedynie zamówienia z etykietą niebieski dolar <input name="calculate-only-with-39" type="checkbox">

                        <button type="submit" class="btn btn-primary">
                            Przelicz
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-title">
                       Generuj raport rzeczywistych kosztów - listy przewozowe
                    </div>
                </div>
                <div class="panel-body">
                    <a href="{{ route('generateRealCostForCompanyInvoiceReport') }}" class="btn btn-primary" id="generate-report">Generuj raport</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

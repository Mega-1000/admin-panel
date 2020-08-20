<div class="row">
    <div id="add-label-container">
        <button class="btn btn-warning" onclick="clearFilters()">Wyszczyść filtry</button>
        <button id="showTable" class="btn btn-warning" style="margin-left: 5px">Pokaż Tabelkę z Etykietami
            Pracownika
        </button>
        <div class="col-md-12 hidden" style="float: right" id="labelTable">
            <table width="50%" style="float:right;" border="1">
                <tr>
                    <th colspan="1" width="10%"></th>
                    <th colspan="1" width="10%">Pracownik</th>
                    <th colspan="1" width="5%">Przedawnienia</th>
                    <th colspan="{{count($labIds['payments'])}}" width="17,5%" style="text-align: center">
                        Płatności
                    </th>
                    <th colspan="{{count($labIds['production'])}}" width="15%" style="text-align: center">
                        Produkcja
                    </th>
                    <th colspan="{{count($labIds['transport'])}}" width="17,5%" style="text-align: center">Transport
                    </th>
                    <th colspan="{{count($labIds['info'])}}" width="20%" style="text-align: center">Info Dodatkowe
                    </th>
                    <th colspan="{{count($labIds['invoice'])}}" width="5%" style="text-align: center">Faktury Zakupu
                    </th>
                </tr>
                <tr>
                    <th>Ikona Etykiety</th>
                    <th></th>
                    <th></th>
                    @foreach ($labIds as $labIdGroup)
                        @foreach ($labIdGroup as $labId)
                            @foreach ($labels as $label)
                                @if ($labId == $label->id)
                                    <th scope="col">
                                        <div style="width:40px;" align="center">
                                            <span title="{{$label->name}}">
                                            <i style="font-size: 2.5rem; color: {{$label->color}}"
                                               class="{{$label->icon_name}}"></i> </span>
                                        </div>
                                    </th>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
                @foreach ($outs as $out)
                    @if (!empty($out['user']->orders[0]))
                        <tr>
                            <th style="text-align: center">{{$out['user']->id}}</th>
                            <th>{{$out['user']->firstname}} {{$out['user']->lastname}}</th>
                            <th>{{$out['outdated']}}</th>
                            @foreach ($labIds as $labIdGroup)
                                @foreach ($labIdGroup as $labId)
                                    <td style="text-align: center">{{$out[$labId] ?? 0}}</td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <input type="hidden" id="spedition-exchange-selected-items" value="[]">
        <button class="btn btn-secondary" onclick="gerateSpeditionExchangeLink()">Giełda spedycyjna (generuj link)
        </button>
        <div class="spedition-exchange-generated-link"></div>
    </div>
    <div style="display: flex; align-items: center;" id="add-label-container">
        <a name="actualization-price" target="_blank" class="btn btn-success" href="/admin/actualizationPrice">Wyślij
            prośbę o aktualizację cen</a>
    </div>
    <div class="col-md-10">
        <form method="POST" action="{{ route('order_packages.getProtocols') }}">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group">
                {{csrf_field()}}
                <label for="protocols">Protokoły z dnia</label>
                <input name="date_from" class="protocol_datepicker" id="protocol_datepicker_from"
                       value="{{ Carbon\Carbon::now()->format('d/m/yy') }}"/>
                do dnia
                <input name="date_to" class="protocol_datepicker" id="protocol_datepicker_to"
                       value="{{ Carbon\Carbon::now()->format('d/m/yy') }}"/>
                z magazynu:
                <input type="text" id="delivery_warehouse" name="delivery_warehouse"
                       value="MEGA-OLAWA"/>
                <input type="submit" name="courier" value="Inpost" target="_blank" class="btn btn-success"/>
                <input type="submit" name="courier" value="Dpd" target="_blank" class="btn btn-success"/>
                <input type="submit" name="courier" value="Pocztex" target="_blank" class="btn btn-success"/>
                <input type="submit" name="courier" value="Apaczka" target="_blank" class="btn btn-success"/>
                <input type="submit" name="courier" value="Jas" target="_blank" class="btn btn-success"/>
                <input type="submit" name="courier" value="Gielda" target="_blank" class="btn btn-success">
                <input type="submit" name="courier" value="Gls" target="_blank" class="btn btn-success"/>
                <input type="submit" name="courier" value="Wszystkie" target="_blank" class="btn btn-info"/>
            </div>
        </form>
        <div class="form-group">
            <label for="send_courier">Wyślij kurierów: </label>
            <button name="send_courier" class="send_courier_class btn btn-success"
                    href="/admin/orderPackages/INPOST/send">Inpost
            </button>
            <button name="send_courier" class="send_courier_class btn btn-success"
                    href="/admin/orderPackages/DPD/send">DPD
            </button>
            <button name="send_courier" class="send_courier_class btn btn-success"
                    href="/admin/orderPackages/POCZTEX/send">Pocztex
            </button>
            <button name="send_courier" class="send_courier_class btn btn-success"
                    href="/admin/orderPackages/APACZKA/send">Apaczka
            </button>
            <button name="send_courier" class="send_courier_class btn btn-success"
                    href="/admin/orderPackages/JAS/send">Jas
            </button>
            <button name="send_courier" class="send_courier_class btn btn-success"
                    href="/admin/orderPackages/GLS/send">GLS
            </button>
            <button name="send_courier" class="send_courier_class btn btn-info"
                    href="/admin/orderPackages/ALL/send">Wyślij wszystkie
            </button>
            <button name="send_courier" class="send_courier_class btn btn-danger"
                    id="filtered-packages">Wyślij przefiltrowane
            </button>
        </div>
        <div id="packages_errors" class="form-group">
        </div>
        <div class="form-group">
            <label for="package_template">Szablony paczek:</label>
            <a name="package_template" class="btn btn-success" href="/admin/packageTemplates/">Lista Szablonów</a>
            <a name="package_template" class="btn btn-info" href="/admin/packageTemplates/create">Dodaj szablon</a>
            <label style="margin-left: 20px" for="container_type">Rodzaje przesyłek:</label>
            <a style="margin-left: 5px" name="container_type" class="btn btn-success" href="/admin/containerTypes/">Lista
                rodzajów przesyłek</a>
            <a name="container_type" class="btn btn-info" href="/admin/containerTypes/create">Dodaj rodzaj przesyłki</a>
        </div>
        <div>
            <label for="content_type">Typy zawartości przesyłek:</label>
            <a name="content_type" class="btn btn-success" href="/admin/contentTypes/">Lista Typów zawartości
                przesyłek</a>
            <a name="content_type" class="btn btn-info" href="/admin/contentTypes/create">Dodaj typ zawartości
                przesyłki</a>
            <label style="margin-left: 20px" for="packing_type">Typy opakowań przesyłek:</label>
            <a style="margin-left: 5px" name="packing_type" class="btn btn-success" href="/admin/packingTypes/">Lista
                Typów
                opakowań przesyłek</a>
            <a name="packing_type" class="btn btn-info" href="/admin/packingTypes/create">Dodaj typ opakowania
                przesyłki</a>
        </div>
        <br>
        <div class="form-group">
            <label for="import_sello">Import z SELLO: </label>
            <a name="import_sello" class="btn btn-success" href="{{ route('orders.sello_import') }}">Importuj</a>
            <label for="send_labels_allegro">Wyślij numery naklejek do allegro: </label>
            <a name="send_labels_allegro" class="btn btn-success" href="{{ route('orders.send_tracking_numbers') }}">Wyślij
                numery</a>
        </div>
        <div class="form-group">
            <label for="print_courier">Drukuj naklejki: </label>
            @foreach($couriers as $courier)
                <a name="print_courier" class="btn btn-success"
                   href="{{route('order_packages.letters',['courier_name'=>$courier->delivery_courier_name])}}">
                    {{$courier->delivery_courier_name}}</a>
            @endforeach
        </div>
        <div class="form-group">
            Import cen paczek.
            <label for="deliverers">Lista dostawców: </label>
            <a name="deliverers" class="btn btn-success" href="{{ route('transportPayment.list') }}">Dostawcy</a>
            <label for="import_packages_payment">Wczytaj płatności dla paczek:</label>
            <a name="import_packages_payment" class="btn btn-success" onclick="$('#upload-payments').modal('show')">Wyślij
                numery</a>
            @if(!empty(session('update_errors')))
                @foreach(session('update_errors') as $error)
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>
                                Nie znaleziono paczki o liście nr:
                            </th>
                            <th>
                                Brak kosztów dla paczki nr:
                            </th>
                            <th>
                                Inny błąd:
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $type1 = session('update_errors')[1] ?? [];
                            $type2 = session('update_errors')[2] ?? [];
                            $typeOther = session('update_errors')['other'] ?? [];

                            $length = max([count($type1), count($type2), count($typeOther)]);
                        @endphp
                        @for ($i = 0; $i < $length; $i++)
                            <tr>
                                <td>{!! isset($type1[$i]) ? $type1[$i] : '' !!}</td>
                                <td>{!! isset($type2[$i]) ? $type2[$i] : '' !!}</td>
                                <td>{!! isset($typeOther[$i]) ? $typeOther[$i] : '' !!}</td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>
        <div class="form-group">
            <label for="upload-allegro-pays">Aktualizuj płatności allegro: </label>
            <a id="upload-allegro-pays" name="print_orders" class="btn btn-success"
               onclick="$('#upload-allegro-payments').modal('show')">Aktualizuj</a>
            <button id="upload-allegro-commission-button" name="print_orders" class="btn btn-success"
               onclick="$('#upload-allegro-commission-modal').modal('show')">Aktualizuj prowizje Allegro</button>
            @if(!empty(session('allegro_new_letters')))
                @foreach(session('allegro_new_letters') as $letter)
                    <div class="alert-info">
                        {{ $letter['letter_number'] }}
                    </div>
                @endforeach
                    <form method="POST" action="{{ route('orders.newLettersFromAllegro') }}">
                        @csrf
                        <input type="hidden" name="letters" value="{{ json_encode(session('allegro_new_letters'))}}">
                        <button class="btn btn-success">Utwórz nowe listy</button>
                    </form>
            @endif
            @if(!empty(session('allegro_commission_errors')))
                @foreach(session('allegro_commission_errors') as $error)
                    <div class="alert-danger">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
                @if(!empty(session('allegro_payments_errors')))
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>
                            W tabeli importu sello nie istnieje transakcja o id:
                        </th>
                        <th>
                            Nie zaimportowano zlecenia z systemu sello do mega1000:
                        </th>
                        <th>
                            Transakcja została już wcześniej opłacona:
                        </th>
                        <th>
                            Brak poprawnej kwoty obietnicy wpłaty. Proszę sprawdzić czy kwota obietnicy wpłaty się
                            zgadza.
                        </th>
                        <th>
                            Inne błędy:
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $type1 = session('allegro_payments_errors')[1] ?? [];
                        $type2 = session('allegro_payments_errors')[2] ?? [];
                        $type3 = session('allegro_payments_errors')[3] ?? [];
                        $type4 = session('allegro_payments_errors')[4] ?? [];
                        $typeOther = session('allegro_payments_errors')['other'] ?? [];

                        $length = max([count($type1), count($type2), count($type3), count($type4), count($typeOther)]);
                    @endphp
                    @for ($i = 0; $i < $length; $i++)
                        <tr>
                            <td>{!! !empty($type1[$i]) ? json_decode($type1[$i])->id : '' !!}</td>
                            <td>{!! !empty($type2[$i]) ? json_decode($type2[$i])->id : '' !!}</td>
                            <td>{!! !empty($type3[$i]) ? json_decode($type3[$i])->id : '' !!}</td>
                            <td>{!! !empty($type4[$i]) ? json_decode($type4[$i])->id : '' !!}</td>
                            <td>{!! !empty($typeOther[$i]) ? 'transakcja: ' . json_decode($typeOther[$i])->id. ', kwota: ' . json_decode($typeOther[$i])->amount : '' !!}</td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
                <form method="POST" action="{{ route('orders.create-payments')}}">
                    @csrf
                    <input type="hidden" name="payments_ids" value="{{ json_encode($type1)}}">
                    <button class="btn btn-success">Utwórz transakcje</button>
                </form>
            @endif
        </div>
        <div class="form-group">
            <label for="print_orders">Drukuj zamówienia widoczne na stronie: </label>
            <a name="print_orders" class="btn btn-success" onclick="printAll()">Drukuj wszystkie</a>
        </div>
        <div style="display: flex; align-items: center;" id="add-label-container">
            <button onclick="addLabel()" type="button" class="btn btn-primary">@lang('orders.table.save_label')</button>
            <select style="margin-left: 10px;" class="form-control text-uppercase" id="choosen-label">
                <option value="" selected="selected">@lang('orders.table.choose_label')</option>
                @foreach($groupedLabels as $groupName => $group)
                    <optgroup label="{{ $groupName }}">
                        @foreach($group as $label)
                            <option value="{{ $label->id }}"
                                    data-timed="{{ $label->timed }}">{{ $label->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div style="display: flex; align-items: center;" id="add-label-container">
            <button class="btn btn-text">Filtr zamówień</button>
            <select style="margin-left: 10px;" class="form-control text-uppercase orderFilter" id="orderFilter">
                <option value="ALL">Wszystkie</option>
                <option value="PENDING">Wszystkie zlecenia wyłączając bez realizacji oraz zakończone</option>
                <option value="WITHOUT_REALIZATION">Bez realizacji</option>
                <option value="COMPLETED">Oferta zakończona</option>
            </select>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <div class="form-group">
                    <label for="searchLP">Szukaj po LP</label>
                    <input type="search" class="form-control" name="searchLP" id="searchLP">
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="form-group">
                    <label for="searchOrderValue">Szukaj po Wartości zamówienia</label>
                    <input type="search" class="form-control" name="searchOrderValue" id="searchOrderValue">
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="form-group">
                    <label for="searchPayment">Szukaj po Zaliczce</label>
                    <input type="search" class="form-control" name="searchPayment" id="searchPayment">
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="form-group">
                    <label for="searchLeft">Szukaj po Pozostały do zapłaty</label>
                    <input type="search" class="form-control" name="searchLeft" id="searchLeft">
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <label for="searchById">Zajdź po ID</label>
                <input type="search" name="searchById" id="searchById">
                <button onclick="findPage()" class="btn btn-success">Znajdź zlecenie</button>
            </div>
            <div class="col-md-4 mb-4">
                <label for="selectAllOrders">Zaznacz wszystkie zlecenia</label>
                <input id="selectAllOrders" type="checkbox"/>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <h4>Drukuj paczki z grupy:</h4>
        <div class="row">
            <button class="btn btn-info print-group" name="paczkomat">Paczkomat</button> ({{ $couriersCount['inpost'] }})
        </div>
        <div class="row">
            <button class="btn btn-info print-group" name="gls">Gls</button> ({{ $couriersCount['gls'] }})
        </div>
        <div class="row">
            <button class="btn btn-info print-group" name="dpd">Dpd</button> ({{ $couriersCount['dpd'] }})
        </div>
        <div class="row">
            <button class="btn btn-info print-group" name="pocztex">Pocztex</button> ({{ $couriersCount['pocztex'] }})
        </div>
        <div class="row">
            <button class="btn btn-info" id="create-new-task-button" name="custom">Dodatkowe Zadanie</button>
        </div>
        <div class="row">
            <button id="accept-pack" class="btn btn-success">Wykonano</button>
            <button id="deny-pack" class="btn btn-danger">Odrzucono</button>
        </div>
    </div>
</div>

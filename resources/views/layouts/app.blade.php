@extends('voyager::master')
@section('css')
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    {{--<link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">--}}
    <style>
        #dataTable thead tr:first-child th:first-child {
            width: 60px !important;
        }

        .dt-button-collection {
            margin-top: 15px !important;
            display: block;
        }

        .orderFilter {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

         .columnSearchSelect {
             margin-top: 20px;
             max-width: 115px;
         }

        .btn-text {
            color: black;
            background: transparent;
            cursor: unset;
            padding-top: 0px;
            margin-top: 0px;
            padding-left: 20.5px;
            padding-right: 20.5px;
        }

        .orderFilter .select2 {
            width: 91.5% !important;
        }

        .orderFilter label {
            width: 8.5% !important;
            font-size: 14px;
        }

        #dataTable_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableWarehouses_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableEmployees_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableOrderMessages_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableOrderPackages_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableOrderPayments_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableOrderTasks_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTablePositions_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTableLogs_wrapper > div.dt-buttons > div > button:nth-child(1) {
            display: none;
        }

        #dataTable thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTable thead tr th > div {
            height: 50px;
        }

        #dataTable thead tr th div input {
            margin-top: 20px;
            text-align: center;
        }

        #dataTableEmployees thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTableEmployees thead tr th div {
            height: 50px;
        }

        .ordersTable #columnSearch4 {
            width: 20px !important;
        }

        .ordersTable #columnSearch8 {
            width: 130px !important;
        }

        .ordersTable #columnSearch10 {
            width: 140px !important;
        }

        .ordersTable #columnSearch11 {
            width: 300px !important;
        }

        .ordersTable #columnSearch12 {
            width: 90px !important;
        }

        .ordersTable #columnSearch12 {
            width: 140px !important;
        }

        .ordersTable #columnSearch20 {
            width: 70px !important;
        }

        .ordersTable #columnSearch24 {
            width: 100px !important;
        }

        .ordersTable #columnSearch36 {
            width: 300px !important;
        }

        .ordersTable #columnSearch29 {
            width: 80px !important;
        }

        #dataTableOrderPackages #columnSearch2 {
            width: 60px !important;
        }

        #dataTableOrderPackages #columnSearch3 {
            width: 80px !important;
        }

        #dataTableOrderPackages #columnSearch4 {
            width: 90px !important;
        }

        #dataTableOrderPackages #columnSearch5 {
            width: 300px !important;
        }

        #dataTableOrderPackages #columnSearch6 {
            width: 30px !important;
        }

        #dataTableOrderPackages #columnSearch7 {
            width: 30px !important;
        }

        #dataTableOrderPackages #columnSearch8 {
            width: 30px !important;
        }

        #dataTableOrderPackages #columnSearch9 {
            width: 60px !important;
        }

        #dataTableOrderPackages #columnSearch10 {
            width: 80px !important;
        }

        #dataTableOrderPackages #columnSearch11 {
            width: 80px !important;
        }

        #dataTableOrderPackages #columnSearch12 {
            width: 90px !important;
        }

        #dataTableOrderPackages #columnSearch13 {
            width: 400px !important;
        }

        #dataTableOrderPackages #columnSearch14 {
            width: 80px !important;
        }

        #dataTableOrderPackages #columnSearch15 {
            width: 80px !important;
        }

        #dataTableOrderPackages #columnSearch16 {
            width: 160px !important;
        }

        #dataTableOrderPackages #columnSearch17 {
            width: 160px !important;
        }

        #dataTableOrderPackages #columnSearch18 {
            width: 90px !important;
        }

        #dataTableOrderPackages #columnSearch19 {
            width: 90px !important;
        }

        #dataTableOrderPackages #columnSearch20 {
            width: 90px !important;
        }

        #dataTableOrderPackages #columnSearch21 {
            width: 200px !important;
        }

        #dataTableOrderPackages #columnSearch22 {
            width: 100px !important;
        }

        .input_div {
            margin-top: 0px;
            text-align: center;
        }

        #dataTableEmployees thead tr th div input {
            width: auto;
            text-align: center;
        }

        #dataTableWarehouses thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTableWarehouses thead tr th div {
            height: 50px;
        }

        #dataTableWarehouses thead tr th div input {
            width: auto;
            text-align: center;
        }

        .button-page-length {
            display: block !important;
        }

        #dataTableOrderPackages thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTableOrderPackages thead tr th div {
            height: 50px;
        }

        #dataTableOrderPackages thead tr th div input {
            width: auto;
            text-align: center;
        }

        #dataTableOrderPayments thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTableOrderPayments thead tr th div {
            height: 50px;
        }

        #dataTableOrderPayments thead tr th div input {
            width: auto;
            text-align: center;
        }

        #dataTableOrderTasks thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTableOrderTasks thead tr th div {
            height: 50px;
        }

        #dataTableOrderTasks thead tr th div input {
            width: auto;
            text-align: center;
        }

        #dataTableLogs thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTableLogs thead tr th div {
            height: 50px;
        }

        #dataTableLogs thead tr th div input {
            width: auto;
            text-align: center;
        }

        #dataTablePositions thead tr th {
            padding: 5px 5px 5px 5px;
        }

        #dataTablePositions thead tr th div {
            height: 50px;
        }

        #dataTablePositions thead tr th div input {
            width: auto;
            text-align: center;
        }

        .dt-button {
            margin-top: 10px;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        .buttons-columnVisibility {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            background-image: none !important;
            box-shadow: none !important;
            color: white !important;
            font-weight: 400 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            border: 1px solid transparent !important;
            padding: .375rem .75rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            border-radius: .25rem !important;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
        }

        .dt-button.active {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            background-image: none !important;
            box-shadow: none !important;
            color: white !important;
            font-weight: 400 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            border: 1px solid transparent !important;
            padding: .375rem .75rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            border-radius: .25rem !important;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
        }

        div.dt-button-collection {
            width: auto;
        }

        .ui-tooltip {
            /* tooltip container box */
            white-space: pre-line;
        }

        .ui-tooltip-content {
            /* tooltip content */
            white-space: pre-line;
        }

        .first-com {
            float: left;
            width: 50%;
        }
        .second-com {
            float: right;
            width: 50%;
        }
        .three-com {
            float:left;
            width: 33.3%;
        }

        #productsTable {
            table-layout: fixed;
        }
        #dataTable-warehouseOrder #columnSearch32, #columnSearch33, #columnSearch36, #columnSearch37, #columnSearch40, #columnSearch41, #columnSearch45, #columnSearch46, #columnSearch50, #columnSearch51 {
            width: 100px;
        }
        #dataTable-warehouseOrder .itemPrice {
            width: 100px;
        }
        #columnSearch-choose_date {
            margin-top: 0px;
            display: inline-block;
        }
        #columnSearch-shipment_date {
            margin-top: 0px;
            display: inline-block;
        }
        #dates_from, #dates_to {
            width: 100px;
            display: inline-block;
        }
        .date__search--text {
            margin-top: 1em !important;
            margin-bottom: 0.5em;
        }
    </style>
    <link href='/fullcalendar/core/main.css' rel='stylesheet' />
    <link href='/fullcalendar/daygrid/main.css' rel='stylesheet' />
    <link href='/fullcalendar/timegrid/main.css' rel='stylesheet' />
    <link href='/fullcalendar/timeline/main.css' rel='stylesheet' />
    <link href='/fullcalendar/resource-timeline/main.css' rel='stylesheet' />
    <script src='/fullcalendar/core/main.js'></script>
    <script src='/fullcalendar/interaction/main.js'></script>
    <script src='/fullcalendar/daygrid/main.js'></script>
    <script src='/fullcalendar/timegrid/main.js'></script>
    <script src='/fullcalendar/timeline/main.js'></script>
    <script src='/fullcalendar/resource-common/main.js'></script>
    <script src='/fullcalendar/resource-timeline/main.js'></script>
    <script src="/js/laroute.js"></script>
    <link href="{{ asset('css/vue/styles.css') }}" rel="stylesheet" />
@endsection

@section('page_header')
    @yield('app-header')
@endsection

@section('content')
    <div class="page-content" id="vue">
        @include('voyager::alerts')
        @include('voyager::dimmers')
        @yield('app-content')
        <label-scheduler-await-user
                :user-id="{{ Auth::id() }}"
        />
    </div>
@endsection

@section('javascript')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ URL::asset('js/ckeditor.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('#start_date').datepicker({dateFormat: 'yy-mm-dd'});
            $('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
            $('.default-date-time-picker-now').datetimepicker({
                sideBySide: true,
                format: "YYYY-MM-DD H:mm"
            });
            $('.default-date-picker-now').datetimepicker({
                format: "YYYY-MM-DD",
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/locale/pl.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/vue-chunk.js') }}"></script>
    <script src="{{ asset('js/vue-scripts.js') }}"></script>
    @yield('scripts')
@endsection

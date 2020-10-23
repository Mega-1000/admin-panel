@extends('voyager::master')
@section('css')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">
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
    </style>
    <link href='/fullcalendar/core/main.css' rel='stylesheet' />
    <link href='/fullcalendar/daygrid/main.css' rel='stylesheet' />
    <link href='/fullcalendar/timegrid/main.css' rel='stylesheet' />
    <link href='/fullcalendar/timeline/main.css' rel='stylesheet' />
    <link href='/fullcalendar/resource-timeline/main.css' rel='stylesheet' />
    {{-- Rollbar script --}}
    <script>
        var _rollbarConfig = {
            accessToken: "41a430ad32e549bc92e2f5c63bd36917",
            captureUncaught: true,
            captureUnhandledRejections: true,
            payload: {
                environment: "production"
            }
        };
        // Rollbar Snippet
        !function(r){var e={};function o(n){if(e[n])return e[n].exports;var t=e[n]={i:n,l:!1,exports:{}};return r[n].call(t.exports,t,t.exports,o),t.l=!0,t.exports}o.m=r,o.c=e,o.d=function(r,e,n){o.o(r,e)||Object.defineProperty(r,e,{enumerable:!0,get:n})},o.r=function(r){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(r,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(r,"__esModule",{value:!0})},o.t=function(r,e){if(1&e&&(r=o(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var t in r)o.d(n,t,function(e){return r[e]}.bind(null,t));return n},o.n=function(r){var e=r&&r.__esModule?function(){return r.default}:function(){return r};return o.d(e,"a",e),e},o.o=function(r,e){return Object.prototype.hasOwnProperty.call(r,e)},o.p="",o(o.s=0)}([function(r,e,o){"use strict";var n=o(1),t=o(5);_rollbarConfig=_rollbarConfig||{},_rollbarConfig.rollbarJsUrl=_rollbarConfig.rollbarJsUrl||"https://cdn.rollbar.com/rollbarjs/refs/tags/v2.19.4/rollbar.min.js",_rollbarConfig.async=void 0===_rollbarConfig.async||_rollbarConfig.async;var a=n.setupShim(window,_rollbarConfig),l=t(_rollbarConfig);window.rollbar=n.Rollbar,a.loadFull(window,document,!_rollbarConfig.async,_rollbarConfig,l)},function(r,e,o){"use strict";var n=o(2),t=o(3);function a(r){return function(){try{return r.apply(this,arguments)}catch(r){try{console.error("[Rollbar]: Internal error",r)}catch(r){}}}}var l=0;function i(r,e){this.options=r,this._rollbarOldOnError=null;var o=l++;this.shimId=function(){return o},"undefined"!=typeof window&&window._rollbarShims&&(window._rollbarShims[o]={handler:e,messages:[]})}var s=o(4),d=function(r,e){return new i(r,e)},c=function(r){return new s(d,r)};function u(r){return a((function(){var e=this,o=Array.prototype.slice.call(arguments,0),n={shim:e,method:r,args:o,ts:new Date};window._rollbarShims[this.shimId()].messages.push(n)}))}i.prototype.loadFull=function(r,e,o,n,t){var l=!1,i=e.createElement("script"),s=e.getElementsByTagName("script")[0],d=s.parentNode;i.crossOrigin="",i.src=n.rollbarJsUrl,o||(i.async=!0),i.onload=i.onreadystatechange=a((function(){if(!(l||this.readyState&&"loaded"!==this.readyState&&"complete"!==this.readyState)){i.onload=i.onreadystatechange=null;try{d.removeChild(i)}catch(r){}l=!0,function(){var e;if(void 0===r._rollbarDidLoad){e=new Error("rollbar.js did not load");for(var o,n,a,l,i=0;o=r._rollbarShims[i++];)for(o=o.messages||[];n=o.shift();)for(a=n.args||[],i=0;i<a.length;++i)if("function"==typeof(l=a[i])){l(e);break}}"function"==typeof t&&t(e)}()}})),d.insertBefore(i,s)},i.prototype.wrap=function(r,e,o){try{var n;if(n="function"==typeof e?e:function(){return e||{}},"function"!=typeof r)return r;if(r._isWrap)return r;if(!r._rollbar_wrapped&&(r._rollbar_wrapped=function(){o&&"function"==typeof o&&o.apply(this,arguments);try{return r.apply(this,arguments)}catch(o){var e=o;throw e&&("string"==typeof e&&(e=new String(e)),e._rollbarContext=n()||{},e._rollbarContext._wrappedSource=r.toString(),window._rollbarWrappedError=e),e}},r._rollbar_wrapped._isWrap=!0,r.hasOwnProperty))for(var t in r)r.hasOwnProperty(t)&&(r._rollbar_wrapped[t]=r[t]);return r._rollbar_wrapped}catch(e){return r}};for(var p="log,debug,info,warn,warning,error,critical,global,configure,handleUncaughtException,handleAnonymousErrors,handleUnhandledRejection,captureEvent,captureDomContentLoaded,captureLoad".split(","),f=0;f<p.length;++f)i.prototype[p[f]]=u(p[f]);r.exports={setupShim:function(r,e){if(r){var o=e.globalAlias||"Rollbar";if("object"==typeof r[o])return r[o];r._rollbarShims={},r._rollbarWrappedError=null;var l=new c(e);return a((function(){e.captureUncaught&&(l._rollbarOldOnError=r.onerror,n.captureUncaughtExceptions(r,l,!0),e.wrapGlobalEventHandlers&&t(r,l,!0)),e.captureUnhandledRejections&&n.captureUnhandledRejections(r,l,!0);var a=e.autoInstrument;return!1!==e.enabled&&(void 0===a||!0===a||"object"==typeof a&&a.network)&&r.addEventListener&&(r.addEventListener("load",l.captureLoad.bind(l)),r.addEventListener("DOMContentLoaded",l.captureDomContentLoaded.bind(l))),r[o]=l,l}))()}},Rollbar:c}},function(r,e,o){"use strict";function n(r,e,o,n){r._rollbarWrappedError&&(n[4]||(n[4]=r._rollbarWrappedError),n[5]||(n[5]=r._rollbarWrappedError._rollbarContext),r._rollbarWrappedError=null);var t=e.handleUncaughtException.apply(e,n);o&&o.apply(r,n),"anonymous"===t&&(e.anonymousErrorsPending+=1)}r.exports={captureUncaughtExceptions:function(r,e,o){if(r){var t;if("function"==typeof e._rollbarOldOnError)t=e._rollbarOldOnError;else if(r.onerror){for(t=r.onerror;t._rollbarOldOnError;)t=t._rollbarOldOnError;e._rollbarOldOnError=t}e.handleAnonymousErrors();var a=function(){var o=Array.prototype.slice.call(arguments,0);n(r,e,t,o)};o&&(a._rollbarOldOnError=t),r.onerror=a}},captureUnhandledRejections:function(r,e,o){if(r){"function"==typeof r._rollbarURH&&r._rollbarURH.belongsToShim&&r.removeEventListener("unhandledrejection",r._rollbarURH);var n=function(r){var o,n,t;try{o=r.reason}catch(r){o=void 0}try{n=r.promise}catch(r){n="[unhandledrejection] error getting `promise` from event"}try{t=r.detail,!o&&t&&(o=t.reason,n=t.promise)}catch(r){}o||(o="[unhandledrejection] error getting `reason` from event"),e&&e.handleUnhandledRejection&&e.handleUnhandledRejection(o,n)};n.belongsToShim=o,r._rollbarURH=n,r.addEventListener("unhandledrejection",n)}}}},function(r,e,o){"use strict";function n(r,e,o){if(e.hasOwnProperty&&e.hasOwnProperty("addEventListener")){for(var n=e.addEventListener;n._rollbarOldAdd&&n.belongsToShim;)n=n._rollbarOldAdd;var t=function(e,o,t){n.call(this,e,r.wrap(o),t)};t._rollbarOldAdd=n,t.belongsToShim=o,e.addEventListener=t;for(var a=e.removeEventListener;a._rollbarOldRemove&&a.belongsToShim;)a=a._rollbarOldRemove;var l=function(r,e,o){a.call(this,r,e&&e._rollbar_wrapped||e,o)};l._rollbarOldRemove=a,l.belongsToShim=o,e.removeEventListener=l}}r.exports=function(r,e,o){if(r){var t,a,l="EventTarget,Window,Node,ApplicationCache,AudioTrackList,ChannelMergerNode,CryptoOperation,EventSource,FileReader,HTMLUnknownElement,IDBDatabase,IDBRequest,IDBTransaction,KeyOperation,MediaController,MessagePort,ModalWindow,Notification,SVGElementInstance,Screen,TextTrack,TextTrackCue,TextTrackList,WebSocket,WebSocketWorker,Worker,XMLHttpRequest,XMLHttpRequestEventTarget,XMLHttpRequestUpload".split(",");for(t=0;t<l.length;++t)r[a=l[t]]&&r[a].prototype&&n(e,r[a].prototype,o)}}},function(r,e,o){"use strict";function n(r,e){this.impl=r(e,this),this.options=e,function(r){for(var e=function(r){return function(){var e=Array.prototype.slice.call(arguments,0);if(this.impl[r])return this.impl[r].apply(this.impl,e)}},o="log,debug,info,warn,warning,error,critical,global,configure,handleUncaughtException,handleAnonymousErrors,handleUnhandledRejection,_createItem,wrap,loadFull,shimId,captureEvent,captureDomContentLoaded,captureLoad".split(","),n=0;n<o.length;n++)r[o[n]]=e(o[n])}(n.prototype)}n.prototype._swapAndProcessMessages=function(r,e){var o,n,t;for(this.impl=r(this.options);o=e.shift();)n=o.method,t=o.args,this[n]&&"function"==typeof this[n]&&("captureDomContentLoaded"===n||"captureLoad"===n?this[n].apply(this,[t[0],o.ts]):this[n].apply(this,t));return this},r.exports=n},function(r,e,o){"use strict";r.exports=function(r){return function(e){if(!e&&!window._rollbarInitialized){for(var o,n,t=(r=r||{}).globalAlias||"Rollbar",a=window.rollbar,l=function(r){return new a(r)},i=0;o=window._rollbarShims[i++];)n||(n=o.handler),o.handler._swapAndProcessMessages(l,o.messages);window[t]=n,window._rollbarInitialized=!0}}}}]);
        // End Rollbar Snippet
    </script>

    <script src='/fullcalendar/core/main.js'></script>
    <script src='/fullcalendar/interaction/main.js'></script>
    <script src='/fullcalendar/daygrid/main.js'></script>
    <script src='/fullcalendar/timegrid/main.js'></script>
    <script src='/fullcalendar/timeline/main.js'></script>
    <script src='/fullcalendar/resource-common/main.js'></script>
    <script src='/fullcalendar/resource-timeline/main.js'></script>
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
    @yield('scripts')
@endsection

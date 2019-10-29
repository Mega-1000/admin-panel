@extends('layouts.app')

@section('app-header')
    <style>
        .modal-dialog {
            width: 1000px;
        }
    </style>
    <div class="form-group" style="display:inline-block;">
        <h1 class="page-title">
            <i class="voyager-calendar"></i> @lang('timetable.title')
        </h1>
    </div>
    <div class="form-group" style="display:inline-block;">
        <select class="form-control" id="selectWarehouse">
            @foreach($warehouses as $warehouse)
                <option id="warehouseSelect" value="{{$warehouse->id}}">{{$warehouse->symbol}}</option>
            @endforeach
        </select>
    </div>
@endsection
@section('content')
    @php
        if($viewType == null){
            $viewType = Request()->view_type;
            $activeDay = Request()->active_start;
        }
    @endphp
    <div class="modal fade" tabindex="-1" id="addStorekeeperTimeError" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas pobierania listy pracowników</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="errorUpdate" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas akceptacji zadania</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="errorGetToUpdate" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wystąpił błąd podczas pobierania danych zadania</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="addStorekeeperTime" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Poniżej znajduje się lista pracowników: </h4>
                </div>
                <div class="modal-body">
                    <form id="workHours" action="{{ action('UserWorksController@addWorkHours') }}" method="POST">
                        @csrf
                        <table style="width: 100%;" id="storekeepers" class="table-active">
                            <thead>
                            <tr>
                                <th>Lp.</th>
                                <th>Nazwa</th>
                                <th>Imię</th>
                                <th>Nazwisko</th>
                                <th>Godzina rozpoczęcia</th>
                                <th>Godzina zakończenia</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="4"><span style="color:red">UWAGA: Jeśli chcesz zmienić daty dla wszystkich pracowników uzupełnij komórki na samej górze.</span>
                                </td>
                                <td><input class="form-control" type="time" name="start" id="start"></td>
                                <td><input class="form-control" type="time" name="end" id="end"></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="workHours" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="addNewTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Dodaj zadanie</h4>
                </div>
                <div class="modal-body">
                    <form id="addTask" action="{{ action('TasksController@addNewTask') }}" method="POST">
                        @csrf
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="addTask" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="updateTaskModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zaktualizuj zadanie</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="updateTaskDetail" name="update" value="1"
                            class="btn btn-success pull-right">Wyślij
                    </button>
                    <button type="submit" form="updateTaskDetail" name="delete" value="1"
                            class="btn btn-danger pull-right">Usuń
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="editTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zaktualizuj zadanie</h4>
                </div>
                <div class="modal-body">
                    <form id="updateTask" method="POST">
                        {{method_field('put')}}
                        @csrf
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="updateTask" class="btn btn-success pull-right">Wyślij</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="acceptTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Zadania oczekujące akceptacji</h4>
                    <span>Jeśli chcesz akceptować zadanie kliknij - AKCEPTUJ.</span>
                    <span>W przypadku odrzucenia wykonania zadania należy kliknąć - ODRZUĆ. Poskutkuje to tym, że zadanie zostanie przeniesione na kolejny dzień, na tą samą godzinę, jeśli chcesz aby było wykonane w innych godzinach, prosimy o zmianę godziny.</span>
                </div>
                <div class="modal-body">

                    <table style="width: 100%;" id="tasksToAccept" class="table-active">
                        <thead>
                        <tr>
                            <th width="5%">Lp.</th>
                            <th width="5%">Nazwa</th>
                            <th width="15%">Godzina rozpoczęcia</th>
                            <th width="15%">Godzina zakończenia</th>
                            <th width="30%">Status</th>
                            <th width="5%">Akceptuj</th>
                            <th width="5%">Odrzuć</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="moveTask" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form id="moveTaskForm" action="" method="POST">
                        {{method_field('put')}}
                        @csrf
                        <input type="hidden" name="old_resource">
                        <input type="hidden" name="new_resource">
                        <input type="hidden" name="view_type" id="view_type">
                        <input type="hidden" name="active_start" id="active_start">
                        <input type="hidden" name="start">
                        <input type="hidden" name="end">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="moveTaskForm" class="btn btn-success pull-right" name="move" value="1">
                        Przesuń
                    </button>
                    <button type="submit" form="moveTaskForm" class="btn btn-success pull-right" name="moveAllRight"
                            value="1">Przesuń wszystkie w prawo
                    </button>
                    <button type="submit" form="moveTaskForm" class="btn btn-success pull-right" name="moveAllLeft"
                            value="1">Przesuń wszystkie w lewo
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="status_move">
@endsection

@section('javascript')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $(document).tooltip();
        });
        let breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/planning/timetable'>Planowanie pracy</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Terminarz</a></li>");
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid', 'resourceTimeline'],
                now: '{{$activeDay != null ? $activeDay : new Carbon\Carbon()}}',
                editable: true,
                aspectRatio: 1.8,
                scrollTime: '7:00',
                slotDuration: '0:05',
                timeZone: 'UTC',
                minTime: "07:00:00",
                maxTime: "20:00:00",
                locale: 'PL',
                titleFormat: {year: 'numeric', month: 'long', day: '2-digit'},
                buttonText: {
                    today: 'Dzisiaj',
                    month: 'Miesiąc',
                    week: 'Tydzień',
                    day: 'Dzień',
                    days: 'Lista',
                    resourceTimelineThreeDays: '3 Dni'
                },
                slotLabelFormat: [
                    {day: '2-digit', month: 'long', year: 'numeric'},
                    {weekday: 'long'},
                    {hour: '2-digit', minute: '2-digit'}
                ],
                header: {
                    left: 'promptResource today prev,next',
                    center: 'title',
                    right: 'resourceTimelineDay,resourceTimelineThreeDays,timeGridWeek,dayGridMonth'
                },
                slotWidth: 15,
                resourceAreaWidth: "10%",
                customButtons: {
                    promptResource: {
                        text: 'Dodaj godziny pracy',
                        click: function () {
                            let warehouse = null;
                            if ($('#warehouseSelect').is(':selected')) {
                                warehouse = $('#warehouseSelect').val();
                            }
                            $.ajax({
                                url: '/admin/planning/timetable/' + warehouse + '/getStorekeepersToModal',
                            }).done(function (data) {
                                $('#addStorekeeperTime').modal();
                                let i = 1;
                                if ($('#storekeepers > tbody > tr').length === 1) {
                                    $.each(data, function (index, value) {
                                        let html = '<tr class="appendRow">';
                                        html += '<td>' + i + '</td>';
                                        html += '<td>' + value.name + '</td>';
                                        html += '<td>' + value.firstname + '</td>';
                                        html += '<td>' + value.lastname + '</td>';
                                        html += '<td><input class="form-control start" type="time" name="start[' + value.id + ']" id="start[' + value.id + ']" value="' + value.user_works[0].start + '"></td>';
                                        html += '<td><input class="form-control end" type="time" name="end[' + value.id + ']" id="end[' + value.id + ']" value="' + value.user_works[0].end + '"></td>';
                                        html += '</tr>';
                                        i++;
                                        $('#storekeepers > tbody').append(html);

                                    });
                                }
                            }).fail(function () {
                                $('#addStorekeeperTimeError').modal();
                            });
                        }
                    }
                },
                dateClick: function (info) {
                    if (info.view.type !== 'timeGridWeek' && info.view.type !== 'dayGridMonth') {
                        $('#addNewTask').modal();
                        let startDate = new Date(info.dateStr);
                        let firstDate = new Date(startDate.setHours(startDate.getHours() - 2));
                        let startMinutes = firstDate.getMinutes();
                        if (startMinutes < 10) {
                            startMinutes = '0' + startMinutes;
                        }
                        let dateTime = firstDate.getFullYear() + '-' + ('0' + (firstDate.getMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                        let newDate = new Date(info.dateStr);
                        let endDate = new Date(newDate.setHours(newDate.getHours() + 1 - 2));
                        let minutes = endDate.getMinutes();
                        if (minutes < 10) {
                            minutes = '0' + minutes;
                        }
                        let dateTimeEnd = endDate.getFullYear() + '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                        let warehouse = null;
                        if ($('#warehouseSelect').is(':selected')) {
                            warehouse = $('#warehouseSelect').val();
                        }
                        let taskGroup = $('#task-group');

                        let html = '';
                        html += '<div id="task-group">'
                        html += '<div class="form-group">';
                        html += '<label for="title">Nazwa</label>';
                        html += '<input type="text" name="title" id="title" value="' + info.resource.title + '" class="form-control" disabled>';
                        html += '<input type="hidden" name="user_id" id="user_id" value="' + info.resource.id + '">';
                        html += '<input type="hidden" name="warehouse_id" id="user_id" value="' + warehouse + '">';
                        html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                        html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="name">Nazwa zadania</label>';
                        html += '<input type="text" name="name" id="name" class="form-control" required>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="start_new">Godzina rozpoczęcia</label>';
                        html += '<input type="text" name="start" id="start_new" class="form-control default-date-time-picker-now" value="' + dateTime + '">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="end">Godzina zakończenia</label>';
                        html += '<input type="text" name="end" id="end" class="form-control default-date-time-picker-now" value="' + dateTimeEnd + '">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="color-green">';
                        html += '<input type="radio" name="color" id="color-green" value="#32CD32" required> ';
                        html += 'Zielony(wyprodukowane)</label>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="color-blue">';
                        html += '<input type="radio" name="color" id="color-blue" value="#194775" checked="checked" required> ';
                        html += 'Niebieski(dopuszczalne przesunięcie terminu dostawy)</label>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="color-yellow">';
                        html += '<input type="radio" name="color" id="color-yellow" value="#E6C74D" required> ';
                        html += 'Żółty(PILNE - prośba o wysłanie we wskazanym terminie)</label>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="color-red">';
                        html += '<input type="radio" name="color" id="color-red" value="#FF0000" required> ';
                        html += 'Czerwony(awaria koniecznie to wysłać dzisiaj)</label>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="color-violet">';
                        html += '<input type="radio" name="color" id="color-violet" value="#9966CC" required> ';
                        html += 'Fioletowy(zamówienie MEGA-OLAWA)</label>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="consultant_value">Koszt obsługi konsultanta</label>';
                        html += '<input type="number" name="consultant_value" id="consultant_value" class="form-control">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="consultant_notice">Opis obsługi konsultanta</label>';
                        html += '<textarea rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice" class="form-control"></textarea>';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="warehouse_value">Koszt obsługi magazynu</label>';
                        html += '<input type="number" name="warehouse_value" id="warehouse_value" class="form-control">';
                        html += '</div>';
                        html += '<div class="form-group">';
                        html += '<label for="warehouse_notice">Opis obsługi magazynu</label>';
                        html += '<textarea rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice" class="form-control"></textarea>';
                        html += '</div>';
                        html += '</div>';
                        if (taskGroup.length !== 0) {
                            taskGroup.remove();
                        }
                        $('#addTask').append(html);
                        $('.default-date-time-picker-now').datetimepicker({
                            sideBySide: true,
                            format: "YYYY-MM-DD H:mm",
                            stepping: 5
                        });
                        $('#name').val((endDate.getUTCDate() + '-' + ('0' + (endDate.getMonth() + 1)).slice(-2)) + ' - ' + $('#warehouse_value').val());
                        $(document).on('focusout', '.default-date-time-picker-now', function () {
                            var dateObj = new Date($('#start_new').val());
                            var month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
                            var day = dateObj.getUTCDate();
                            $('#name').val(day + '-' + month + ' - ' + $('#warehouse_value').val());
                        });
                        $('#warehouse_value').change(function () {
                            var dateObj = new Date($('#start_new').val());
                            var month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
                            var day = dateObj.getUTCDate();
                            $('#name').val(day + '-' + month + ' - ' + $('#warehouse_value').val());
                        });
                    }
                },
                defaultView: '{{$viewType != null ? $viewType : 'resourceTimelineDay'}}',
                views: {
                    resourceTimelineThreeDays: {
                        type: 'resourceTimeline',
                        duration: {days: 3},
                        buttonText: '3 days'
                    }
                },
                resourceLabelText: 'Magazynierzy',
                resourceRender: function (arg) {
                    let resource = arg.resource.id;

                    arg.el.addEventListener('click', function () {
                        $.ajax({
                            type: "GET",
                            url: '/admin/planning/tasks/16/getTasksForUser/' + resource,
                        }).done(function (data) {
                            $('#acceptTask').modal();
                            $.each(data, function (index, value) {
                                if (value.status === 'WAITING_FOR_ACCEPT') {
                                    let startDate = new Date(value.start);
                                    let firstDate = new Date(startDate.setHours(startDate.getHours() - 2));
                                    let startMinutes = firstDate.getMinutes();
                                    if (startMinutes < 10) {
                                        startMinutes = '0' + startMinutes;
                                    }
                                    let dateTime = firstDate.getFullYear() + '-' + ('0' + (firstDate.getMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                                    let newDate = new Date(value.end);
                                    let endDate = new Date(newDate.setHours(newDate.getHours() - 2));
                                    let minutes = endDate.getMinutes();
                                    if (minutes < 10) {
                                        minutes = '0' + minutes;
                                    }
                                    let dateTimeEnd = endDate.getFullYear() + '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                                    let html = '<tr class="appendRow">';
                                    html += '<td>' + value.id + '</td>';
                                    html += '<td>' + value.title + '</td>';
                                    html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                                    html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                                    html += '<td><input class="form-control date_start" type="text" name="date_start[' + value.id + ']" id="date_start[' + value.id + ']" value="' + dateTime + '"></td>';
                                    html += '<td><input class="form-control date_end" type="text" name="date_end[' + value.id + ']" id="date_end[' + value.id + ']" value="' + dateTimeEnd + '"></td>';
                                    html += '<td><input class="form-control status" type="text" name="status[' + value.id + ']" id="status[' + value.id + ']" value="Oczekuje na akceptację" disabled></td>';
                                    html += '<td><button type="button" class="btn btn-success" onclick="acceptTask(' + value.id + ')">Akceptuj</button></td>'
                                    html += '<td><button type="button" class="btn btn-danger" onclick="rejectTask(' + value.id + ')">Odrzuć</button></td>'
                                    html += '</tr>';
                                    $('#tasksToAccept > tbody').append(html);
                                }
                            });

                        }).fail(function () {
                            return false;
                        });
                    });
                },
                resources: {
                    url: '/admin/planning/timetable/16/getStorekeepers',
                    method: 'GET'
                },
                events: {
                    url: '/admin/planning/tasks/16/getTasks',
                    method: 'GET'
                },
                eventRender: function (event) {

                    let id;
                    if (event.event.extendedProps.customOrderId !== null) {
                        id = event.event.extendedProps.customOrderId;
                    } else {
                        id = event.event.extendedProps.customTaskId;
                    }
                    $(event.el).attr('id', id);

                },
                eventMouseEnter: function (info) {
                    let startDate = new Date(info.event.start);
                    let firstDate = new Date(startDate.setHours(startDate.getHours() - 2));
                    let startMinutes = firstDate.getMinutes();
                    if (startMinutes < 10) {
                        startMinutes = '0' + startMinutes;
                    }
                    let newDate = new Date(info.event.end);
                    let endDate = new Date(newDate.setHours(newDate.getHours() - 2));
                    let minutes = endDate.getMinutes();
                    if (minutes < 10) {
                        minutes = '0' + minutes;
                    }

                    $(info.el).attr('title', info.event.extendedProps.text);
                },
                eventDrop: function (info) {
                    let startDate = new Date(info.event.start);
                    let firstDate = new Date(startDate.setHours(startDate.getHours() - 2));
                    let startMinutes = firstDate.getMinutes();
                    if (startMinutes < 10) {
                        startMinutes = '0' + startMinutes;
                    }
                    let dateTime = firstDate.getFullYear() + '-' + ('0' + (firstDate.getMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                    let newDate = new Date(info.event.end);
                    let endDate = new Date(newDate.setHours(newDate.getHours() - 2));
                    let minutes = endDate.getMinutes();
                    if (minutes < 10) {
                        minutes = '0' + minutes;
                    }
                    let dateTimeEnd = endDate.getFullYear() + '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                    $('input[name="view_type"]').val(info.view.type);
                    $('input[name="active_start"]').val(info.view.activeStart);
                    $('#moveTaskForm').attr('action', '/admin/planning/tasks/' + info.event.id + '/moveTask');
                    if (info.newResource !== null) {
                        $('input[name="old_resource"]').val(info.oldResource.id);
                        $('input[name="new_resource"]').val(info.newResource.id);
                        $('#moveTask > div > div > div.modal-header > h4').text('Czy jesteś pewny że chcesz zabrać zadanie użytkownikowi: ' + info.oldResource.title + ', i dodać go użytkownikowi: ' + info.newResource.title + '?')
                    } else {
                        $('#moveTask > div > div > div.modal-header > h4').text('Czy chcesz przesunąć to zadanie?')
                    }
                    $('input[name="start"]').val(dateTime);
                    $('input[name="end"]').val(dateTimeEnd);
                    $('#moveTask').modal();
                },
                eventResize: function (info) {
                    $('#editTask').modal();
                    let startDate = new Date(info.event.start);
                    let firstDate = new Date(startDate.setHours(startDate.getHours() - 2));
                    let startMinutes = firstDate.getMinutes();
                    if (startMinutes < 10) {
                        startMinutes = '0' + startMinutes;
                    }
                    let dateTime = firstDate.getFullYear() + '-' + ('0' + (firstDate.getMonth() + 1)).slice(-2) + '-' + firstDate.getUTCDate() + ' ' + firstDate.getHours() + ':' + startMinutes;
                    let newDate = new Date(info.event.end);
                    let endDate = new Date(newDate.setHours(newDate.getHours() - 2));
                    let minutes = endDate.getMinutes();
                    if (minutes < 10) {
                        minutes = '0' + minutes;
                    }
                    let dateTimeEnd = endDate.getFullYear() + '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' + endDate.getUTCDate() + ' ' + endDate.getHours() + ':' + minutes;
                    let warehouse = null;
                    if ($('#warehouseSelect').is(':selected')) {
                        warehouse = $('#warehouseSelect').val();
                    }
                    let taskGroup = $('#task-group-edit');
                    $('#updateTask').attr('action', '/admin/planning/tasks/' + info.event.id + '/updateTaskTime');
                    let html = '';
                    html += '<div id="task-group-edit">'
                    html += '<div class="form-group">';
                    html += '<label for="title">Nazwa zadania</label>';
                    html += '<input type="text" name="title" id="title" value="' + info.event.title + '" class="form-control" disabled>';
                    html += '<input type="hidden" name="task_id" id="task_id" value="' + info.event.id + '" class="form-control">';
                    html += '<input type="hidden" name="warehouse_id" id="user_id" value="' + warehouse + '" class="form-control">';
                    html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                    html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                    html += '</div>';
                    html += '<div class="form-group">';
                    html += '<label for="start">Godzina rozpoczęcia</label>';
                    html += '<input type="text" name="start" id="start" class="form-control default-date-time-picker-now" value="' + dateTime + '">';
                    html += '</div>';
                    html += '<div class="form-group">';
                    html += '<label for="end">Godzina zakończenia</label>';
                    html += '<input type="text" name="end" id="end" class="form-control default-date-time-picker-now" value="' + dateTimeEnd + '">';
                    html += '</div>';
                    html += '</div>';
                    if (taskGroup.length !== 0) {
                        taskGroup.remove();
                    }
                    $('#updateTask').append(html);
                    $('.default-date-time-picker-now').datetimepicker({
                        sideBySide: true,
                        format: "YYYY-MM-DD H:mm",
                        stepping: 5
                    });
                },
                eventClick: function (info) {
                    let warehouse = null;
                    if ($('#warehouseSelect').is(':selected')) {
                        warehouse = $('#warehouseSelect').val();
                    }
                    $.ajax({
                        url: '/admin/planning/tasks/' + info.event.id + '/getTask',
                        type: "GET"
                    }).done(function (data) {
                        $('#updateTaskModal').modal();
                        let form = $('#updateTaskDetail').length;
                        if (form === 0) {
                            let html = '';
                            html += '<form id="updateTaskDetail" action="/admin/planning/tasks/' + info.event.id + '/updateTask" method="POST">';
                            html += '{{method_field('put')}}';
                            html += '{{csrf_field()}}';
                            html += '<div id="task-group">'
                            html += '<div class="form-group">';
                            html += '<label for="title">Nazwa</label>';
                            html += '<input type="text" name="title" id="title" value="' + data.user.name + '" class="form-control" disabled>';
                            html += '<input type="hidden" name="user_id" id="user_id" value="' + data.user.id + '" class="form-control">';
                            html += '<input type="hidden" name="warehouse_id" id="user_id" value="' + warehouse + '" class="form-control">';
                            html += '<input type="hidden" name="view_type" id="view_type" value="' + info.view.type + '">';
                            html += '<input type="hidden" name="active_start" id="active_start" value="' + info.view.activeStart + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="name">Nazwa zadania</label>';
                            html += '<input type="text" name="name" id="name" class="form-control" required value="' + info.event.title + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="start">Godzina rozpoczęcia</label>';
                            html += '<input type="text" name="start" id="start" class="form-control default-date-time-picker-now" value="' + data.task_time.date_start + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="end">Godzina zakończenia</label>';
                            html += '<input type="text" name="end" id="end" class="form-control default-date-time-picker-now" value="' + data.task_time.date_end + '">';
                            html += '</div>';
                            if (data.order !== undefined && data.order !== null) {
                                html += '</div>';
                                html += '<div class="form-group">';
                                html += '<label for="shipment_date">Data rozpoczęcia nadawania przesyłki</label>';
                                html += '<input type="text" name="shipment_date" id="shipment_date" class="form-control default-date-picker-now" value="' + data.order.shipment_date + '">';
                                html += '</div>';
                            }
                            html += '<div class="form-group">';
                            html += '<label for="color-dark-green">';
                            html += '<input type="radio" name="color" id="color-dark-green" value="#008000" required> ';
                            html += 'Ciemnozielony(towar wydany)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-green">';
                            html += '<input type="radio" name="color" id="color-green" value="#32CD32" required> ';
                            html += 'Zielony(wyprodukowane)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-blue">';
                            html += '<input type="radio" name="color" id="color-blue" value="#194775" checked="checked" required> ';
                            html += 'Niebieski(dopuszczalne przesunięcie terminu dostawy)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-yellow">';
                            html += '<input type="radio" name="color" id="color-yellow" value="#E6C74D" required> ';
                            html += 'Żółty(PILNE - prośba o wysłanie we wskazanym terminie)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-red">';
                            html += '<input type="radio" name="color" id="color-red" value="#FF0000" required> ';
                            html += 'Czerwony(awaria koniecznie to wysłać dzisiaj)</label>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="color-violet">';
                            html += '<input type="radio" name="color" id="color-violet" value="#9966CC" required> ';
                            html += 'Fioletowy(zamówienie MEGA-OLAWA)</label>';
                            html += '</div>';
                            let consultant_value;
                            let consultant_notice;
                            let warehouse_value;
                            let warehouse_notice
                            if (data.task_salary_detail == null) {
                                consultant_value = '';
                                consultant_notice = '';
                                warehouse_value = '';
                                warehouse_notice = '';
                            } else {
                                consultant_value = data.task_salary_detail.consultant_value;
                                consultant_notice = data.task_salary_detail.consultant_notice;
                                warehouse_value = data.task_salary_detail.warehouse_value;
                                warehouse_notice = data.task_salary_detail.warehouse_notice;
                                if (consultant_notice == null) {
                                    consultant_notice = '';
                                }
                                if (warehouse_notice == null) {
                                    warehouse_notice = '';
                                }
                            }
                            html += '<div class="form-group">';
                            html += '<label for="consultant_value">Koszt obsługi konsultanta</label>';
                            html += '<input type="number" name="consultant_value" id="consultant_value" class="form-control" value="' + consultant_value + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="consultant_notice">Opis obsługi konsultanta</label>';
                            html += '<textarea rows="5" cols="40" type="text" name="consultant_notice" id="consultant_notice" class="form-control">' + consultant_notice + '</textarea>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="warehouse_value">Koszt obsługi magazynu</label>';
                            html += '<input type="number" name="warehouse_value" id="warehouse_value" class="form-control" value="' + warehouse_value + '">';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<label for="warehouse_notice">Opis obsługi magazynu</label>';
                            html += '<textarea rows="5" cols="40" type="text" name="warehouse_notice" id="warehouse_notice" class="form-control">' + warehouse_notice + '</textarea>';
                            html += '</div>';
                            html += '<div class="form-group">';
                            html += '<a class="btn btn-success" target="_blank" href="/admin/orders/' + data.order_id + '/edit">Przenieś mnie do edycji zlecenia</a>';
                            html += '<br><a class="btn btn-success" target="_blank" href="/admin/orders?order_id=' + data.order_id + '&planning=true">Przenieś mnie do zlecenia na liście zleceń</a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</form>';
                            $('#updateTaskModal > div > div > div.modal-body').append(html);
                            $('.default-date-time-picker-now').datetimepicker({
                                sideBySide: true,
                                format: "YYYY-MM-DD H:mm",
                                stepping: 5
                            });
                            $('.default-date-picker-now').datetimepicker({
                                sideBySide: true,
                                format: "YYYY-MM-DD",
                            });
                            if (info.event.backgroundColor === '#194775') {
                                $('#color-blue').attr('checked', true);
                            } else if (info.event.backgroundColor === '#E7A954') {
                                $('#color-orange').attr('checked', true);
                            } else if (info.event.backgroundColor === '#E6C74D') {
                                $('#color-yellow').attr('checked', true);
                            } else if (info.event.backgroundColor === '#32CD32') {
                                $('#color-green').attr('checked', true);
                            } else if (info.event.backgroundColor === '#FF0000') {
                                $('#color-red').attr('checked', true);
                            } else if (info.event.backgroundColor === '#008000') {
                                $('#color-dark-green').attr('checked', true);
                            } else if (info.event.backgroundColor === '#9966CC') {
                                $('#color-violet').attr('checked', true);
                            }
                        }
                    }).fail(function () {
                        $('#errorGetToUpdate').modal();
                    });
                },
                eventAllow: function (dropLocation, draggedEvent) {
                    return $.ajax({
                        type: "POST",
                        url: '/admin/planning/tasks/allowTaskMove',
                        data: {
                            id: draggedEvent.id,
                            start: dropLocation.startStr,
                            end: dropLocation.endStr,
                            user_id: dropLocation.resource.id
                        },
                    }).done(function (data) {
                        if (data === true) {
                            return true;
                        } else {
                            return false;
                        }
                    }).fail(function () {
                        return false;
                    });

                }
            });

            calendar.render();
            $('.fc-license-message').remove();

            $('#start').change(function () {
                $('.start').val($('#start').val());
            });
            $('#end').change(function () {
                $('.end').val($('#end').val());
            });

        });

        function acceptTask(id) {
            let dateStart = $('input[name="date_start[' + id + ']"]').val();
            let dateEnd = $('input[name="date_end[' + id + ']"]').val();
            let warehouse = null;
            if ($('#warehouseSelect').is(':selected')) {
                warehouse = $('#warehouseSelect').val();
            }
            $.ajax({
                type: "POST",
                url: '/admin/planning/tasks/acceptTask',
                data: {
                    id: id,
                    start: dateStart,
                    end: dateEnd,
                    status: 'TO_DO'
                }
            }).done(function (data) {
                if (data.status == 'TO_DO') {
                    $('input[name="status[' + data.id + ']"]').val('Zaakceptowano');
                }
            }).fail(function () {
                $('#errorUpdate').modal();
            });
        }

        function rejectTask(id) {
            let dateStart = $('input[name="date_start[' + id + ']"]').val();
            let dateEnd = $('input[name="date_end[' + id + ']"]').val();
            let warehouse = null;
            if ($('#warehouseSelect').is(':selected')) {
                warehouse = $('#warehouseSelect').val();
            }
            $.ajax({
                type: "POST",
                url: '/admin/planning/tasks/rejectTask',
                data: {
                    id: id,
                    start: dateStart,
                    end: dateEnd,
                    status: 'REJECTED'
                }
            }).done(function (data) {
                if (data.status == 'REJECTED' && data.message == null) {
                    $('input[name="status[' + data.id + ']"]').val('Odrzucono i przesunięto');
                } else {
                    $('input[name="status[' + data.id + ']"]').val(data.message);
                }
            }).fail(function () {
                $('#acceptTask').hide();
                $('#errorUpdate').modal();
            });
        }

        $('.modal').on('hidden.bs.modal', function () {
            location.reload();
        });
    </script>
    <script src="//cdn.jsdelivr.net/npm/jquery.scrollto@2.1.2/jquery.scrollTo.min.js"></script>
    <script>
        $(document).ready(function () {
            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = window.location.search.substring(1),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');

                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                    }
                }
            };
            var idFromUrl = getUrlParameter('id');
            var viewTypeFromUrl = getUrlParameter('view_type');
            var activeStartFromUrl = getUrlParameter('active_start');
            var activeEndFromUrl = getUrlParameter('active_end');
            if (idFromUrl !== undefined) {
                setTimeout(function () {
                    $('#' + idFromUrl).css('border', '3px solid rgb(96,2,1)');
                    $(".fc-scroller").animate({
                        scrollLeft: $('#' + idFromUrl).position().left - 600
                    }, 500);
                }, 1500);
            }
        });
    </script>
@endsection

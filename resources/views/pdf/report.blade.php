<html>
<head>
    <style>
        body {
            font-family: Calibri !important;
            font-size: 12px !important;
        }
    </style>
    <meta charset="UTF-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<h1 class="page-title">
    <i class="voyager-tag"></i> Raport {{$report->from}} - {{$report->to}}
</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container-fluid">

        @foreach($report->users as $user)
        <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th colspan="3">{{$user->firstname}} {{$user->lastname}}</th>
                        </tr>
                        <tr>
                            <th>Nazwa</th>
                            <th>Czas pracy</th>
                            <th>Kwota</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sum = 0;
                        @endphp
                        @foreach($report->properties->where('user_id', '=', $user->id) as $item)
                            @if($item != null)
                                <tr>
                                    <td><a target="_blank"
                                           href="{{env('APP_URL')}}/admin/planning/timetable?id={{$item->task->order_id != null ? 'taskOrder-'.$item->task->order_id : 'task-'.$item->task->id}}">{{$item->task->name}}</a>
                                    </td>
                                    <td>{{is_float($item->time_work) ? number_format($item->time_work, '2') : $item->time_work }}</td>
                                    <td>{{number_format($item->price,'2')}} zl</td>
                                </tr>
                                @php
                                    $sum += number_format($item->price,'2');
                                @endphp
                            @endif
                        @endforeach
                        <tr><td colspan="3">Raport wg. dni</td></tr>
                        @foreach($report->daily->where('user_id', '=', $user->id) as $dailyItem)
                            <tr>
                                <td colspan="2"><a target="_blank" href="/admin/planning/timetable?date={{$dailyItem->date}}">{{$dailyItem->date}}</a></td>
                                <td>{{number_format($dailyItem->price,'2')}} zl</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td><strong>Suma: {{number_format($sum,'2')}} zl</strong></td>
                        </tr>
                        </tbody>
                    </table>
            </div>
        </div>
        @endforeach

</div>
</body>
</html>

<html>
<head>
    <style>
        body {
            font-family: Verdana !important;
            font-size: 12px !important;
        }
    </style>
    <meta charset="UTF-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div>

    @if($report->user_id === null)
        <div class="title"><h2>Raport dla magazynu {{$report->warehouse->symbol}} z okresu {{$report->from}}
                - {{$report->to}} </h2> - wygenerowano: {{$date}}</div>
    @else
        <div class="title"><h2>Raport dla konsultanta {{$report->user->name}} z okresu {{$report->from}}
                - {{$report->to}} - wygenerowano: {{$date}} </h2></div>
    @endif
    <table class="table table-striped">
        <thead>
        <tr style="height:20px;">
            <th>Lp.</th>
            <th>Zadanie</th>
            <th>Data rozpoczecia</th>
            <th>Data zakonczenia</th>
            <th>Konsultant</th>
            <th>Magazyn</th>
            @if($report->user_id === null)
                <th>Koszt obslugi konsultanta</th>
            @else
                <th>Koszt obslugi magazynu</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @php
            $i = 0;
        @endphp
        @foreach($tasks as $task)
            {{$i++}}
            <tr>
                <td>{{$i}}</td>
                <td>{{$task->name}}</td>
                <td>{{$task->taskTime !== null ? $task->taskTime->date_start : ''}}</td>
                <td>{{$task->taskTime !== null ? $task->taskTime->date_end : ''}}</td>
                <td>{{$task->user->name}}</td>
                <td>{{$task->warehouse->symbol}}</td>
                @if($report->user_id === null)
                    <td>{{$task->taskSalaryDetail !== null ? $task->taskSalaryDetail->warehouse_value : ''}}</td>
                @else
                    <td>{{$task->taskSalaryDetail !== null ? $task->taskSalaryDetail->consultant_value : ''}}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="text-align: center;">
                Suma: {{$sum}}
            </th>
        </tr>
        </tfoot>
    </table>
</div>


</body>
</html>
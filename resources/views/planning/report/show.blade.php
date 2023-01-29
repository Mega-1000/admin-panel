@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Zobacz raport {{$report->from}} - {{$report->to}}
        <a style="margin-left: 10px;" href="{{ route('planning.reports.generatePdfReport', ['id' => $report->id]) }}"
           class="btn btn-info install pull-right">
            <span>Pobierz raport</span>
        </a>
        <a style="margin-left: 10px;" href="{{ route('planning.reports.index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('reports.list')</span>
        </a>
    </h1>

    <style>
        .tags {
            width: 100%;
        }

        .tag {
            width: 50%;
            float: right;
        }
    </style>
@endsection

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
    <div class="container-fluid">
        <div class="row">
            @foreach($report->users as $user)
                <div class="col-md-{{count($report->users) == 1 ? '12' : (count($report->users) == 2 ? '6' : '4')}}"
                     style="display:inline-block; float:left;">
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
                                               href="/admin/planning/timetable?id={{$item->task->order_id != null ? 'taskOrder-'.$item->task->order_id : 'task-'.$item->task->id}}">{{$item->task->name}}</a>
                                        </td>
                                        <td>{{strlen($item->time_work) != 11 ? number_format($item->time_work, '2') : $item->time_work }}</td>
                                        <td>{{number_format($item->price,'2')}} zł</td>
                                    </tr>
                                    @php
                                        $sum += number_format($item->price,'2');
                                    @endphp
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="3">Raport wg. dni</td>
                            </tr>
                            @foreach($report->daily->where('user_id', '=', $user->id) as $dailyItem)
                                <tr>
                                    <td colspan="2"><a target="_blank"
                                                       href="/admin/planning/timetable?date={{$dailyItem->date}}">{{$dailyItem->date}}</a>
                                    </td>
                                    <td>{{number_format($dailyItem->price,'2')}} zł</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td><strong>Suma: {{number_format($sum,'2')}} zł</strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/statuses/'>Raporty</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Zobacz</a></li>");
    </script>
@endsection

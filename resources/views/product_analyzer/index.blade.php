@extends('layouts.datatable')

@section('app-header')
<h1 class="page-title">
    <i class="voyager-money"></i> @lang('product_analyzer.title')
</h1>

@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>@lang('product_analyzer.table.product_name')</th>
            <th>@lang('product_analyzer.table.price')</th>
            <th>@lang('product_analyzer.table.date')</th>
        </tr>
        </thead>
    </table>
@endsection


@section('datatable-scripts')
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='javascript:void();'>@lang('product_analyzer.title')</a></li>");


        // DataTable
        let table = $('#dataTable').DataTable({
            language: {!! json_encode( __('voyager.datatable'), true) !!},
            processing: true,
            serverSide: true,
            "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "Wszystkie"]],
            columnDefs: [
                { className: "dt-center", targets: "_all" }
            ],
            dom: 'Bfrtip',
			buttons: [

			],
            order: [[0, "asc"]],
			ajax: {
				url: '{!! route('product_analyzer.datatable') !!}',
				type: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			},
            columns: [
                {
                    data: 'product_id',
                    name: 'product_id',
					render: function (data, type, row) {
						return row.product.name;
					}
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'analyze_date',
                    name: 'analyze_date'
                }
            ]
        });

        @foreach($visibilities as $key =>$row)

		var {{'show'.$row->name}}  = @json($row->show);
        {{'show'.$row->name}} = {{'show'.$row->name}}.map(function(x){
			return table.column(x+':name').index();
		});
        {{'show'.$row->name}} = {{'show'.$row->name}}.filter(function (el) {
			return el != null;
		});

		var {{'hidden'.$row->name}} = @json($row->hidden);
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.map(function(x){
			// if (typeof table.column(x+':name').index() === "number")
			return table.column(x+':name').index();
		});
        {{'hidden'.$row->name}} = {{'hidden'.$row->name}}.filter(function (el) {
			return el != null;
		});
		table.button().add({{1+$key}},{
			extend: 'colvisGroup',
			text: '{{$row->display_name}}',
			show: {{'show'.$row->name}},
			hide: {{'hidden'.$row->name}}
		});
        @endforeach
    </script>
@endsection

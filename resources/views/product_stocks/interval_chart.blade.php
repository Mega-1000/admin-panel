@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> @lang('product_stocks.title')
    </h1>
@endsection

@section('table')
    <div style="width: 80%; margin: 0 auto">
        <canvas id="myChart"></canvas>
    </div>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const data = JSON.parse(@json($intervals));

        const renderChart = (data) => {
            const labels = data.map((item) => item.interval);
            const quantities = data.map((item) => item.quantity);

            const config = {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Ilość',
                            data: quantities,
                            backgroundColor: 'rgb(75, 192, 192)',
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            };

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        };

        renderChart(data);
    </script>
@endsection

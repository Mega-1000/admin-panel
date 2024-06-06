<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h1>Order Chart</h1>

<form>
    <label for="interval">Interval:</label>
    <select id="interval" name="interval" onchange="updateChart()">
        <option value="week" {{ $interval === 'week' ? 'selected' : '' }}>Week</option>
        <option value="month" {{ $interval === 'month' ? 'selected' : '' }}>Month</option>
        <option value="day" {{ $interval === 'day' ? 'selected' : '' }}>Day</option>
    </select>

    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" onchange="updateChart()">

    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" onchange="updateChart()">
</form>

<canvas id="chart"></canvas>

<script>
    var labels = {!! json_encode($labels) !!};
    var data = {!! json_encode($data) !!};
    var interval = "{!! $interval !!}";

    var chartCanvas = document.getElementById('chart').getContext('2d');
    var chart = new Chart(chartCanvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Orders',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateChart() {
        var intervalSelect = document.getElementById('interval');
        var startDateInput = document.getElementById('start_date');
        var endDateInput = document.getElementById('end_date');

        var url = new URL(window.location.href);
        url.searchParams.set('interval', intervalSelect.value);
        url.searchParams.set('start_date', startDateInput.value);
        url.searchParams.set('end_date', endDateInput.value);

        window.location.href = url.toString();
    }
</script>
</body>
</html>

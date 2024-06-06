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

<h2>Orders by Week</h2>
<canvas id="weekChart"></canvas>

<h2>Orders by Month</h2>
<canvas id="monthChart"></canvas>

<script>
    var weekLabels = {!! json_encode($weekLabels) !!};
    var weekData = {!! json_encode($weekData) !!};
    var monthLabels = {!! json_encode($monthLabels) !!};
    var monthData = {!! json_encode($monthData) !!};

    var weekChartCanvas = document.getElementById('weekChart').getContext('2d');
    var weekChart = new Chart(weekChartCanvas, {
        type: 'bar',
        data: {
            labels: weekLabels,
            datasets: [{
                label: 'Orders',
                data: weekData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
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

    var monthChartCanvas = document.getElementById('monthChart').getContext('2d');
    var monthChart = new Chart(monthChartCanvas, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Orders',
                data: monthData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
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
</script>
</body>
</html>

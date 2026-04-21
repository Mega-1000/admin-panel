<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #555;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            border-radius: 8px;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }
    </style>
</head>
<body>
<h1>Order Dashboard</h1>

<div class="chart-container">
    <h2>Orders by Day</h2>
    <canvas id="dayChart"></canvas>
</div>

<div class="chart-container">
    <h2>Orders by Week</h2>
    <canvas id="weekChart"></canvas>
</div>

<div class="chart-container">
    <h2>Orders by Month</h2>
    <canvas id="monthChart"></canvas>
</div>

<script>
    var dayLabels = {!! json_encode($dayLabels) !!};
    var dayData = {!! json_encode($dayData) !!};
    var weekLabels = {!! json_encode($weekLabels) !!};
    var weekData = {!! json_encode($weekData) !!};
    var monthLabels = {!! json_encode($monthLabels) !!};
    var monthData = {!! json_encode($monthData) !!};

    var dayChartCanvas = document.getElementById('dayChart').getContext('2d');
    var dayChart = new Chart(dayChartCanvas, {
        type: 'line',
        data: {
            labels: dayLabels,
            datasets: [{
                label: 'Daily Orders',
                data: dayData,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                pointBackgroundColor: 'rgba(255, 99, 132, 1)'
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Orders Overview'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    var weekChartCanvas = document.getElementById('weekChart').getContext('2d');
    var weekChart = new Chart(weekChartCanvas, {
        type: 'line',
        data: {
            labels: weekLabels,
            datasets: [{
                label: 'Weekly Orders',
                data: weekData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Weekly Orders Overview'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    var monthChartCanvas = document.getElementById('monthChart').getContext('2d');
    var monthChart = new Chart(monthChartCanvas, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Monthly Orders',
                data: monthData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Orders Overview'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.raw}`;
                        }
                    }
                }
            },
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

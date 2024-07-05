@php
    $firm = \App\Entities\Firm::where('symbol', 'IZOTERM')->first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $firm->name }} - Employee Coverage Map</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <style>
        #map { height: 600px; }
    </style>
</head>
<body>
<h1>{{ $firm->name }} - Employee Coverage Map</h1>
<div id="map"></div>
<script>
    // Initialize the map
    var map = L.map('map').setView([0, 0], 2);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Function to generate a random color
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Employee data
    var employees = [
            @foreach($firm->employees as $employee)
        {
            id: {{ $employee->id }},
            name: "{{ $employee->name }}",
            radius: {{ $employee->radius }},
            zipCodes: [
                    @foreach(['zip_code_1', 'zip_code_2', 'zip_code_3', 'zip_code_4', 'zip_code_5'] as $zipCodeField)
                    @if($employee->$zipCodeField)
                    @php
                        $latLon = App\Entities\PostalCodeLatLon::where('postal_code', $employee->$zipCodeField)->first();
                    @endphp
                    @if($latLon)
                {
                    code: "{{ $employee->name }}",
                    lat: {{ $latLon->latitude }},
                    lng: {{ $latLon->longitude }}
                },
                @endif
                @endif
                @endforeach
            ]
        },
        @endforeach
    ];

    // Create the map
    employees.forEach(function(employee) {
        var color = getRandomColor();

        employee.zipCodes.forEach(function(zipCode) {
            L.circle([zipCode.lat, zipCode.lng], {
                color: color,
                fillColor: color,
                fillOpacity: 0.2,
                radius: employee.radius * 1000 // Convert km to meters
            }).addTo(map).bindPopup(`${employee.name}<br>Imie pracownika: ${zipCode.code}`);
        });
    });

    // Fit the map to show all markers
    var group = new L.featureGroup(map._layers);
    map.fitBounds(group.getBounds());
</script>
</body>
</html>

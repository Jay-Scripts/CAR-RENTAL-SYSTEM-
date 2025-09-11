<?php
// Example coordinates from DB or form
$pickup = ['lat' => 14.5995, 'lng' => 120.9842]; // Manila
$dropoff = ['lat' => 14.6760, 'lng' => 121.0437]; // QC
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Car Rental Route</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
    #map {
      height: 500px;
      width: 100%;
    }
  </style>
</head>

<body>
  <h2>Best Driving Route</h2>
  <div id="map"></div>

  <script>
    var map = L.map('map').setView([<?= $pickup['lat'] ?>, <?= $pickup['lng'] ?>], 12);

    // OSM Tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map);

    // Markers
    var pickup = [<?= $pickup['lat'] ?>, <?= $pickup['lng'] ?>];
    var dropoff = [<?= $dropoff['lat'] ?>, <?= $dropoff['lng'] ?>];

    L.marker(pickup).addTo(map).bindPopup("Pickup");
    L.marker(dropoff).addTo(map).bindPopup("Drop-off");

    // Call OpenRouteService API
    var apiKey = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjAwYzRlZTI5ZjMzYjQwYWI4YmNhZDI5YmRmZjE3ZTI5IiwiaCI6Im11cm11cjY0In0=";
    var url = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${pickup[1]},${pickup[0]}&end=${dropoff[1]},${dropoff[0]}`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        var coords = data.features[0].geometry.coordinates;
        var latlngs = coords.map(c => [c[1], c[0]]);
        var route = L.polyline(latlngs, {
          color: 'blue'
        }).addTo(map);
        map.fitBounds(route.getBounds());
      })
      .catch(err => console.error(err));
  </script>
</body>

</html>
promts here
https://chatgpt.com/share/68c2a12a-1d38-8012-965e-cf0d4df4c244
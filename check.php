<!DOCTYPE html>
<html>
<head>
    <title>Police Station Locator</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        #map { height: 600px; }
    </style>
</head>
<body>
    <h1>Police Station Locator</h1>
    <div id="map"></div>

    <script>
        // Set the initial view to a location in India (New Delhi) and zoom level
        var map = L.map('map').setView([28.613939, 77.209021], 5); // New Delhi coordinates

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Define the bounds for India
        var southWest = L.latLng(6.5546079, 68.1113787),
            northEast = L.latLng(35.6745457, 97.395555);
        var bounds = L.latLngBounds(southWest, northEast);

        // Restrict the map to the bounds of India
        map.setMaxBounds(bounds);
        map.on('drag', function() {
            map.panInsideBounds(bounds, { animate: false });
        });

        var marker;
        var radius = 10000; // 10 km

        function onMapClick(e) {
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker(e.latlng).addTo(map);
            fetchPoliceStations(e.latlng.lat, e.latlng.lng, radius);
            map.off('click', onMapClick); // Disable further clicks
        }

        map.on('click', onMapClick);

        function fetchPoliceStations(lat, lng, radius) {
            fetch(`find_police_stations.php?lat=${lat}&lng=${lng}&radius=${radius}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    data.elements.forEach(element => {
                        L.marker([element.lat, element.lon]).addTo(map)
                            .bindPopup(`<b>${element.tags.name || 'Police Station'}</b><br>${element.tags.amenity}`).openPopup();
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    </script>
</body>
</html>

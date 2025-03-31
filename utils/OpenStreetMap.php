<?php
class OpenStreetMap {
    // Generate HTML code for an OpenStreetMap with a marker
    public static function generateMap($latitude, $longitude, $zoom = 15, $width = '100%', $height = '400px') {
        $html = '
        <div id="map" style="width: ' . $width . '; height: ' . $height . ';"></div>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script>
            var map = L.map("map").setView([' . $latitude . ', ' . $longitude . '], ' . $zoom . ');
            
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors",
                maxZoom: 19
            }).addTo(map);
            
            L.marker([' . $latitude . ', ' . $longitude . ']).addTo(map)
                .bindPopup("Car Location")
                .openPopup();
        </script>';
        
        return $html;
    }

    // Generate HTML code for an OpenStreetMap with search functionality
    public static function generateMapWithSearch($default_lat = 10.762622, $default_lng = 106.660172, $zoom = 15, $width = '100%', $height = '400px') {
        $html = '
        <div id="map-container">
            <div id="search-box" class="mb-3">
                <input type="text" id="address-search" class="form-control" placeholder="Search for a location">
                <button id="search-button" class="btn btn-primary mt-2">Search</button>
            </div>
            <div id="map" style="width: ' . $width . '; height: ' . $height . ';"></div>
            <div class="form-group mt-3">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" class="form-control" required>
                <input type="hidden" id="latitude" name="latitude" value="' . $default_lat . '">
                <input type="hidden" id="longitude" name="longitude" value="' . $default_lng . '">
            </div>
        </div>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
        <script>
            var map = L.map("map").setView([' . $default_lat . ', ' . $default_lng . '], ' . $zoom . ');
            var marker;
            
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors",
                maxZoom: 19
            }).addTo(map);
            
            // Add geocoder control
            var geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            }).addTo(map);
            
            // Add initial marker
            marker = L.marker([' . $default_lat . ', ' . $default_lng . '], {
                draggable: true
            }).addTo(map);
            
            // Update form on marker drag
            marker.on("dragend", function(e) {
                var position = marker.getLatLng();
                document.getElementById("latitude").value = position.lat;
                document.getElementById("longitude").value = position.lng;
                
                // Reverse geocode to get address
                geocoder.options.geocoder.reverse(
                    position,
                    map.options.crs.scale(map.getZoom()),
                    function(results) {
                        var address = results[0].name;
                        document.getElementById("address").value = address;
                    }
                );
            });
            
            // Search button click handler
            document.getElementById("search-button").addEventListener("click", function() {
                var address = document.getElementById("address-search").value;
                
                geocoder.options.geocoder.geocode(address, function(results) {
                    if (results.length > 0) {
                        var result = results[0];
                        map.setView(result.center, 16);
                        
                        // Update marker
                        marker.setLatLng(result.center);
                        
                        // Update form
                        document.getElementById("latitude").value = result.center.lat;
                        document.getElementById("longitude").value = result.center.lng;
                        document.getElementById("address").value = result.name;
                    }
                });
            });
        </script>';
        
        return $html;
    }

    // Get coordinates from address using Nominatim API
    public static function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Car Rental App');
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (!empty($data)) {
            return [
                'latitude' => $data[0]['lat'],
                'longitude' => $data[0]['lon']
            ];
        }
        
        return false;
    }

    // Calculate distance between two coordinates (Haversine formula)
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371; // Kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earth_radius * $c;
        
        return $distance;
    }
}
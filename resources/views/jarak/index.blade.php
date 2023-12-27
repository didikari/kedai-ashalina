<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Cek Ongkir Kedai Ashalina</title>
</head>

<body>
    <div class="container">
        <h4 class="pt-2 text-uppercase">Cek Ongkir Kedai Ashalina</h4>
        <div class="row">
            <div class="col">
                <form action="{{ route('hitung-jarak') }}" method="post">
                    @csrf <!-- Laravel menggunakan csrf token untuk keamanan -->
                    <input type="hidden" id="clientLatitude" name="clientLatitude">
                    <input type="hidden" id="clientLongitude" name="clientLongitude">

                    <label for="purchase">Masukkan Belanja:</label>
                    <input type="number" id="purchase" name="purchase"><br><br>

                    <!-- Elemen div untuk peta -->
                    <div id="map" style="height: 400px; width: 100%;"></div>
                    <br>
                    <input type="submit" value="Hitung Jarak dan Biaya">
                </form>
            </div>
        </div>
        @if (isset($distanceResult))
            <!-- Tampilkan hasil jika $distanceResult sudah ada -->
            <div class="alert alert-success mt-3">
                <p>{{ $distanceResult }}</p>
            </div>
        @endif
    </div>
</body>
<!-- Di bagian head atau sebelum bagian body -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let map;
        let marker;

        function initializeMap(latitude, longitude) {
            map = L.map('map').setView([latitude, longitude], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            map.on('click', function(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng, {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', function(e) {
                    updateMarkerPosition(marker.getLatLng());
                });

                updateMarkerPosition(e.latlng);
            });
        }

        function updateMarkerPosition(latlng) {
            document.getElementById('clientLatitude').value = latlng.lat;
            document.getElementById('clientLongitude').value = latlng.lng;
        }

        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Isi nilai latitude dan longitude di input
                document.getElementById('clientLatitude').value = latitude;
                document.getElementById('clientLongitude').value = longitude;

                // Inisialisasi peta pada lokasi terkini
                initializeMap(latitude, longitude);

                // Tambahkan marker di lokasi terkini
                marker = L.marker([latitude, longitude], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', function(e) {
                    updateMarkerPosition(marker.getLatLng());
                });
            });
        } else {
            console.log('Geolocation tidak didukung di browser ini.');
        }
    });
</script>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>

</html>

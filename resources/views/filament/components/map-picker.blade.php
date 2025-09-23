<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="w-full">
        <div class="mb-2">
            <label class="text-sm font-medium text-gray-700">
                Pilih lokasi pada peta (klik untuk memilih koordinat)
            </label>
        </div>
        <div id="map-{{ $getId() }}" style="height: {{ $getHeight() ?? '400px' }};" class="w-full rounded-lg border border-gray-300 shadow-sm overflow-hidden">
            <div class="flex items-center justify-center h-full text-gray-500">
                Loading map...
            </div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const id = '{{ $getId() }}';
            const mapId = 'map-' + id;
            const mapElement = document.getElementById(mapId);

            if (!mapElement) {
                console.error('Map element not found:', mapId);
                return;
            }

            // Prevent double init on Livewire re-renders
            if (mapElement.dataset.initialized === '1') {
                return;
            }
            mapElement.dataset.initialized = '1';

            // Initial state from field
            const state = @json($getState()); // { lat, lng }
            const defaultZoom = {{ (int) $getDefaultZoom() }};
            let centerLat = -6.2088;
            let centerLng = 106.8456;
            if (state && typeof state.lat !== 'undefined' && typeof state.lng !== 'undefined') {
                centerLat = parseFloat(state.lat);
                centerLng = parseFloat(state.lng);
            }

            const map = L.map(mapId).setView([centerLat, centerLng], defaultZoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let marker = null;
            // Place initial marker if state present
            if (!isNaN(centerLat) && !isNaN(centerLng)) {
                marker = L.marker([centerLat, centerLng]).addTo(map)
                    .bindPopup(`Lat: ${centerLat.toFixed(8)}<br>Lng: ${centerLng.toFixed(8)}`);
            }

            map.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(8);
                const lng = e.latlng.lng.toFixed(8);

                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(`Lat: ${lat}<br>Lng: ${lng}`)
                    .openPopup();

                if (window.Livewire) {
                    window.Livewire.dispatch('coordinates-updated', { lat: lat, lng: lng });
                }
            });
        });
    </script>
</x-dynamic-component>
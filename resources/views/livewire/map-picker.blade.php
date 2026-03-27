<div
    x-data="{
        map: null,
        marker: null,
        readOnly: @js($readOnly),
        lat: @entangle('latitude'),
        lng: @entangle('longitude'),
        address: @entangle('address'),
        defaultLat: @js($defaultLat),
        defaultLng: @js($defaultLng),
        zoom: @js($zoom),
        mapId: @js($mapId),

        init() {
            this.$nextTick(() => this.initMap());
        },

        initMap() {
            const initialLat = this.lat ?? this.defaultLat;
            const initialLng = this.lng ?? this.defaultLng;

            this.map = L.map(this.mapId).setView([initialLat, initialLng], this.zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot;>OSM</a>',
                maxZoom: 19,
            }).addTo(this.map);

            if (this.lat && this.lng) {
                this.placeMarker(this.lat, this.lng);
            }

            if (!this.readOnly) {
                this.map.on('click', (e) => {
                    this.placeMarker(e.latlng.lat, e.latlng.lng);
                    this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                });
            }

            this.$watch('lat', (val) => {
                if (val && this.lng) this.placeMarker(val, this.lng, true);
            });
            this.$watch('lng', (val) => {
                if (this.lat && val) this.placeMarker(this.lat, val, true);
            });
        },

        placeMarker(lat, lng, panTo = false) {
            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            } else {
                this.marker = L.marker([lat, lng], {
                    draggable: !this.readOnly,
                }).addTo(this.map);

                if (!this.readOnly) {
                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.updateCoords(pos.lat, pos.lng);
                        this.reverseGeocode(pos.lat, pos.lng);
                    });
                }
            }

            if (!this.readOnly) {
                this.updateCoords(lat, lng);
            }

            if (panTo) {
                this.map.panTo([lat, lng]);
            }
        },

        updateCoords(lat, lng) {
            this.lat = Math.round(lat * 10000000) / 10000000;
            this.lng = Math.round(lng * 10000000) / 10000000;
            $wire.updateLocation(this.lat, this.lng, this.address);
        },

        async reverseGeocode(lat, lng) {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
                const res = await fetch(url, { headers: { 'Accept-Language': 'id' } });
                const data = await res.json();
                if (data.display_name) {
                    this.address = data.display_name;
                    $wire.updateLocation(this.lat, this.lng, this.address);
                }
            } catch (e) { /* Nominatim may rate-limit */ }
        },

        async searchAddress(query) {
            if (!query || query.length < 3) return [];
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5&viewbox=118.2,\-8.3,118.7,\-8.7&bounded=1`;
                const res = await fetch(url, { headers: { 'Accept-Language': 'id' } });
                return await res.json();
            } catch (e) { return []; }
        },

        selectResult(result) {
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            this.address = result.display_name;
            this.placeMarker(lat, lng, true);
            this.updateCoords(lat, lng);
            this.map.setView([lat, lng], 17);
        },
    }"
    x-on:request-geolocation.window="
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    placeMarker(pos.coords.latitude, pos.coords.longitude, true);
                    reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                    map.setView([pos.coords.latitude, pos.coords.longitude], 17);
                },
                () => alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.')
            );
        } else {
            alert('Browser tidak mendukung geolokasi.');
        }
    "
    class="space-y-2"
>
    {{-- Search bar --}}
    @unless($readOnly)
        <div x-data="{ query: '', results: [], open: false }" class="relative">
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="query"
                    x-on:input.debounce.500ms="results = await searchAddress(query); open = results.length > 0"
                    x-on:click.away="open = false"
                    placeholder="Cari alamat..."
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm py-1.5 px-3"
                />
                <button
                    type="button"
                    x-on:click="$wire.useMyLocation()"
                    class="fi-btn fi-btn-size-sm inline-flex items-center gap-1 rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-primary-500 whitespace-nowrap"
                >
                    <x-filament::icon icon="heroicon-o-map-pin" class="size-4" />
                    Lokasi Saya
                </button>
            </div>

            {{-- Search results dropdown --}}
            <div
                x-show="open"
                x-transition
                class="absolute z-[9999] mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800 max-h-48 overflow-y-auto"
            >
                <template x-for="result in results" :key="result.place_id">
                    <button
                        type="button"
                        x-on:click="selectResult(result); open = false; query = result.display_name"
                        x-text="result.display_name"
                        class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200 truncate"
                    ></button>
                </template>
            </div>
        </div>
    @endunless

    {{-- Map --}}
    <div id="{{ $mapId }}" class="h-64 w-full rounded-lg border border-gray-300 dark:border-gray-600 z-0"></div>

    {{-- Coordinates display --}}
    @if($latitude && $longitude)
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ $latitude }}, {{ $longitude }}
            @if($address)
                — {{ Str::limit($address, 80) }}
            @endif
        </p>
    @endif
</div>

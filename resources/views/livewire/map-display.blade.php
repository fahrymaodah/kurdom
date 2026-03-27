<div
    x-data="{
        map: null,
        pickupLat: @js($pickupLat),
        pickupLng: @js($pickupLng),
        pickupLabel: @js($pickupLabel),
        deliveryLat: @js($deliveryLat),
        deliveryLng: @js($deliveryLng),
        deliveryLabel: @js($deliveryLabel),
        mapId: @js($mapId),

        init() {
            this.$nextTick(() => this.initMap());
        },

        initMap() {
            if (!this.pickupLat || !this.deliveryLat) return;

            const bounds = L.latLngBounds(
                [this.pickupLat, this.pickupLng],
                [this.deliveryLat, this.deliveryLng]
            );

            this.map = L.map(this.mapId).fitBounds(bounds.pad(0.2));

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot;>OSM</a>',
                maxZoom: 19,
            }).addTo(this.map);

            // Pickup marker (green)
            const pickupIcon = L.divIcon({
                html: '<div class=&quot;flex items-center justify-center size-8 rounded-full bg-emerald-500 text-white text-xs font-bold shadow-lg border-2 border-white&quot;>P</div>',
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            L.marker([this.pickupLat, this.pickupLng], { icon: pickupIcon })
                .addTo(this.map)
                .bindPopup(this.pickupLabel);

            // Delivery marker (red)
            const deliveryIcon = L.divIcon({
                html: '<div class=&quot;flex items-center justify-center size-8 rounded-full bg-red-500 text-white text-xs font-bold shadow-lg border-2 border-white&quot;>D</div>',
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            L.marker([this.deliveryLat, this.deliveryLng], { icon: deliveryIcon })
                .addTo(this.map)
                .bindPopup(this.deliveryLabel);

            // Route line
            L.polyline([
                [this.pickupLat, this.pickupLng],
                [this.deliveryLat, this.deliveryLng],
            ], {
                color: '#3b82f6',
                weight: 3,
                dashArray: '8, 8',
            }).addTo(this.map);
        },
    }"
>
    <div id="{{ $mapId }}" class="h-64 w-full rounded-lg border border-gray-300 dark:border-gray-600 z-0"></div>

    @if($pickupLat && $deliveryLat)
        <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
            <span><span class="inline-block size-3 rounded-full bg-emerald-500 mr-1"></span>{{ $pickupLabel }}</span>
            <span><span class="inline-block size-3 rounded-full bg-red-500 mr-1"></span>{{ $deliveryLabel }}</span>
        </div>
    @endif
</div>

<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left: Form --}}
        <div>
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button wire:click="calculateFee" color="gray">
                    Hitung Ongkir
                </x-filament::button>
                <x-filament::button wire:click="submit">
                    Buat Pesanan
                </x-filament::button>
            </div>

            @if($this->estimatedFee !== null)
                <div class="mt-3 rounded-lg bg-success-50 dark:bg-success-950 p-3 text-sm text-success-700 dark:text-success-300">
                    <strong>Estimasi Ongkir:</strong>
                    Rp {{ number_format($this->estimatedFee, 0, ',', '.') }}
                    ({{ $this->estimatedDistance }} km)
                </div>
            @endif
        </div>

        {{-- Right: Maps --}}
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <x-filament::icon icon="heroicon-o-map-pin" class="inline size-4 text-emerald-500" />
                    Lokasi Pickup
                </h3>
                <livewire:map-picker
                    :latitude="$pickupLat"
                    :longitude="$pickupLng"
                    map-id="pickup-map"
                    location-event="pickup-location-updated"
                    wire:key="pickup-map"
                />
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <x-filament::icon icon="heroicon-o-map-pin" class="inline size-4 text-red-500" />
                    Lokasi Pengiriman
                </h3>
                <livewire:map-picker
                    :latitude="$deliveryLat"
                    :longitude="$deliveryLng"
                    map-id="delivery-map"
                    location-event="delivery-location-updated"
                    wire:key="delivery-map"
                />
            </div>
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    {{-- Map --}}
    @if($record->pickup_latitude && $record->delivery_latitude)
        <div class="mb-6">
            <livewire:map-display
                :pickup-lat="(float) $record->pickup_latitude"
                :pickup-lng="(float) $record->pickup_longitude"
                :pickup-label="$record->pickup_address_text ?? 'Pickup'"
                :delivery-lat="(float) $record->delivery_latitude"
                :delivery-lng="(float) $record->delivery_longitude"
                :delivery-label="$record->delivery_address_text ?? 'Tujuan'"
                map-id="order-map-{{ $record->id }}"
            />
        </div>
    @endif

    {{-- Order details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-filament::section heading="Info Pesanan">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Kode</dt>
                    <dd class="font-medium">{{ $record->order_code }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                    <dd><x-filament::badge :color="$record->status->color()">{{ $record->status->label() }}</x-filament::badge></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Penjual</dt>
                    <dd>{{ $record->seller?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Pembeli</dt>
                    <dd>{{ $record->buyer_name }} ({{ $record->buyer_phone }})</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Jarak</dt>
                    <dd>{{ $record->distance_km }} km</dd>
                </div>
            </dl>
        </x-filament::section>

        <x-filament::section heading="Alamat & Biaya">
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Pickup</dt>
                    <dd>{{ $record->pickup_address_text ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Pengiriman</dt>
                    <dd>{{ $record->delivery_address_text ?? '-' }}</dd>
                </div>
                <div class="flex justify-between pt-2 border-t dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">Harga Barang</dt>
                    <dd>Rp {{ number_format((float) $record->item_price, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Ongkir</dt>
                    <dd class="font-semibold text-primary-600">Rp {{ number_format((float) $record->delivery_fee, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between font-bold">
                    <dt>Total</dt>
                    <dd>Rp {{ number_format((float) $record->total, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </x-filament::section>
    </div>

    @if($record->notes)
        <x-filament::section heading="Catatan" class="mt-4">
            <p class="text-sm">{{ $record->notes }}</p>
        </x-filament::section>
    @endif
</x-filament-panels::page>

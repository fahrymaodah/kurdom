<div>
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Lacak Pesanan</h1>

    <form wire:submit="track" class="mb-6 flex gap-2">
        <input wire:model="orderCode" type="text" placeholder="Masukkan kode pesanan (KD-XXXXXXXX)"
               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
        <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
            Lacak
        </button>
    </form>

    @if($notFound)
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center text-sm text-red-700">
            Pesanan dengan kode <strong>{{ $orderCode }}</strong> tidak ditemukan.
        </div>
    @endif

    @if($order)
        <div wire:poll.15s="track" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold font-mono">{{ $order->order_code }}</h2>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                    {{ match($order->status->color()) {
                        'info' => 'bg-blue-100 text-blue-800',
                        'warning' => 'bg-yellow-100 text-yellow-800',
                        'primary' => 'bg-indigo-100 text-indigo-800',
                        'success' => 'bg-green-100 text-green-800',
                        'danger' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                    } }}">
                    {{ $order->status->label() }}
                </span>
            </div>

            {{-- Progress bar --}}
            @php
                $steps = [
                    'new' => 'Baru',
                    'courier_assigned' => 'Kurir Ditugaskan',
                    'picked_up' => 'Diambil',
                    'in_delivery' => 'Dalam Pengiriman',
                    'completed' => 'Selesai',
                ];
                $currentIndex = array_search($order->status->value, array_keys($steps));
                $isCancelled = $order->status->value === 'cancelled';
            @endphp

            @unless($isCancelled)
                <div class="mb-6">
                    <div class="flex gap-1 mb-2">
                        @foreach($steps as $key => $label)
                            @php $i = array_search($key, array_keys($steps)); @endphp
                            <div class="h-2 flex-1 rounded-full {{ $i <= $currentIndex ? 'bg-primary-500' : 'bg-gray-200' }}"></div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        @foreach($steps as $label)
                            <span>{{ $label }}</span>
                        @endforeach
                    </div>
                </div>
            @endunless

            {{-- Map --}}
            @if($order->pickup_latitude && $order->delivery_latitude)
                <div class="mb-4">
                    <livewire:map-display
                        :pickup-lat="(float) $order->pickup_latitude"
                        :pickup-lng="(float) $order->pickup_longitude"
                        pickup-label="Pickup"
                        :delivery-lat="(float) $order->delivery_latitude"
                        :delivery-lng="(float) $order->delivery_longitude"
                        delivery-label="Tujuan"
                        map-id="track-map"
                    />
                </div>
            @endif

            {{-- Details --}}
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-gray-500">Pickup</dt>
                    <dd>{{ $order->pickup_address_text }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tujuan</dt>
                    <dd>{{ $order->delivery_address_text }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Pembeli</dt>
                    <dd>{{ $order->buyer_name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Kurir</dt>
                    <dd>{{ $order->courier?->name ?? 'Menunggu kurir...' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jarak</dt>
                    <dd>{{ $order->distance_km }} km</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Ongkir</dt>
                    <dd class="font-semibold">Rp {{ number_format((float) $order->delivery_fee, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Harga Barang</dt>
                    <dd>Rp {{ number_format((float) $order->item_price, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Total</dt>
                    <dd class="text-lg font-bold">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd>
                </div>
            </dl>

            @if($order->notes)
                <div class="mt-4 rounded-lg bg-gray-50 p-3">
                    <p class="text-sm text-gray-600"><strong>Catatan:</strong> {{ $order->notes }}</p>
                </div>
            @endif

            {{-- Timeline --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-700">Timeline</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span class="text-gray-500">Dibuat:</span>
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($order->courier_assigned_at)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                            <span class="text-gray-500">Kurir ditugaskan:</span>
                            <span>{{ $order->courier_assigned_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->picked_up_at)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                            <span class="text-gray-500">Diambil:</span>
                            <span>{{ $order->picked_up_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->delivery_started_at)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                            <span class="text-gray-500">Pengiriman dimulai:</span>
                            <span>{{ $order->delivery_started_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->completed_at)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            <span class="text-gray-500">Selesai:</span>
                            <span>{{ $order->completed_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->cancelled_at)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            <span class="text-gray-500">Dibatalkan:</span>
                            <span>{{ $order->cancelled_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

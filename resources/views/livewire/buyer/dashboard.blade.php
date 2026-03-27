<div wire:poll.15s>
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Dashboard</h1>

    <h2 class="mb-3 text-lg font-semibold text-gray-800">Pesanan Aktif</h2>

    @if($this->activeOrders->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-500">
            Tidak ada pesanan aktif saat ini.
        </div>
    @else
        <div class="space-y-4">
            @foreach($this->activeOrders as $order)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-sm font-semibold">{{ $order->order_code }}</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
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
                        $steps = ['new', 'courier_assigned', 'picked_up', 'in_delivery', 'completed'];
                        $currentIndex = array_search($order->status->value, $steps);
                    @endphp
                    <div class="mb-3 flex gap-1">
                        @foreach($steps as $i => $step)
                            <div class="h-1.5 flex-1 rounded-full {{ $i <= $currentIndex ? 'bg-primary-500' : 'bg-gray-200' }}"></div>
                        @endforeach
                    </div>

                    <dl class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <dt class="text-gray-500">Dari</dt>
                            <dd>{{ $order->pickup_address_text }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Tujuan</dt>
                            <dd>{{ $order->delivery_address_text }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Kurir</dt>
                            <dd>{{ $order->courier?->name ?? 'Menunggu kurir...' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Total</dt>
                            <dd class="font-semibold">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    @if($order->pickup_latitude && $order->delivery_latitude)
                        <div class="mt-3">
                            <livewire:map-display
                                :pickup-lat="(float) $order->pickup_latitude"
                                :pickup-lng="(float) $order->pickup_longitude"
                                pickup-label="Pickup"
                                :delivery-lat="(float) $order->delivery_latitude"
                                :delivery-lng="(float) $order->delivery_longitude"
                                delivery-label="Tujuan"
                                :map-id="'dashboard-map-' . $order->id"
                                :key="'map-' . $order->id"
                            />
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

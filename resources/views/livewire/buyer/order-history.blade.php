<div>
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Riwayat Pesanan</h1>

    @if($this->orders->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-500">
            Belum ada riwayat pesanan.
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Toko</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Lacak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($this->orders as $order)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-sm">{{ $order->order_code }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">{{ $order->seller?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
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
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <a href="{{ route('buyer.track', $order->order_code) }}" class="text-primary-600 hover:underline">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->orders->links() }}
        </div>
    @endif
</div>

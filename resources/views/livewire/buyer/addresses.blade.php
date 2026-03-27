<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Alamat Tersimpan</h1>
        @unless($showForm)
            <button wire:click="create" class="rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-700">
                + Tambah Alamat
            </button>
        @endunless
    </div>

    {{-- Form --}}
    @if($showForm)
        <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">{{ $editingId ? 'Edit Alamat' : 'Tambah Alamat' }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Label</label>
                        <input wire:model="label" type="text" placeholder="Rumah, Kantor, dll." required
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                        @error('label') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mt-7">
                            <input wire:model="is_default" type="checkbox" class="rounded border-gray-300 text-primary-600" />
                            Jadikan alamat utama
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi</label>
                    <livewire:map-picker
                        :latitude="$latitude ?? -8.5365"
                        :longitude="$longitude ?? 118.4633"
                        :address="$address_text"
                        location-event="address-location-updated"
                        map-id="address-map"
                        :key="'address-map-' . ($editingId ?? 'new')"
                    />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                        Simpan
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- List --}}
    @if($this->addresses->isEmpty() && !$showForm)
        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-500">
            Belum ada alamat tersimpan.
        </div>
    @else
        <div class="space-y-3">
            @foreach($this->addresses as $address)
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ $address->label }}</span>
                            @if($address->is_default)
                                <span class="inline-flex rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700">Utama</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-sm text-gray-500">{{ $address->address_text }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="edit({{ $address->id }})" class="text-sm text-primary-600 hover:underline">Edit</button>
                        <button wire:click="delete({{ $address->id }})" wire:confirm="Hapus alamat ini?" class="text-sm text-red-600 hover:underline">Hapus</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@script
<script>
    $wire.on('address-location-updated', (data) => {
        $wire.set('latitude', data[0].lat);
        $wire.set('longitude', data[0].lng);
        $wire.set('address_text', data[0].address);
    });
</script>
@endscript

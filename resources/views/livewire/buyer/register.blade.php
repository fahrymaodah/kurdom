<div class="mx-auto max-w-md">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Daftar Akun Pembeli</h1>

    <form wire:submit="register" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input wire:model="name" type="text" id="name" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">No. HP</label>
            <input wire:model="phone" type="tel" id="phone" placeholder="08xxxxxxxxxx" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input wire:model="password" type="password" id="password" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
            <input wire:model="password_confirmation" type="password" id="password_confirmation" required
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Anda (opsional)</label>
            <livewire:map-picker
                :latitude="$latitude ?? -8.5365"
                :longitude="$longitude ?? 118.4633"
                :address="$address_text"
                location-event="buyer-location-updated"
                map-id="register-map"
            />
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700">
            Daftar
        </button>

        <p class="text-center text-sm text-gray-600">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Masuk</a>
        </p>
    </form>
</div>

@script
<script>
    $wire.on('buyer-location-updated', (data) => {
        $wire.set('latitude', data[0].lat);
        $wire.set('longitude', data[0].lng);
        $wire.set('address_text', data[0].address);
    });
</script>
@endscript

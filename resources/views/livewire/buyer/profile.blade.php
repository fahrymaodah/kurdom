<div class="mx-auto max-w-lg">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Profil</h1>

    {{-- Profile Form --}}
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold">Informasi Akun</h2>

        @if($saved)
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">Profil berhasil diperbarui.</div>
        @endif

        <form wire:submit="updateProfile" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input wire:model="name" type="text" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">No. HP</label>
                <input wire:model="phone" type="tel" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                <livewire:map-picker
                    :latitude="$latitude ?? -8.5365"
                    :longitude="$longitude ?? 118.4633"
                    :address="$address_text"
                    location-event="profile-location-updated"
                    map-id="profile-map"
                />
            </div>

            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                Simpan Profil
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold">Ubah Password</h2>

        @if($passwordChanged)
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">Password berhasil diubah.</div>
        @endif

        <form wire:submit="changePassword" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                <input wire:model="current_password" type="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input wire:model="new_password" type="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                @error('new_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input wire:model="new_password_confirmation" type="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
            </div>

            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                Ubah Password
            </button>
        </form>
    </div>
</div>

@script
<script>
    $wire.on('profile-location-updated', (data) => {
        $wire.set('latitude', data[0].lat);
        $wire.set('longitude', data[0].lng);
        $wire.set('address_text', data[0].address);
    });
</script>
@endscript

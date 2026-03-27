<div class="mx-auto max-w-md">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Masuk</h1>

    <form wire:submit="login" class="space-y-4">
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

        <button type="submit"
                class="w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700">
            Masuk
        </button>

        <p class="text-center text-sm text-gray-600">
            Belum punya akun? <a href="{{ route('buyer.register') }}" class="text-primary-600 hover:underline">Daftar</a>
        </p>
    </form>
</div>

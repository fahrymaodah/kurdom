<x-filament-panels::page>
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center size-24 rounded-full {{ $isOnline ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600' }} mb-4">
            <x-filament::icon
                :icon="$isOnline ? 'heroicon-o-signal' : 'heroicon-o-signal-slash'"
                class="size-12"
            />
        </div>
        <h2 class="text-2xl font-bold {{ $isOnline ? 'text-success-600' : 'text-danger-600' }}">
            {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
        </h2>
        <p class="text-gray-500 mt-2">
            {{ $isOnline ? 'Anda akan menerima pesanan baru.' : 'Anda tidak akan menerima pesanan.' }}
        </p>
    </div>
</x-filament-panels::page>

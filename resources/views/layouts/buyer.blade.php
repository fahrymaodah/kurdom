<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'KurDom' }} — KurDom</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-leaflet-assets />
</head>
<body class="h-full bg-gray-50 font-sans antialiased">

    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-14 items-center justify-between">
                <a href="{{ route('buyer.dashboard') }}" class="text-lg font-bold text-primary-600">KurDom</a>

                @auth
                    <div class="flex items-center gap-4">
                        <a href="{{ route('buyer.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 {{ request()->routeIs('buyer.dashboard') ? 'font-semibold text-gray-900' : '' }}">Dashboard</a>
                        <a href="{{ route('buyer.orders') }}" class="text-sm text-gray-600 hover:text-gray-900 {{ request()->routeIs('buyer.orders') ? 'font-semibold text-gray-900' : '' }}">Riwayat</a>
                        <a href="{{ route('buyer.addresses') }}" class="text-sm text-gray-600 hover:text-gray-900 {{ request()->routeIs('buyer.addresses') ? 'font-semibold text-gray-900' : '' }}">Alamat</a>
                        <a href="{{ route('buyer.profile') }}" class="text-sm text-gray-600 hover:text-gray-900 {{ request()->routeIs('buyer.profile') ? 'font-semibold text-gray-900' : '' }}">Profil</a>
                        <form method="POST" action="{{ route('buyer.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Keluar</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Masuk</a>
                        <a href="{{ route('buyer.register') }}" class="rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-700">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>
</body>
</html>

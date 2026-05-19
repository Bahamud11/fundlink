<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fundlink') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50" x-data="{ openSidebar: false }">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar Navigation -->
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden">
                <!-- Mobile Top Header -->
                <header class="lg:hidden bg-white border-b border-gray-100 px-4 py-3 flex items-center space-x-3 sticky top-0 z-30">
                    <button @click="openSidebar = true" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-xl transition-all">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Fundlink Logo" class="h-8 w-auto">
                        <span class="font-black text-gray-900 text-sm tracking-tight uppercase">Fundlink</span>
                    </div>
                </header>

                <!-- Page Heading -->
                @if (isset($header))
                    <div class="pt-6 px-4 sm:px-8">
                        <div class="w-full">
                            {{ $header }}
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-8">
                    <div class="w-full">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>

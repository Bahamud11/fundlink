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
    <body class="font-sans antialiased bg-gray-50">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar Navigation -->
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden">
                <!-- Top Header for Notifications -->
                <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-10 py-4 px-8 flex justify-end items-center">
                    <div class="flex items-center space-x-4">
                        <!-- Notification Trigger -->
                        <a href="{{ route('notifications') }}" class="relative p-2 text-gray-400 hover:text-blue-600 transition-colors duration-200">
                            <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 border-2 border-white rounded-full text-[10px] text-white flex items-center justify-center font-bold">3</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </a>
                        
                        <!-- User Profile Mini -->
                        <div class="flex items-center space-x-3 border-l pl-4 ml-2 border-gray-100">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mt-1">{{ auth()->user()->role }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-200">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Heading -->
                @if (isset($header))
                    <div class="pt-6 px-8">
                        <div class="max-w-7xl">
                            {{ $header }}
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-8">
                    <div class="max-w-7xl">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>

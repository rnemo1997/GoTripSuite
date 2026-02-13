<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('GoTripSuite - Your Journey Starts Here'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @foreach($supportedLocales as $code => $name)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ localizedUrl($code) }}" />
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ localizedUrl('en') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-stone-50 min-h-screen antialiased" style="font-family: 'Inter', sans-serif;">
    {{-- Navigation --}}
    <nav class="@yield('nav-class', 'bg-white/95 backdrop-blur-md shadow-sm') fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl">&#9992;&#65039;</span>
                    <span class="text-xl font-bold tracking-tight @yield('logo-text-class', 'text-stone-900')" style="font-family: 'Playfair Display', serif;">GoTripSuite</span>
                </a>
                <div class="flex items-center gap-4 sm:gap-6 text-sm">
                    <div class="hidden sm:flex items-center gap-6 @yield('nav-links-class', 'text-stone-600')">
                        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">{{ __('Explore') }}</a>
                        <a href="{{ route('trip-builder.index') }}" class="hover:text-amber-600 transition-colors">{{ __('Trip Builder') }}</a>
                    </div>
                    <span class="hidden sm:inline text-stone-300">|</span>
                    {{-- Language Switcher (click-based) --}}
                    <div class="relative @yield('nav-links-class', 'text-stone-600')" id="langSwitcher">
                        <button type="button" id="langBtn" class="inline-flex items-center gap-1.5 hover:text-amber-600 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            <span class="uppercase text-xs font-semibold tracking-wide">{{ $currentLocale }}</span>
                            <svg class="w-3 h-3 transition-transform" id="langChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="langDropdown" class="absolute right-0 mt-2 hidden">
                            <div class="w-40 bg-white rounded-xl shadow-lg border border-stone-200 py-1">
                                @foreach($supportedLocales as $code => $name)
                                    <a href="{{ localizedUrl($code) }}"
                                       class="block px-4 py-2 text-sm text-stone-700 hover:bg-amber-50 hover:text-amber-700 transition-colors {{ $code === $currentLocale ? 'font-semibold bg-stone-50' : '' }}">
                                        {!! $name !!}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="fixed top-20 left-1/2 -translate-x-1/2 z-50 max-w-lg w-full px-4 animate-fade-in">
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl shadow-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('langBtn');
        const dropdown = document.getElementById('langDropdown');
        const chevron = document.getElementById('langChevron');
        if (!btn || !dropdown) return;

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const open = !dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            chevron.style.transform = open ? '' : 'rotate(180deg)';
        });

        document.addEventListener('click', function() {
            dropdown.classList.add('hidden');
            chevron.style.transform = '';
        });
    });
    </script>
</body>
</html>

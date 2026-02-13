@extends('layouts.app')

@section('title', __('Trip Builder') . ' - GoTripSuite')
@section('nav-class', 'bg-transparent')
@section('logo-text-class', 'text-white')
@section('nav-links-class', 'text-white/80')

@section('content')
{{-- Hero Section --}}
<section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=2000&q=80"
             alt="Road trip" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60"></div>
    </div>

    <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 w-full pt-24 pb-16">
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-4 py-1.5 mb-6">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                <span class="text-white/90 text-sm font-medium">{{ __('Trip Builder') }}</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
                {{ __('Multiple stops, one epic journey') }}
            </h1>
            <p class="text-lg text-white/70 max-w-xl mx-auto font-light">
                {{ __('Map out your dream road trip. Add destinations, find the perfect hotel at each stop, and book everything in one go.') }}
            </p>
        </div>

        @if($trip)
            {{-- Resume existing trip --}}
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-5 mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-300 text-xs font-medium tracking-[0.15em] uppercase mb-1">{{ __('Continue your trip') }}</p>
                        <h2 class="font-semibold text-white text-lg" style="font-family: 'Playfair Display', serif;">{{ $trip->name }}</h2>
                        <p class="text-sm text-white/60 mt-1">{{ $trip->stops()->count() }} {{ $trip->stops()->count() === 1 ? __('stop') : __('stops') }} &middot; {{ $trip->adults }} {{ $trip->adults === 1 ? __('guest') : __('guests') }}</p>
                    </div>
                    <a href="{{ route('trip-builder.show', $trip) }}"
                       class="bg-white text-stone-900 py-2.5 px-6 rounded-xl font-semibold hover:bg-amber-50 active:scale-[0.98] transition-all text-sm">
                        {{ __('Continue') }}
                    </a>
                </div>
            </div>
            <div class="text-center text-white/40 text-sm mb-5 flex items-center gap-4">
                <span class="flex-1 h-px bg-white/10"></span>
                <span>{{ __('or start a new trip') }}</span>
                <span class="flex-1 h-px bg-white/10"></span>
            </div>
        @endif

        {{-- New Trip Form --}}
        <form action="{{ route('trip-builder.create') }}" method="POST"
              class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl p-6 sm:p-8 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Give your trip a name') }}</label>
                <input type="text" name="name" required placeholder="e.g. Italy Road Trip, Southeast Asia Adventure..."
                    class="w-full border border-stone-200 rounded-xl px-4 py-3.5 text-stone-800 placeholder-stone-400 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all text-lg" style="font-family: 'Playfair Display', serif;">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" id="startDate" required
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Travellers') }}</label>
                    <select name="adults"
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                        @for($i = 1; $i <= 9; $i++)
                            <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? __('Traveller') : __('Travellers') }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-stone-900 text-white py-3.5 px-6 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                {{ __('Start Planning') }}
            </button>
        </form>

        @if($errors->any())
            <div class="mt-4 bg-red-50/90 backdrop-blur border border-red-200 text-red-700 px-5 py-4 rounded-xl">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>

{{-- How it works --}}
<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <p class="text-amber-600 text-sm font-medium tracking-[0.2em] uppercase mb-3">{{ __('How it works') }}</p>
            <h2 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">
                {{ __('Three steps to your perfect trip') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="relative text-center group">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition-colors relative">
                    <span class="absolute -top-2 -right-2 w-6 h-6 bg-amber-500 text-white rounded-full text-xs font-bold flex items-center justify-center">1</span>
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">{{ __('Add your stops') }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ __('Search for destinations and drag them into the perfect order. See your route come alive on the map.') }}</p>
            </div>
            <div class="relative text-center group">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition-colors relative">
                    <span class="absolute -top-2 -right-2 w-6 h-6 bg-amber-500 text-white rounded-full text-xs font-bold flex items-center justify-center">2</span>
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">{{ __('Pick your hotels') }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ __('Find and compare hotels at each destination. Dates chain automatically so everything lines up.') }}</p>
            </div>
            <div class="relative text-center group">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition-colors relative">
                    <span class="absolute -top-2 -right-2 w-6 h-6 bg-amber-500 text-white rounded-full text-xs font-bold flex items-center justify-center">3</span>
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">{{ __('Book everything at once') }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ __('Fill in your details once, pay for all hotels, and get instant confirmation for your entire journey.') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Inspiration --}}
<section class="py-20 bg-stone-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <p class="text-amber-600 text-sm font-medium tracking-[0.2em] uppercase mb-3">{{ __('Need inspiration?') }}</p>
            <h2 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Popular road trips') }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach([
                [__('Italian Classics'), 'Rome, Florence, Venice, Milan', 'https://images.unsplash.com/photo-1515859005217-8a1f08870f59?auto=format&fit=crop&w=600&q=80', '4 stops · 10 nights'],
                [__('Thai Explorer'), 'Bangkok, Chiang Mai, Phuket', 'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=600&q=80', '3 stops · 9 nights'],
                [__('Spanish Coast'), 'Barcelona, Valencia, Seville', 'https://images.unsplash.com/photo-1583422409516-2895a77efded?auto=format&fit=crop&w=600&q=80', '3 stops · 8 nights'],
            ] as [$title, $route, $img, $details])
                <div class="group relative h-64 rounded-2xl overflow-hidden">
                    <img src="{{ $img }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5">
                        <h3 class="text-white font-bold text-lg" style="font-family: 'Playfair Display', serif;">{{ $title }}</h3>
                        <p class="text-white/70 text-sm mt-1">{{ $route }}</p>
                        <p class="text-amber-300 text-xs font-medium mt-2">{{ $details }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="bg-stone-900 text-stone-400 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center text-sm">
        <p class="mb-2" style="font-family: 'Playfair Display', serif;">
            <span class="text-white font-semibold text-lg">GoTripSuite</span>
        </p>
        <p class="text-stone-500">{{ __('Your journey starts here. Book with confidence.') }}</p>
    </div>
</footer>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const el = document.getElementById('startDate');
    el.value = tomorrow.toISOString().split('T')[0];
    el.min = tomorrow.toISOString().split('T')[0];

    // Nav background on scroll (same as home)
    const nav = document.querySelector('nav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 60) {
            nav.classList.remove('bg-transparent');
            nav.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-sm');
            nav.querySelector('span[style]').classList.remove('text-white');
            nav.querySelector('span[style]').classList.add('text-stone-900');
            nav.querySelectorAll('.sm\\:flex, #langSwitcher').forEach(el => {
                el.classList.remove('text-white/80');
                el.classList.add('text-stone-600');
            });
        } else {
            nav.classList.add('bg-transparent');
            nav.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-sm');
            nav.querySelector('span[style]').classList.add('text-white');
            nav.querySelector('span[style]').classList.remove('text-stone-900');
            nav.querySelectorAll('.sm\\:flex, #langSwitcher').forEach(el => {
                el.classList.add('text-white/80');
                el.classList.remove('text-stone-600');
            });
        }
    });
});
</script>
@endpush

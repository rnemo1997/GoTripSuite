@extends('layouts.app')

@section('title', __('GoTripSuite - Your Journey Starts Here'))
@section('nav-class', 'bg-transparent')
@section('logo-text-class', 'text-white')
@section('nav-links-class', 'text-white/80')

@section('content')
{{-- Hero Section --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    {{-- Background Image with Overlay --}}
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=2000&q=80"
             alt="Travel destination"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/30 to-black/60"></div>
    </div>

    {{-- Hero Content --}}
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 w-full pt-24 pb-16">
        {{-- Tagline --}}
        <div class="text-center mb-10">
            <p class="text-amber-300 text-sm font-medium tracking-[0.25em] uppercase mb-4">{{ __('Discover the world, your way') }}</p>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
                {{ __('Where will your next story begin?') }}
            </h1>
            <p class="text-lg text-white/70 max-w-xl mx-auto font-light">
                {{ __('Find handpicked hotels that match your travel dreams. Search by destination or let AI find your perfect vibe.') }}
            </p>
        </div>

        {{-- Search Card --}}
        <form action="{{ route('search') }}" method="POST" id="searchForm"
              class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl p-6 sm:p-8 space-y-5">
            @csrf

            {{-- Mode Toggle --}}
            <div class="flex bg-stone-100 rounded-xl p-1">
                <button type="button" id="btnDestination"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all mode-btn"
                    data-mode="destination">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ __('Destination') }}
                    </span>
                </button>
                <button type="button" id="btnVibe"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all mode-btn"
                    data-mode="vibe">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        {{ __('Vibe Search') }}
                    </span>
                </button>
            </div>
            <input type="hidden" name="mode" id="modeInput" value="destination">

            {{-- Destination Input --}}
            <div id="destinationField">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-stone-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="placeSearch" placeholder="Where are you going? Try &quot;Paris&quot;, &quot;Bali&quot;, &quot;Tokyo&quot;..."
                        class="w-full border border-stone-200 rounded-xl pl-12 pr-4 py-3.5 text-stone-800 placeholder-stone-400 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all"
                        autocomplete="off">
                    <input type="hidden" name="place_id" id="placeId">
                    <input type="hidden" name="place_name" id="placeName">
                    <div id="placeResults" class="absolute z-50 w-full bg-white border border-stone-200 rounded-xl shadow-xl mt-1 hidden max-h-60 overflow-y-auto"></div>
                </div>
            </div>

            {{-- Vibe Input --}}
            <div id="vibeField" class="hidden">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-stone-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <input type="text" name="vibe" id="vibeInput" placeholder="Describe your dream trip... e.g. &quot;Romantic weekend in a Parisian boutique hotel&quot;"
                        class="w-full border border-stone-200 rounded-xl pl-12 pr-4 py-3.5 text-stone-800 placeholder-stone-400 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>
            </div>

            {{-- Date + Guests Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Check-in') }}</label>
                    <input type="date" name="checkin" id="checkin" required
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Check-out') }}</label>
                    <input type="date" name="checkout" id="checkout" required
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Guests') }}</label>
                    <select name="adults" class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                        @for($i = 1; $i <= 9; $i++)
                            <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? __('Guest') : __('Guests') }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Search Button --}}
            <button type="submit" id="searchBtn"
                class="w-full bg-stone-900 text-white py-3.5 px-6 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <span id="searchBtnText" class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    {{ __('Search Hotels') }}
                </span>
                <span id="searchBtnLoading" class="hidden items-center gap-2">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    {{ __('Finding your perfect stay...') }}
                </span>
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

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/50 animate-bounce">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
    </div>
</section>

{{-- Story Section --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16">
            <p class="text-amber-600 text-sm font-medium tracking-[0.2em] uppercase mb-3">{{ __('Why GoTripSuite') }}</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">
                {{ __('Travel is the only thing you buy that makes you richer') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="text-center group">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition-colors">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">{{ __('AI-Powered Discovery') }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ __('Describe your dream trip in your own words. Our AI matches you with hotels that fit your vibe perfectly.') }}</p>
            </div>
            <div class="text-center group">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition-colors">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">{{ __('Best Rates Guaranteed') }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ __('Compare prices across hundreds of providers. Transparent pricing with no hidden fees or surprises.') }}</p>
            </div>
            <div class="text-center group">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition-colors">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">{{ __('Secure & Instant') }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ __('Book with confidence. Secure payments, instant confirmation, and flexible cancellation options.') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Inspiration Section --}}
<section class="py-20 bg-stone-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <p class="text-amber-600 text-sm font-medium tracking-[0.2em] uppercase mb-3">{{ __('Get Inspired') }}</p>
            <h2 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Try a vibe search') }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['Cozy mountain lodge with fireplace', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=600&q=80', __('Mountain Retreat')],
                ['Beachfront resort with infinity pool', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=600&q=80', __('Beach Paradise')],
                ['Boutique hotel in historic city center', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=600&q=80', __('City Boutique')],
                ['Romantic getaway with spa and vineyard', 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=600&q=80', __('Romantic Escape')],
            ] as [$vibe, $img, $label])
                <button type="button" onclick="fillVibe('{{ $vibe }}')"
                    class="group relative h-52 rounded-2xl overflow-hidden cursor-pointer text-left">
                    <img src="{{ $img }}" alt="{{ $label }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <span class="text-white font-semibold text-sm">{{ $label }}</span>
                        <p class="text-white/70 text-xs mt-0.5">"{{ $vibe }}"</p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</section>

{{-- Trip Builder CTA --}}
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <p class="text-amber-600 text-sm font-medium tracking-[0.2em] uppercase mb-3">{{ __('New Feature') }}</p>
        <h2 class="text-3xl sm:text-4xl font-bold text-stone-900 mb-4" style="font-family: 'Playfair Display', serif;">
            {{ __('Plan a multi-stop trip') }}
        </h2>
        <p class="text-stone-500 max-w-xl mx-auto mb-8">
            {{ __('Add multiple destinations, see your route on a map, find hotels at each stop, and book everything in one go.') }}
        </p>
        <a href="{{ route('trip-builder.index') }}"
           class="inline-flex items-center gap-2 bg-stone-900 text-white py-3.5 px-8 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            {{ __('Start Planning') }}
        </a>
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
function fillVibe(text) {
    document.getElementById('btnVibe').click();
    document.getElementById('vibeInput').value = text;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function() {
    const localePrefix = @json(app()->getLocale() === 'en' ? '' : '/' . app()->getLocale());
    const modeInput = document.getElementById('modeInput');
    const destField = document.getElementById('destinationField');
    const vibeField = document.getElementById('vibeField');
    const modeBtns = document.querySelectorAll('.mode-btn');
    const placeSearch = document.getElementById('placeSearch');
    const placeResults = document.getElementById('placeResults');
    const placeId = document.getElementById('placeId');
    const placeName = document.getElementById('placeName');
    const searchForm = document.getElementById('searchForm');
    const searchBtn = document.getElementById('searchBtn');

    // Set default dates (tomorrow + 3 days)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const threeDays = new Date();
    threeDays.setDate(threeDays.getDate() + 4);
    document.getElementById('checkin').value = tomorrow.toISOString().split('T')[0];
    document.getElementById('checkout').value = threeDays.toISOString().split('T')[0];
    document.getElementById('checkin').min = tomorrow.toISOString().split('T')[0];

    // Mode toggle
    modeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modeBtns.forEach(b => {
                b.classList.remove('bg-white', 'shadow-md', 'text-stone-900');
                b.classList.add('text-stone-500');
            });
            btn.classList.add('bg-white', 'shadow-md', 'text-stone-900');
            btn.classList.remove('text-stone-500');
            const mode = btn.dataset.mode;
            modeInput.value = mode;
            destField.classList.toggle('hidden', mode !== 'destination');
            vibeField.classList.toggle('hidden', mode !== 'vibe');
        });
    });

    // Init active state
    document.getElementById('btnDestination').classList.add('bg-white', 'shadow-md', 'text-stone-900');
    document.getElementById('btnVibe').classList.add('text-stone-500');

    // Places autocomplete
    let debounceTimer;
    placeSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            placeResults.classList.add('hidden');
            return;
        }
        debounceTimer = setTimeout(() => {
            fetch(`${localePrefix}/api/places?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    const places = data.data || [];
                    if (places.length === 0) {
                        placeResults.classList.add('hidden');
                        return;
                    }
                    placeResults.innerHTML = places.map(p =>
                        `<div class="px-4 py-3 hover:bg-amber-50 cursor-pointer border-b border-stone-100 last:border-0 flex items-center gap-3"
                              data-id="${p.placeId || p.id}" data-name="${p.displayName || p.name}">
                            <svg class="w-4 h-4 text-stone-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <div class="font-medium text-stone-800 text-sm">${p.displayName || p.name}</div>
                                ${p.formattedAddress ? `<div class="text-xs text-stone-400">${p.formattedAddress}</div>` : ''}
                            </div>
                        </div>`
                    ).join('');
                    placeResults.classList.remove('hidden');

                    placeResults.querySelectorAll('[data-id]').forEach(el => {
                        el.addEventListener('click', () => {
                            placeId.value = el.dataset.id;
                            placeName.value = el.dataset.name;
                            placeSearch.value = el.dataset.name;
                            placeResults.classList.add('hidden');
                        });
                    });
                });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!placeSearch.contains(e.target) && !placeResults.contains(e.target)) {
            placeResults.classList.add('hidden');
        }
    });

    // Form submit loading
    searchForm.addEventListener('submit', function() {
        searchBtn.disabled = true;
        document.getElementById('searchBtnText').classList.add('hidden');
        document.getElementById('searchBtnLoading').classList.remove('hidden');
        document.getElementById('searchBtnLoading').classList.add('flex');
    });

    // Nav background on scroll
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

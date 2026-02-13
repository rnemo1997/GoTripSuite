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
        <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=2000&q=80"
             alt="Road trip"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/30 to-black/60"></div>
    </div>

    {{-- Hero Content --}}
    <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 w-full pt-24 pb-16">
        {{-- Tagline --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-4 py-1.5 mb-6">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                <span class="text-white/90 text-sm font-medium">{{ __('Trip Builder') }}</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
                {{ __('Plan your next adventure') }}
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
                    <a href="{{ route('trip-builder.show', ['trip' => $trip]) }}"
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
                <input type="text" name="name" required placeholder="{{ __('e.g. Italy Road Trip, Southeast Asia Adventure...') }}"
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

{{-- Popular Road Trips Section --}}
<section class="py-20 bg-stone-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <p class="text-amber-600 text-sm font-medium tracking-[0.2em] uppercase mb-3">{{ __('Need inspiration?') }}</p>
            <h2 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Popular road trips') }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach([
                ['italian-classics', __('Italian Classics'), 'Rome, Florence, Venice, Milan', 'https://images.unsplash.com/photo-1515859005217-8a1f08870f59?auto=format&fit=crop&w=600&q=80', '4 ' . __('stops') . ' · 10 ' . __('nights')],
                ['thai-explorer', __('Thai Explorer'), 'Chiang Mai, Bangkok, Phuket', 'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=600&q=80', '3 ' . __('stops') . ' · 9 ' . __('nights')],
                ['spanish-coast', __('Spanish Coast'), 'Barcelona, Valencia, Seville', 'https://images.unsplash.com/photo-1583422409516-2895a77efded?auto=format&fit=crop&w=600&q=80', '3 ' . __('stops') . ' · 8 ' . __('nights')],
            ] as [$slug, $title, $routeText, $img, $details])
                <form action="{{ route('trip-builder.template', $slug) }}" method="POST" class="template-form">
                    @csrf
                    <input type="hidden" name="start_date" class="template-date-input">
                    <div class="relative h-64 rounded-2xl overflow-hidden">
                        {{-- Clickable card image --}}
                        <button type="button" class="template-card group absolute inset-0 w-full cursor-pointer text-left">
                            <img src="{{ $img }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-5">
                                <h3 class="text-white font-bold text-lg" style="font-family: 'Playfair Display', serif;">{{ $title }}</h3>
                                <p class="text-white/70 text-sm mt-1">{{ $routeText }}</p>
                                <p class="text-amber-300 text-xs font-medium mt-2">{{ $details }}</p>
                            </div>
                        </button>
                        {{-- Date picker overlay (hidden by default) --}}
                        <div class="template-date-picker absolute inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center hidden z-10">
                            <div class="bg-white rounded-xl p-5 shadow-2xl w-64 text-center" onclick="event.stopPropagation()">
                                <h4 class="font-semibold text-stone-900 text-sm mb-1" style="font-family: 'Playfair Display', serif;">{{ $title }}</h4>
                                <p class="text-xs text-stone-400 mb-4">{{ __('When do you want to leave?') }}</p>
                                <input type="date" class="template-date-field w-full border border-stone-200 rounded-lg px-3 py-2 text-sm text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 mb-3">
                                <button type="submit" class="w-full bg-stone-900 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all">
                                    {{ __('Start Planning') }}
                                </button>
                                <button type="button" class="template-cancel w-full text-stone-400 hover:text-stone-600 text-xs mt-2 py-1 transition-colors">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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
    const minDate = tomorrow.toISOString().split('T')[0];
    const el = document.getElementById('startDate');
    el.value = minDate;
    el.min = minDate;

    // Template road trip cards: click → show date picker → submit
    document.querySelectorAll('.template-form').forEach(form => {
        const card = form.querySelector('.template-card');
        const picker = form.querySelector('.template-date-picker');
        const dateField = form.querySelector('.template-date-field');
        const dateInput = form.querySelector('.template-date-input');
        const cancel = form.querySelector('.template-cancel');

        dateField.value = minDate;
        dateField.min = minDate;

        card.addEventListener('click', () => {
            picker.classList.remove('hidden');
        });

        cancel.addEventListener('click', () => {
            picker.classList.add('hidden');
        });

        picker.addEventListener('click', (e) => {
            if (e.target === picker) picker.classList.add('hidden');
        });

        form.addEventListener('submit', () => {
            dateInput.value = dateField.value;
        });
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

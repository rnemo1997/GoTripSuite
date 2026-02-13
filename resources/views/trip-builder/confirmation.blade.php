@extends('layouts.app')

@section('title', __('Trip Confirmation') . ' - GoTripSuite')

@section('content')
<div class="pt-24 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        {{-- Status header --}}
        @if($allBooked)
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-50 rounded-full mb-5">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Trip Booked!') }}</h1>
                <p class="text-stone-500 mt-2">{{ __('All :count hotels are confirmed. We\'ve sent the details to :email.', ['count' => $trip->stops->count(), 'email' => $guest['email']]) }}</p>
            </div>
        @else
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-50 rounded-full mb-5">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Partially Booked') }}</h1>
                <p class="text-stone-500 mt-2">{{ __('Some bookings could not be completed. See details below.') }}</p>
            </div>
        @endif

        {{-- Trip overview card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-stone-900 text-lg">{{ $trip->name }}</h2>
                <span class="text-sm text-stone-400">{{ $trip->adults }} {{ $trip->adults === 1 ? __('guest') : __('guests') }}</span>
            </div>

            <div class="space-y-4">
                @foreach($trip->stops as $i => $stop)
                    <div class="flex items-start gap-3 {{ !$loop->last ? 'pb-4 border-b border-stone-100' : '' }}">
                        <div class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i + 1 }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-stone-900">{{ $stop->place_name }}</h3>
                                    <p class="text-sm text-stone-500 mt-0.5">{{ $stop->hotel_name }}</p>
                                    <p class="text-xs text-stone-400 mt-1">
                                        {{ $stop->checkin->format('M d, Y') }} — {{ $stop->checkout->format('M d, Y') }}
                                        &middot; {{ $stop->nights }} {{ $stop->nights === 1 ? __('night') : __('nights') }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @if($stop->booking_status === 'booked')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold border border-emerald-200">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            {{ __('Confirmed') }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold border border-red-200 uppercase">{{ $stop->booking_status }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($stop->booking_id)
                                <div class="mt-2 bg-stone-50 rounded-lg px-3 py-2">
                                    <span class="text-xs text-stone-400 uppercase tracking-wider">{{ __('Booking ID') }}</span>
                                    <div class="font-mono font-semibold text-stone-900 text-sm">{{ $stop->booking_id }}</div>
                                </div>
                            @endif
                            @if($stop->hotel_price)
                                <p class="text-sm font-semibold text-stone-900 mt-2">{{ $stop->hotel_currency }} {{ number_format($stop->hotel_price, 2) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($trip->total_price)
                <div class="mt-4 pt-4 border-t border-stone-200 flex items-baseline justify-between">
                    <span class="text-stone-400 text-sm">{{ __('Total') }}</span>
                    <span class="text-2xl font-bold text-stone-900">{{ $trip->currency }} {{ number_format($trip->total_price, 2) }}</span>
                </div>
            @endif
        </div>

        {{-- Guest info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <h2 class="font-semibold text-stone-900 mb-3">{{ __('Guest Details') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Guest') }}</span>
                    <div class="font-semibold text-stone-900 mt-1">{{ $guest['first_name'] }} {{ $guest['last_name'] }}</div>
                </div>
                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Email') }}</span>
                    <div class="font-semibold text-stone-900 mt-1">{{ $guest['email'] }}</div>
                </div>
            </div>
        </div>

        {{-- Map --}}
        @if($trip->stops->filter(fn($s) => $s->latitude && $s->longitude)->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden mb-6">
                <div class="p-4 border-b border-stone-100">
                    <h2 class="font-semibold text-stone-900">{{ __('Your Route') }}</h2>
                </div>
                <div id="confirmationMap" class="h-72"></div>
            </div>
        @endif

        <div class="text-center pt-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-stone-900 text-white py-3.5 px-10 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ __('Plan Another Trip') }}
            </a>
        </div>
    </div>
</div>
@endsection

@if($trip->stops->filter(fn($s) => $s->latitude && $s->longitude)->count() > 0)
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .stop-marker {
        background: #d97706;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        font-family: 'Inter', sans-serif;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stops = @json($trip->stops);
    const mapEl = document.getElementById('confirmationMap');
    if (!mapEl) return;

    const map = L.map('confirmationMap', { zoomControl: false, scrollWheelZoom: false }).setView([48.8566, 2.3522], 4);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    const latlngs = [];
    stops.forEach((stop, i) => {
        if (!stop.latitude || !stop.longitude) return;
        const ll = [parseFloat(stop.latitude), parseFloat(stop.longitude)];
        latlngs.push(ll);

        const icon = L.divIcon({
            className: '',
            html: `<div class="stop-marker">${i + 1}</div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        });

        L.marker(ll, { icon }).addTo(map)
            .bindPopup(`<strong>${stop.place_name}</strong><br>${stop.hotel_name || ''}`);
    });

    if (latlngs.length > 1) {
        L.polyline(latlngs, {
            color: '#d97706',
            weight: 3,
            dashArray: '8 6',
            opacity: 0.7,
        }).addTo(map);
    }

    if (latlngs.length > 0) {
        map.fitBounds(L.latLngBounds(latlngs).pad(0.15));
    }
});
</script>
@endpush
@endif

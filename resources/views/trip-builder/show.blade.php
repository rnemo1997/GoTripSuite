@extends('layouts.app')

@section('title', $trip->name . ' - Trip Builder - GoTripSuite')

@section('content')
<div class="pt-16 h-screen flex flex-col">
    {{-- Top bar --}}
    <div class="bg-white border-b border-stone-200 px-4 sm:px-6 flex items-center justify-between flex-shrink-0 h-14">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-stone-400 hover:text-stone-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="font-semibold text-stone-900 leading-tight" style="font-family: 'Playfair Display', serif;">{{ $trip->name }}</h1>
                <p class="text-xs text-stone-400 -mt-0.5">{{ $trip->adults }} {{ $trip->adults === 1 ? __('traveller') : __('travellers') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            {{-- Progress --}}
            <div id="tripProgress" class="hidden items-center gap-2 text-xs text-stone-400">
                <div class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    <span id="stopCount">0</span> {{ __('stops') }}
                </div>
                <span class="text-stone-200">|</span>
                <div class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span id="hotelCount">0</span> {{ __('hotels') }}
                </div>
            </div>
            <div id="tripTotal" class="text-right hidden">
                <span class="text-[10px] text-stone-400 uppercase tracking-wider leading-none">{{ __('Trip total') }}</span>
                <div class="font-bold text-stone-900 text-sm leading-tight" id="totalPrice"></div>
            </div>
            <button type="button" id="bookAllBtn" disabled
                class="bg-stone-900 text-white py-2 px-5 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all text-sm disabled:opacity-30 disabled:cursor-not-allowed flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span id="bookAllText">{{ __('Book All Hotels') }}</span>
            </button>
        </div>
    </div>

    {{-- Two panel layout --}}
    <div class="flex flex-1 overflow-hidden">
        {{-- Left panel: stops --}}
        <div class="w-full lg:w-[420px] xl:w-[460px] flex-shrink-0 bg-stone-50 border-r border-stone-200 flex flex-col overflow-hidden">
            {{-- Add stop --}}
            <div class="p-4 border-b border-stone-200 bg-white">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-amber-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <input type="text" id="addStopInput" placeholder="Where to next? Search for a city or place..."
                        class="w-full border border-stone-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-stone-800 placeholder-stone-400 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all"
                        autocomplete="off">
                    <div id="addStopResults" class="absolute z-50 w-full bg-white border border-stone-200 rounded-xl shadow-xl mt-1 hidden max-h-48 overflow-y-auto"></div>
                </div>
            </div>

            {{-- Stops list --}}
            <div id="stopsList" class="flex-1 overflow-y-auto">
                {{-- Rendered by JS --}}
            </div>
        </div>

        {{-- Right panel: map --}}
        <div class="hidden lg:block flex-1 relative">
            <div id="map" class="absolute inset-0"></div>
            {{-- Empty state --}}
            <div id="mapEmpty" class="absolute inset-0 flex items-center justify-center" style="background: linear-gradient(135deg, #f5f0ea 0%, #e8e0d6 50%, #d9d0c4 100%);">
                <div class="text-center max-w-sm">
                    {{-- Decorative map illustration --}}
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <div class="absolute inset-0 bg-amber-100 rounded-full opacity-50 animate-pulse"></div>
                        <div class="absolute inset-3 bg-amber-50 rounded-full flex items-center justify-center">
                            <svg class="w-14 h-14 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        </div>
                        {{-- Floating pins --}}
                        <div class="absolute top-1 right-1 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-md" style="animation: float 3s ease-in-out infinite;">1</div>
                        <div class="absolute bottom-2 left-0 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-md" style="animation: float 3s ease-in-out infinite 1s;">2</div>
                        <div class="absolute bottom-0 right-4 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-md" style="animation: float 3s ease-in-out infinite 2s;">3</div>
                    </div>
                    <h3 class="text-stone-700 font-semibold text-lg mb-2" style="font-family: 'Playfair Display', serif;">{{ __('Your adventure starts here') }}</h3>
                    <p class="text-stone-400 text-sm leading-relaxed">{{ __('Search for your first destination and watch your route come alive on the map.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hotel search slide-over --}}
<div id="hotelSlideOver" class="fixed inset-0 z-[9999] hidden">
    <div id="hotelOverlay" class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity"></div>
    <div id="hotelPanel" class="absolute right-0 top-0 bottom-0 w-full sm:w-[440px] bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-out">
        <div class="p-4 border-b border-stone-200 flex items-center justify-between flex-shrink-0 bg-stone-50">
            <div>
                <h2 class="font-semibold text-stone-900" id="hotelPanelTitle" style="font-family: 'Playfair Display', serif;">{{ __('Hotels') }}</h2>
                <p class="text-xs text-stone-400 mt-0.5" id="hotelPanelDates"></p>
            </div>
            <button type="button" id="closeHotelPanel" class="text-stone-400 hover:text-stone-600 transition-colors p-1.5 hover:bg-stone-200 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="hotelResults" class="flex-1 overflow-y-auto p-4">
            {{-- Filled by JS --}}
        </div>
    </div>
</div>

{{-- Toast notification --}}
<div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">
    <div class="bg-stone-900 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2">
        <svg id="toastIcon" class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="toastText"></span>
    </div>
</div>
@endsection

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .stop-marker {
        background: #d97706;
        color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        border: 3px solid white;
        box-shadow: 0 2px 12px rgba(0,0,0,0.25);
        font-family: 'Inter', sans-serif;
        transition: transform 0.2s;
    }
    .stop-marker:hover { transform: scale(1.2); }
    .stop-marker-sm {
        background: #d97706;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 11px;
        border: 2px solid white;
        box-shadow: 0 1px 6px rgba(0,0,0,0.2);
        font-family: 'Inter', sans-serif;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-6px); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stop-card { animation: slideIn 0.3s ease-out; }
    .timeline-line {
        position: absolute;
        left: 23px;
        top: 40px;
        bottom: 0;
        width: 2px;
        background: repeating-linear-gradient(
            to bottom,
            #d97706 0px,
            #d97706 4px,
            transparent 4px,
            transparent 8px
        );
    }
    /* Leaflet popup overrides */
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
    }
    .leaflet-popup-content {
        margin: 10px 14px !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 13px !important;
    }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { scrollbar-width: none; -ms-overflow-style: none; }
    .line-clamp-4 {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const localePrefix = @json(app()->getLocale() === 'en' ? '' : '/' . app()->getLocale());
    const tripId = @json($trip->id);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let stops = @json($trip->stops);
    let map = null;
    let markers = [];
    let routeLine = null;
    let currentSearchStopId = null;
    let lastHotelResults = [];

    // ─── Locale-aware date formatting ───
    const jsLocale = '{{ app()->getLocale() === "en" ? "en-US" : app()->getLocale() }}';
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString(jsLocale, { month: 'short', day: 'numeric' });
    }

    // ─── i18n strings used in JS ───
    const i18n = {
        night: '{{ __('night') }}',
        nights: '{{ __('nights') }}',
        noHotelSelectedYet: '{{ __('No hotel selected yet') }}',
        dragToReorder: '{{ __('Drag to reorder') }}',
        removeStop: '{{ __('Remove stop') }}',
        change: '{{ __('Change') }}',
        findHotel: '{{ __('Find hotel') }}',
        addedToYourTrip: '{{ __('added to your trip') }}',
        removed: '{{ __('removed') }}',
        routeUpdated: '{{ __('Route updated') }}',
        hotelsInPlace: '{{ __('Hotels in :place') }}',
        searchingHotelsInPlace: '{{ __('Searching hotels in :place...') }}',
        thisMayTakeAFewSeconds: '{{ __('This may take a few seconds') }}',
        noHotelsFound: '{{ __('No hotels found') }}',
        tryAdjustingTheNumberOfNights: '{{ __('Try adjusting the number of nights') }}',
        somethingWentWrong: '{{ __('Something went wrong') }}',
        tryAgain: '{{ __('Try again') }}',
        hotelDetails: '{{ __('Hotel Details') }}',
        loadingHotelDetails: '{{ __('Loading hotel details...') }}',
        backToResults: '{{ __('Back to results') }}',
        couldNotLoadHotelDetails: '{{ __('Could not load hotel details') }}',
        facilities: '{{ __('Facilities') }}',
        aboutThisHotel: '{{ __('About this hotel') }}',
        roomsAndRates: '{{ __('Rooms & Rates') }}',
        noRoomInformationAvailable: '{{ __('No room information available') }}',
        freeCancellation: '{{ __('Free cancellation') }}',
        nonRefundable: '{{ __('Non-refundable') }}',
        plusTaxesAndFees: '{{ __('+ taxes & fees') }}',
        totalStay: '{{ __('total stay') }}',
        select: '{{ __('Select') }}',
        from: '{{ __('From') }}',
        viewRooms: '{{ __('View rooms') }}',
        processing: '{{ __('Processing...') }}',
        checkoutFailed: '{{ __('Checkout failed') }}',
        anErrorOccurred: '{{ __('An error occurred') }}',
        selected: '{{ __('selected') }}',
        addYourFirstStop: '{{ __('Add your first stop') }}',
        searchForACityOrDestination: '{{ __('Search for a city or destination in the bar above to start building your trip.') }}',
        found: '{{ __('found') }}',
        bookAllHotels: '{{ __('Book All Hotels') }}',
    };

    // ─── TOAST ───
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        const text = document.getElementById('toastText');
        text.textContent = message;
        icon.className = type === 'success'
            ? 'w-4 h-4 text-emerald-400'
            : 'w-4 h-4 text-red-400';
        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
        }, 2500);
    }

    // ─── MAP INIT ───
    function initMap() {
        map = L.map('map', { zoomControl: false }).setView([35, 15], 3);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            maxZoom: 19,
            subdomains: 'abcd',
        }).addTo(map);
        L.control.zoom({ position: 'bottomright' }).addTo(map);
    }
    initMap();

    function updateMap() {
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        if (routeLine) { map.removeLayer(routeLine); routeLine = null; }

        const mapEmpty = document.getElementById('mapEmpty');
        const stopsWithCoords = stops.filter(s => s.latitude && s.longitude);

        if (stopsWithCoords.length === 0) {
            mapEmpty.classList.remove('hidden');
            return;
        }
        mapEmpty.classList.add('hidden');

        const latlngs = [];
        stopsWithCoords.forEach((stop, i) => {
            const ll = [parseFloat(stop.latitude), parseFloat(stop.longitude)];
            latlngs.push(ll);

            const hasHotel = !!stop.hotel_id;
            const icon = L.divIcon({
                className: '',
                html: `<div class="stop-marker" style="${hasHotel ? 'background:#059669;' : ''}">${i + 1}</div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            const popupContent = `
                <div style="min-width:160px;">
                    <strong style="font-size:14px;">${stop.place_name}</strong>
                    <div style="color:#78716c;font-size:11px;margin-top:2px;">${formatDate(stop.checkin)} — ${formatDate(stop.checkout)} &middot; ${stop.nights} ${stop.nights === 1 ? i18n.night : i18n.nights}</div>
                    ${stop.hotel_name ? `<div style="margin-top:6px;padding-top:6px;border-top:1px solid #e7e5e4;font-size:12px;color:#44403c;"><strong>${stop.hotel_name}</strong><br><span style="color:#d97706;font-weight:600;">${stop.hotel_currency} ${parseFloat(stop.hotel_price).toFixed(2)}</span></div>` : `<div style="margin-top:4px;font-size:11px;color:#d97706;">${i18n.noHotelSelectedYet}</div>`}
                </div>`;

            const marker = L.marker(ll, { icon })
                .addTo(map)
                .bindPopup(popupContent);
            markers.push(marker);
        });

        if (latlngs.length > 1) {
            routeLine = L.polyline(latlngs, {
                color: '#d97706',
                weight: 3,
                dashArray: '10 8',
                opacity: 0.6,
                lineCap: 'round',
            }).addTo(map);
        }

        map.fitBounds(L.latLngBounds(latlngs).pad(0.2), { animate: true, duration: 0.5 });
    }

    // ─── RENDER STOPS ───
    function renderStops() {
        const list = document.getElementById('stopsList');

        // Update progress
        const progress = document.getElementById('tripProgress');
        const stopCountEl = document.getElementById('stopCount');
        const hotelCountEl = document.getElementById('hotelCount');
        const hotelsSelected = stops.filter(s => s.hotel_id).length;

        if (stops.length > 0) {
            progress.classList.remove('hidden');
            progress.classList.add('flex');
            stopCountEl.textContent = stops.length;
            hotelCountEl.textContent = `${hotelsSelected}/${stops.length}`;
        } else {
            progress.classList.add('hidden');
            progress.classList.remove('flex');
        }

        if (stops.length === 0) {
            list.innerHTML = `
                <div class="flex items-center justify-center h-full p-8">
                    <div class="text-center max-w-xs">
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-9 h-9 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="text-stone-700 font-semibold mb-1" style="font-family: 'Playfair Display', serif;">${i18n.addYourFirstStop}</h3>
                        <p class="text-stone-400 text-sm leading-relaxed">${i18n.searchForACityOrDestination}</p>
                    </div>
                </div>`;
            updateTripTotal();
            updateMap();
            return;
        }

        list.innerHTML = '<div class="p-4 space-y-0">' + stops.map((stop, i) => {
            const hasHotel = !!stop.hotel_id;
            const isLast = i === stops.length - 1;

            return `
                <div class="stop-card relative" data-stop-id="${stop.id}" draggable="true">
                    ${!isLast ? '<div class="timeline-line"></div>' : ''}
                    <div class="flex gap-3 pb-5">
                        {{-- Marker + drag handle --}}
                        <div class="flex flex-col items-center flex-shrink-0 cursor-grab active:cursor-grabbing" title="${i18n.dragToReorder}">
                            <div class="stop-marker-sm ${hasHotel ? '!bg-emerald-600' : ''}">${i + 1}</div>
                        </div>
                        {{-- Card --}}
                        <div class="flex-1 bg-white rounded-xl border border-stone-200 overflow-hidden hover:shadow-md hover:border-stone-300 transition-all">
                            <div class="p-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-stone-900 text-sm truncate">${stop.place_name}</h3>
                                        <p class="text-xs text-stone-400 mt-0.5 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            ${formatDate(stop.checkin)} — ${formatDate(stop.checkout)}
                                        </p>
                                    </div>
                                    <button type="button" onclick="removeStop(${stop.id})" class="text-stone-300 hover:text-red-500 transition-colors flex-shrink-0 p-0.5" title="${i18n.removeStop}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-2 mt-2.5">
                                    <div class="flex items-center gap-1.5 bg-stone-50 rounded-lg px-2 py-1">
                                        <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                        <select onchange="updateNights(${stop.id}, this.value)" class="bg-transparent text-xs text-stone-700 font-medium border-0 p-0 pr-5 focus:ring-0 cursor-pointer">
                                            ${[1,2,3,4,5,6,7].map(n => `<option value="${n}" ${n === stop.nights ? 'selected' : ''}>${n} ${n === 1 ? i18n.night : i18n.nights}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="flex-1"></div>
                                    <button type="button" onclick="searchHotels(${stop.id})"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all ${hasHotel ? 'text-stone-500 hover:text-amber-600 hover:bg-amber-50' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        ${hasHotel ? i18n.change : i18n.findHotel}
                                    </button>
                                </div>
                            </div>
                            ${hasHotel ? `
                                <div class="px-3.5 pb-3.5">
                                    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2.5 flex items-center gap-2.5">
                                        ${stop.hotel_photo ? `<img src="${stop.hotel_photo}" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">` : `<div class="w-10 h-10 rounded-lg bg-emerald-100 flex-shrink-0 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div>`}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-emerald-900 truncate">${stop.hotel_name}</p>
                                            <p class="text-xs font-bold text-emerald-700">${stop.hotel_currency} ${parseFloat(stop.hotel_price).toFixed(2)}</p>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>`;
        }).join('') + '</div>';

        updateTripTotal();
        updateMap();
        initDragAndDrop();
    }

    function updateTripTotal() {
        const totalEl = document.getElementById('tripTotal');
        const priceEl = document.getElementById('totalPrice');
        const bookBtn = document.getElementById('bookAllBtn');

        const stopsWithHotel = stops.filter(s => s.hotel_price);
        const total = stopsWithHotel.reduce((sum, s) => sum + parseFloat(s.hotel_price), 0);
        const allHaveHotels = stops.length > 0 && stopsWithHotel.length === stops.length;

        if (stopsWithHotel.length > 0) {
            const currency = stopsWithHotel[0].hotel_currency || 'USD';
            priceEl.textContent = `${currency} ${total.toFixed(2)}`;
            totalEl.classList.remove('hidden');
        } else {
            totalEl.classList.add('hidden');
        }

        bookBtn.disabled = !allHaveHotels;
    }

    // ─── API HELPERS ───
    async function apiPost(url, data) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data),
        });
        return res.json();
    }

    async function apiPut(url, data) {
        const res = await fetch(url, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data),
        });
        return res.json();
    }

    async function apiDelete(url) {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        return res.json();
    }

    async function apiGet(url) {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        return res.json();
    }

    // ─── PLACES AUTOCOMPLETE ───
    const addStopInput = document.getElementById('addStopInput');
    const addStopResults = document.getElementById('addStopResults');
    let debounceTimer;

    addStopInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) { addStopResults.classList.add('hidden'); return; }

        debounceTimer = setTimeout(async () => {
            try {
                const data = await apiGet(`${localePrefix}/api/places?q=${encodeURIComponent(q)}`);
                const places = data.data || [];
                if (places.length === 0) { addStopResults.classList.add('hidden'); return; }

                addStopResults.innerHTML = places.map(p => `
                    <div class="px-4 py-2.5 hover:bg-amber-50 cursor-pointer border-b border-stone-100 last:border-0 text-sm flex items-center gap-3 transition-colors"
                         data-id="${p.placeId || p.id}" data-name="${p.displayName || p.name}"
                         data-lat="${p.latitude || ''}" data-lng="${p.longitude || ''}">
                        <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <div>
                            <div class="font-medium text-stone-800">${p.displayName || p.name}</div>
                            ${p.formattedAddress ? `<div class="text-xs text-stone-400">${p.formattedAddress}</div>` : ''}
                        </div>
                    </div>
                `).join('');
                addStopResults.classList.remove('hidden');

                addStopResults.querySelectorAll('[data-id]').forEach(el => {
                    el.addEventListener('click', () => addStop(el.dataset.id, el.dataset.name, el.dataset.lat, el.dataset.lng));
                });
            } catch (e) { console.error(e); }
        }, 300);
    });

    addStopInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            this.dispatchEvent(new Event('input'));
        }
    });

    document.addEventListener('click', (e) => {
        if (!addStopInput.contains(e.target) && !addStopResults.contains(e.target)) {
            addStopResults.classList.add('hidden');
        }
    });

    // ─── STOP CRUD ───
    async function addStop(placeId, placeName, lat, lng) {
        addStopInput.value = '';
        addStopResults.classList.add('hidden');

        // If no lat/lng from places API, geocode via Nominatim
        if (!lat || !lng || lat === '' || lng === '') {
            try {
                const geo = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(placeName)}&limit=1`);
                const geoData = await geo.json();
                if (geoData.length > 0) {
                    lat = geoData[0].lat;
                    lng = geoData[0].lon;
                }
            } catch (e) { console.error('Geocoding failed:', e); }
        }

        const data = await apiPost(`${localePrefix}/trip-builder/${tripId}/stops`, {
            place_id: placeId,
            place_name: placeName,
            latitude: lat || null,
            longitude: lng || null,
        });

        if (data.success) {
            stops = data.stops;
            renderStops();
            showToast(`${placeName} ${i18n.addedToYourTrip}`);
        }
    }
    window.addStop = addStop;

    async function updateNights(stopId, nights) {
        const data = await apiPut(`${localePrefix}/trip-builder/${tripId}/stops/${stopId}`, { nights: parseInt(nights) });
        if (data.success) {
            stops = data.stops;
            renderStops();
        }
    }
    window.updateNights = updateNights;

    async function removeStop(stopId) {
        const stop = stops.find(s => s.id === stopId);
        const data = await apiDelete(`${localePrefix}/trip-builder/${tripId}/stops/${stopId}`);
        if (data.success) {
            stops = data.stops;
            renderStops();
            showToast(`${stop ? stop.place_name : 'Stop'} ${i18n.removed}`);
        }
    }
    window.removeStop = removeStop;

    // ─── DRAG AND DROP REORDER ───
    function initDragAndDrop() {
        const items = document.querySelectorAll('[data-stop-id]');
        let draggedId = null;

        items.forEach(item => {
            item.addEventListener('dragstart', (e) => {
                draggedId = item.dataset.stopId;
                item.style.opacity = '0.4';
                e.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', () => {
                item.style.opacity = '1';
                document.querySelectorAll('[data-stop-id]').forEach(el => {
                    el.querySelector('.bg-white, .rounded-xl')?.classList.remove('ring-2', 'ring-amber-400');
                });
            });

            item.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                item.querySelector('.bg-white')?.classList.add('ring-2', 'ring-amber-400');
            });

            item.addEventListener('dragleave', () => {
                item.querySelector('.bg-white')?.classList.remove('ring-2', 'ring-amber-400');
            });

            item.addEventListener('drop', async (e) => {
                e.preventDefault();
                item.querySelector('.bg-white')?.classList.remove('ring-2', 'ring-amber-400');
                const targetId = item.dataset.stopId;
                if (draggedId === targetId) return;

                const draggedIndex = stops.findIndex(s => s.id == draggedId);
                const targetIndex = stops.findIndex(s => s.id == targetId);
                const [moved] = stops.splice(draggedIndex, 1);
                stops.splice(targetIndex, 0, moved);

                const order = stops.map(s => s.id);
                const data = await apiPost(`${localePrefix}/trip-builder/${tripId}/reorder`, { order });
                if (data.success) {
                    stops = data.stops;
                }
                renderStops();
                showToast(i18n.routeUpdated);
            });
        });
    }

    // ─── HOTEL SEARCH SLIDE-OVER ───
    const slideOver = document.getElementById('hotelSlideOver');
    const hotelPanel = document.getElementById('hotelPanel');
    const hotelOverlay = document.getElementById('hotelOverlay');

    function openHotelPanel() {
        slideOver.classList.remove('hidden');
        requestAnimationFrame(() => {
            hotelPanel.classList.remove('translate-x-full');
        });
    }

    function closeHotelPanel() {
        hotelPanel.classList.add('translate-x-full');
        setTimeout(() => slideOver.classList.add('hidden'), 300);
    }

    document.getElementById('closeHotelPanel').addEventListener('click', closeHotelPanel);
    hotelOverlay.addEventListener('click', closeHotelPanel);

    async function searchHotels(stopId) {
        currentSearchStopId = stopId;
        const stop = stops.find(s => s.id === stopId);
        if (!stop) return;

        document.getElementById('hotelPanelTitle').textContent = i18n.hotelsInPlace.replace(':place', stop.place_name);
        document.getElementById('hotelPanelDates').textContent = `${formatDate(stop.checkin)} — ${formatDate(stop.checkout)} · ${stop.nights} ${stop.nights === 1 ? i18n.night : i18n.nights}`;

        document.getElementById('hotelResults').innerHTML = `
            <div class="flex flex-col items-center justify-center h-64">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 bg-amber-100 rounded-full animate-ping opacity-30"></div>
                    <div class="relative w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <p class="text-stone-500 font-medium text-sm">${i18n.searchingHotelsInPlace.replace(':place', stop.place_name)}</p>
                <p class="text-stone-300 text-xs mt-1">${i18n.thisMayTakeAFewSeconds}</p>
            </div>`;

        openHotelPanel();

        try {
            const data = await apiGet(`${localePrefix}/trip-builder/${tripId}/stops/${stopId}/hotels`);
            const hotels = data.hotels || [];

            if (hotels.length === 0) {
                document.getElementById('hotelResults').innerHTML = `
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <p class="text-stone-600 font-medium">${i18n.noHotelsFound}</p>
                        <p class="text-stone-400 text-sm mt-1">${i18n.tryAdjustingTheNumberOfNights}</p>
                    </div>`;
                return;
            }

            lastHotelResults = hotels;
            renderHotelList(hotels);
        } catch (e) {
            console.error(e);
            document.getElementById('hotelResults').innerHTML = `
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    </div>
                    <p class="text-stone-600 font-medium">${i18n.somethingWentWrong}</p>
                    <button type="button" onclick="searchHotels(${currentSearchStopId})" class="text-amber-600 hover:text-amber-700 text-sm font-medium mt-2">${i18n.tryAgain}</button>
                </div>`;
        }
    }
    window.searchHotels = searchHotels;

    function renderHotelList(hotels) {
        document.getElementById('hotelResults').innerHTML = `
            <p class="text-xs text-stone-400 mb-3">${hotels.length} hotel${hotels.length !== 1 ? 's' : ''} ${i18n.found}</p>
        ` + hotels.map((h, idx) => `
            <div class="rounded-xl border border-stone-200 overflow-hidden mb-3 hover:shadow-md hover:border-stone-300 transition-all cursor-pointer" style="animation: slideIn ${0.15 + idx * 0.05}s ease-out;" onclick="viewHotelDetail(${currentSearchStopId}, '${h.hotelId}')">
                ${h.photo ? `<div class="h-32 overflow-hidden"><img src="${h.photo}" alt="" class="w-full h-full object-cover"></div>` : ''}
                <div class="p-3.5">
                    <h3 class="font-semibold text-stone-900 text-sm">${h.name}</h3>
                    ${h.starRating ? `<div class="flex items-center gap-0.5 mt-1">${'<svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'.repeat(parseInt(h.starRating))}</div>` : ''}
                    ${h.address ? `<p class="text-xs text-stone-400 mt-1">${h.address}</p>` : ''}
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-stone-100">
                        <div>
                            <span class="text-xs text-stone-400">${i18n.from}</span>
                            <div class="font-bold text-stone-900">${h.currency} ${parseFloat(h.price).toFixed(2)}</div>
                        </div>
                        <span class="inline-flex items-center gap-1 text-amber-600 text-xs font-semibold">
                            ${i18n.viewRooms}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // ─── HOTEL DETAIL VIEW ───
    async function viewHotelDetail(stopId, hotelId) {
        currentSearchStopId = stopId;
        const stop = stops.find(s => s.id === stopId);

        // Show loading in the panel
        document.getElementById('hotelPanelTitle').textContent = i18n.hotelDetails;
        document.getElementById('hotelPanelDates').textContent = stop ? `${formatDate(stop.checkin)} — ${formatDate(stop.checkout)} · ${stop.nights} ${stop.nights === 1 ? i18n.night : i18n.nights}` : '';

        document.getElementById('hotelResults').innerHTML = `
            <div class="flex flex-col items-center justify-center h-64">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 bg-amber-100 rounded-full animate-ping opacity-30"></div>
                    <div class="relative w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <p class="text-stone-500 font-medium text-sm">${i18n.loadingHotelDetails}</p>
            </div>`;

        if (!slideOver.classList.contains('hidden') === false) {
            openHotelPanel();
        }

        try {
            const data = await apiGet(`${localePrefix}/trip-builder/${tripId}/stops/${stopId}/hotels/${hotelId}`);
            renderHotelDetail(data, stopId);
        } catch (e) {
            console.error(e);
            document.getElementById('hotelResults').innerHTML = `
                <div class="text-center py-12">
                    <button type="button" onclick="backToHotelList()" class="flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 font-medium mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        ${i18n.backToResults}
                    </button>
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    </div>
                    <p class="text-stone-600 font-medium">${i18n.couldNotLoadHotelDetails}</p>
                    <button type="button" onclick="viewHotelDetail(${stopId}, '${hotelId}')" class="text-amber-600 hover:text-amber-700 text-sm font-medium mt-2">${i18n.tryAgain}</button>
                </div>`;
        }
    }
    window.viewHotelDetail = viewHotelDetail;

    function renderHotelDetail(data, stopId) {
        const hotel = data.hotel;
        const rooms = data.rooms || [];

        const starHtml = hotel.starRating ? '<svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'.repeat(parseInt(hotel.starRating)) : '';

        const ratingBadge = hotel.rating ? `
            <div class="inline-flex items-center gap-1.5 bg-amber-50 px-2.5 py-1 rounded-lg">
                <span class="text-sm font-bold text-amber-700">${parseFloat(hotel.rating).toFixed(1)}</span>
                ${hotel.reviewCount ? `<span class="text-xs text-stone-400">(${hotel.reviewCount} reviews)</span>` : ''}
            </div>` : '';

        // Images carousel
        const allImages = [];
        if (hotel.photo) allImages.push(hotel.photo);
        (hotel.images || []).forEach(img => { if (img && img !== hotel.photo) allImages.push(img); });

        const imagesHtml = allImages.length > 0 ? `
            <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
                ${allImages.map((img, i) => `
                    <div class="flex-shrink-0 ${i === 0 ? 'w-64 h-44' : 'w-40 h-44'} rounded-xl overflow-hidden">
                        <img src="${img}" alt="" class="w-full h-full object-cover" loading="lazy">
                    </div>
                `).join('')}
            </div>` : '';

        // Facilities chips
        const facilitiesHtml = (hotel.facilities || []).length > 0 ? `
            <div class="mt-4">
                <h4 class="text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">${i18n.facilities}</h4>
                <div class="flex flex-wrap gap-1.5">
                    ${hotel.facilities.map(f => `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-stone-100 text-stone-600 rounded-full text-xs">${f}</span>`).join('')}
                </div>
            </div>` : '';

        // Description
        const descHtml = hotel.description ? `
            <div class="mt-4">
                <h4 class="text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">${i18n.aboutThisHotel}</h4>
                <p class="text-sm text-stone-600 leading-relaxed line-clamp-4">${hotel.description}</p>
            </div>` : '';

        // Rooms & offers
        const roomsHtml = rooms.length > 0 ? `
            <div class="mt-5">
                <h4 class="text-xs font-semibold text-stone-500 uppercase tracking-wider mb-3">${i18n.roomsAndRates}</h4>
                <div class="space-y-3">
                    ${rooms.map(room => `
                        <div class="border border-stone-200 rounded-xl overflow-hidden">
                            ${room.image ? `
                                <div class="flex gap-3 p-3 bg-stone-50 border-b border-stone-100">
                                    <img src="${room.image}" alt="" class="w-20 h-16 rounded-lg object-cover flex-shrink-0">
                                    <div class="min-w-0">
                                        <h5 class="font-semibold text-stone-900 text-sm truncate">${room.name}</h5>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-stone-400">
                                            ${room.maxOccupancy ? `<span class="flex items-center gap-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> Max ${room.maxOccupancy}</span>` : ''}
                                            ${room.size ? `<span>${room.size}</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            ` : `
                                <div class="p-3 bg-stone-50 border-b border-stone-100">
                                    <h5 class="font-semibold text-stone-900 text-sm">${room.name}</h5>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-stone-400">
                                        ${room.maxOccupancy ? `<span class="flex items-center gap-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> Max ${room.maxOccupancy}</span>` : ''}
                                        ${room.size ? `<span>${room.size}</span>` : ''}
                                    </div>
                                </div>
                            `}
                            ${room.amenities && room.amenities.length > 0 ? `
                                <div class="px-3 pt-2 pb-1">
                                    <div class="flex flex-wrap gap-1">
                                        ${room.amenities.map(a => `<span class="text-[10px] text-stone-400 bg-stone-50 px-2 py-0.5 rounded-full">${a}</span>`).join('')}
                                    </div>
                                </div>
                            ` : ''}
                            <div class="divide-y divide-stone-100">
                                ${room.offers.map(offer => `
                                    <div class="px-3 py-2.5 flex items-center justify-between gap-3 hover:bg-stone-50 transition-colors">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-xs font-medium text-stone-700">${offer.boardName}</span>
                                                ${offer.refundable === 'RFN' || offer.refundable === 'RFND'
                                                    ? `<span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">${i18n.freeCancellation}</span>`
                                                    : `<span class="text-[10px] font-medium text-stone-400 bg-stone-100 px-1.5 py-0.5 rounded">${i18n.nonRefundable}</span>`}
                                            </div>
                                            ${!offer.taxesIncluded ? `<p class="text-[10px] text-stone-400 mt-0.5">${i18n.plusTaxesAndFees}</p>` : ''}
                                        </div>
                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <div class="text-right">
                                                <div class="font-bold text-stone-900 text-sm">${offer.currency} ${parseFloat(offer.price).toFixed(2)}</div>
                                                <span class="text-[10px] text-stone-400">${i18n.totalStay}</span>
                                            </div>
                                            <button type="button" onclick="event.stopPropagation(); selectHotel(${stopId}, '${hotel.id}', '${escapeJs(hotel.name)}', '${hotel.photo || ''}', '${offer.offerId}', ${offer.price}, '${offer.currency}')"
                                                class="bg-stone-900 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-stone-800 active:scale-[0.97] transition-all whitespace-nowrap">
                                                ${i18n.select}
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>` : `
            <div class="mt-5 text-center py-8">
                <p class="text-stone-400 text-sm">${i18n.noRoomInformationAvailable}</p>
            </div>`;

        document.getElementById('hotelPanelTitle').textContent = hotel.name;

        document.getElementById('hotelResults').innerHTML = `
            <div>
                <button type="button" onclick="backToHotelList()" class="flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 font-medium mb-4 group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    ${i18n.backToResults}
                </button>

                ${imagesHtml}

                <div class="mt-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-stone-900 text-lg leading-snug" style="font-family: 'Playfair Display', serif;">${hotel.name}</h3>
                            ${starHtml ? `<div class="flex items-center gap-0.5 mt-1">${starHtml}</div>` : ''}
                            ${hotel.address ? `<p class="text-xs text-stone-400 mt-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> ${hotel.address}${hotel.city ? `, ${hotel.city}` : ''}</p>` : ''}
                        </div>
                        ${ratingBadge}
                    </div>

                    ${descHtml}
                    ${facilitiesHtml}
                    ${roomsHtml}
                </div>
            </div>`;
    }

    function backToHotelList() {
        if (lastHotelResults.length > 0 && currentSearchStopId) {
            const stop = stops.find(s => s.id === currentSearchStopId);
            document.getElementById('hotelPanelTitle').textContent = i18n.hotelsInPlace.replace(':place', stop ? stop.place_name : '');
            document.getElementById('hotelPanelDates').textContent = stop ? `${formatDate(stop.checkin)} — ${formatDate(stop.checkout)} · ${stop.nights} ${stop.nights === 1 ? i18n.night : i18n.nights}` : '';

            renderHotelList(lastHotelResults);
        } else {
            // Re-fetch if no cache
            searchHotels(currentSearchStopId);
        }
    }
    window.backToHotelList = backToHotelList;
    window.viewHotelDetail = viewHotelDetail;

    function escapeJs(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    async function selectHotel(stopId, hotelId, hotelName, hotelPhoto, offerId, price, currency) {
        const data = await apiPost(`${localePrefix}/trip-builder/${tripId}/stops/${stopId}/select`, {
            hotel_id: hotelId,
            hotel_name: hotelName,
            hotel_photo: hotelPhoto,
            offer_id: offerId,
            hotel_price: price,
            hotel_currency: currency,
        });

        if (data.success) {
            stops = stops.map(s => s.id === stopId ? data.stop : s);
            renderStops();
            closeHotelPanel();
            showToast(`${hotelName} ${i18n.selected}`);
        }
    }
    window.selectHotel = selectHotel;

    // ─── BOOK ALL ───
    document.getElementById('bookAllBtn').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        document.getElementById('bookAllText').textContent = i18n.processing;

        try {
            const data = await apiPost(`${localePrefix}/trip-builder/${tripId}/checkout`, {});

            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                showToast(data.error || i18n.checkoutFailed, 'error');
                btn.disabled = false;
                document.getElementById('bookAllText').textContent = i18n.bookAllHotels;
            }
        } catch (e) {
            showToast(i18n.anErrorOccurred, 'error');
            btn.disabled = false;
            document.getElementById('bookAllText').textContent = i18n.bookAllHotels;
        }
    });

    // ─── INITIAL RENDER ───
    renderStops();
});
</script>
@endpush

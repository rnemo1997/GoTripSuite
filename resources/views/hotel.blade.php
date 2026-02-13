@extends('layouts.app')

@section('title', ($hotel['name'] ?? 'Hotel') . ' - GoTripSuite')

@section('content')
<div class="pt-20 pb-16">
    {{-- Hero Image --}}
    @php
        $images = $hotel['hotelImages'] ?? [];
        $mainPhoto = $hotel['main_photo'] ?? ($images[0]['url'] ?? null);
    @endphp

    @if($mainPhoto)
        <div class="relative h-72 sm:h-96 lg:h-[28rem] overflow-hidden">
            <img src="{{ $mainPhoto }}" alt="{{ $hotel['name'] ?? '' }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/20"></div>

            {{-- Back button --}}
            <div class="absolute top-6 left-6">
                <a href="javascript:history.back()" class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm text-stone-800 px-4 py-2 rounded-xl text-sm font-medium hover:bg-white transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    {{ __('Back') }}
                </a>
            </div>

            {{-- Hotel name overlay --}}
            <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
                <div class="max-w-7xl mx-auto">
                    @if($hotel['starRating'] ?? null)
                        <div class="flex items-center gap-0.5 mb-2">
                            @for($i = 0; $i < (int)$hotel['starRating']; $i++)
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>
                    @endif
                    <h1 class="text-3xl sm:text-4xl font-bold text-white" style="font-family: 'Playfair Display', serif;">{{ $hotel['name'] ?? 'Hotel' }}</h1>
                    @if($hotel['address'] ?? null)
                        <p class="text-white/80 mt-1 flex items-center gap-1.5 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            {{ $hotel['address'] }}{{ isset($hotel['city']) ? ', ' . $hotel['city'] : '' }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Stay info bar --}}
        <div class="flex flex-wrap items-center gap-4 py-5 border-b border-stone-200 text-sm text-stone-600">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ \Carbon\Carbon::parse($checkin)->format('M d') }} - {{ \Carbon\Carbon::parse($checkout)->format('M d, Y') }}
            </span>
            <span class="text-stone-300">&middot;</span>
            <span>{{ $adults }} {{ $adults === 1 ? __('guest') : __('guests') }}</span>
            @if($hotel['rating'] ?? null)
                <span class="text-stone-300">&middot;</span>
                <span class="inline-flex items-center gap-1 font-semibold text-stone-800">
                    <span class="bg-stone-900 text-white text-xs px-1.5 py-0.5 rounded">{{ number_format((float)$hotel['rating'], 1) }}</span>
                    @if(($hotel['reviewCount'] ?? 0) > 0)
                        <span class="font-normal text-stone-500">{{ $hotel['reviewCount'] }} {{ __('reviews') }}</span>
                    @endif
                </span>
            @endif
        </div>

        {{-- Hotel description & facilities --}}
        @if(($hotel['hotelDescription'] ?? null) || !empty($hotel['hotelFacilities'] ?? []))
            <div class="py-6 border-b border-stone-200">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    @if($hotel['hotelDescription'] ?? null)
                        <div class="lg:col-span-2">
                            <h2 class="text-lg font-semibold text-stone-900 mb-3">{{ __('About this hotel') }}</h2>
                            <div class="text-sm text-stone-600 leading-relaxed max-h-40 overflow-y-auto prose prose-sm prose-stone">
                                {!! strip_tags($hotel['hotelDescription'], '<p><br><strong><em><ul><li>') !!}
                            </div>
                        </div>
                    @endif
                    @if(!empty($hotel['hotelFacilities'] ?? []))
                        <div>
                            <h2 class="text-lg font-semibold text-stone-900 mb-3">{{ __('Facilities') }}</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($hotel['hotelFacilities'], 0, 12) as $facility)
                                    <span class="inline-block px-2.5 py-1 bg-stone-100 text-stone-600 rounded-lg text-xs">{{ $facility }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Room Offers --}}
        <div class="py-8">
            <h2 class="text-2xl font-bold text-stone-900 mb-6" style="font-family: 'Playfair Display', serif;">{{ __('Choose your room') }}</h2>

            @if(count($groupedRooms) === 0)
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-10 text-center">
                    <div class="w-16 h-16 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-stone-800 mb-1">{{ __('No rooms available') }}</h3>
                    <p class="text-stone-500 text-sm">{{ __('Try different dates for this hotel.') }}</p>
                </div>
            @endif

            <div class="space-y-6">
                @foreach($groupedRooms as $roomId => $room)
                    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
                        <div class="lg:flex">
                            {{-- Room Image --}}
                            <div class="lg:w-72 h-52 lg:h-auto bg-stone-100 flex-shrink-0 overflow-hidden relative">
                                @if($room['image'])
                                    <img src="{{ $room['image'] }}" alt="{{ $room['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-stone-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"></path></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Room Info & Offers --}}
                            <div class="flex-1 p-5 lg:p-6">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-stone-900">{{ $room['name'] }}</h3>
                                        <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-stone-500">
                                            @if($room['size'])
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                    {{ $room['size'] }}
                                                </span>
                                            @endif
                                            @if($room['maxOccupancy'])
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    {{ __('Max :count guests', ['count' => $room['maxOccupancy']]) }}
                                                </span>
                                            @endif
                                            @foreach($room['bedTypes'] as $bed)
                                                <span class="inline-flex items-center gap-1">{{ $bed['quantity'] ?? 1 }}x {{ $bed['bedType'] ?? 'Bed' }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($room['amenities']))
                                    <div class="flex flex-wrap gap-1.5 mb-4">
                                        @foreach($room['amenities'] as $amenity)
                                            <span class="inline-block px-2 py-0.5 bg-stone-50 text-stone-500 rounded text-xs">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Offers --}}
                                <div class="space-y-2.5">
                                    @foreach($room['offers'] as $offer)
                                        <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-stone-50 rounded-xl border border-stone-100 hover:border-amber-200 transition-colors">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-medium text-stone-700">{{ $offer['boardName'] }}</span>

                                                @if($offer['refundable'] === 'RFN')
                                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                        {{ __('Free cancellation') }}
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 text-xs font-medium bg-stone-100 text-stone-500 rounded-full">{{ __('Non-refundable') }}</span>
                                                @endif

                                                @if($offer['taxesIncluded'])
                                                    <span class="inline-block px-2 py-0.5 text-xs font-medium bg-sky-50 text-sky-600 rounded-full">{{ __('Taxes incl.') }}</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-4">
                                                @if($offer['price'])
                                                    <div class="text-right">
                                                        <div class="text-lg font-bold text-stone-900">{{ $offer['currency'] }} {{ number_format((float)$offer['price'], 2) }}</div>
                                                        <div class="text-xs text-stone-400">{{ __('total stay') }}</div>
                                                    </div>
                                                @endif

                                                <form action="{{ route('prebook') }}" method="POST" class="prebook-form">
                                                    @csrf
                                                    <input type="hidden" name="offer_id" value="{{ $offer['offerId'] }}">
                                                    <input type="hidden" name="hotel_id" value="{{ $hotelId }}">
                                                    <input type="hidden" name="checkin" value="{{ $checkin }}">
                                                    <input type="hidden" name="checkout" value="{{ $checkout }}">
                                                    <input type="hidden" name="adults" value="{{ $adults }}">
                                                    <button type="submit"
                                                        class="bg-stone-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-stone-800 active:scale-95 transition-all prebook-btn whitespace-nowrap">
                                                        <span class="btn-text">{{ __('Select') }}</span>
                                                        <span class="btn-loading hidden">
                                                            <svg class="animate-spin inline h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.prebook-form').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('.prebook-btn');
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('hidden');
        btn.querySelector('.btn-loading').classList.remove('hidden');
    });
});
</script>
@endpush

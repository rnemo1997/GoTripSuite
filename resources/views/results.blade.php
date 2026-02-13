@extends('layouts.app')

@section('title', __('Hotels in :query', ['query' => $query]) . ' - GoTripSuite')

@section('content')
<div class="pt-24 pb-16">
    {{-- Header --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-700 text-sm font-medium mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            {{ __('New Search') }}
        </a>
        <h1 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">
            @if($mode === 'vibe')
                {{ __('Hotels matching ":query"', ['query' => $query]) }}
            @else
                {{ __('Hotels in :query', ['query' => $query]) }}
            @endif
        </h1>
        <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-stone-500">
            <span class="inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ \Carbon\Carbon::parse($checkin)->format('M d') }} - {{ \Carbon\Carbon::parse($checkout)->format('M d, Y') }}
            </span>
            <span class="text-stone-300">&middot;</span>
            <span class="inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                {{ $adults }} {{ $adults === 1 ? __('guest') : __('guests') }}
            </span>
            <span class="text-stone-300">&middot;</span>
            <span class="font-medium text-stone-700">{{ count($hotels) }} {{ count($hotels) === 1 ? __('hotel found') : __('hotels found') }}</span>
        </div>
    </div>

    {{-- Results Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(count($hotels) === 0)
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h2 class="text-xl font-semibold text-stone-800 mb-2" style="font-family: 'Playfair Display', serif;">{{ __('No hotels found') }}</h2>
                <p class="text-stone-500 mb-8 max-w-md mx-auto">{{ __('We couldn\'t find any available hotels. Try adjusting your dates, destination, or search terms.') }}</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-stone-900 text-white py-3 px-8 rounded-xl font-semibold hover:bg-stone-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    {{ __('Search Again') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($hotels as $hotel)
                    <a href="{{ route('hotel.show', $hotel['hotelId']) }}?checkin={{ $checkin }}&checkout={{ $checkout }}&adults={{ $adults }}"
                       class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        {{-- Photo --}}
                        <div class="relative h-52 bg-stone-100 overflow-hidden">
                            @if($hotel['photo'])
                                <img src="{{ $hotel['photo'] }}" alt="{{ $hotel['name'] }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            @endif

                            {{-- Star rating badge --}}
                            @if($hotel['starRating'])
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 flex items-center gap-0.5">
                                    @for($i = 0; $i < (int)$hotel['starRating']; $i++)
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                </div>
                            @endif

                            {{-- Rating badge --}}
                            @if($hotel['rating'])
                                <div class="absolute top-3 right-3 bg-stone-900/80 backdrop-blur-sm text-white rounded-lg px-2.5 py-1 text-sm font-semibold">
                                    {{ number_format((float)$hotel['rating'], 1) }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-5">
                            <h3 class="font-semibold text-stone-900 text-lg leading-snug mb-1.5 group-hover:text-amber-700 transition-colors">{{ $hotel['name'] }}</h3>

                            @if($hotel['address'])
                                <p class="text-sm text-stone-400 mb-4 line-clamp-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $hotel['address'] }}
                                </p>
                            @endif

                            <div class="flex items-end justify-between">
                                @if($hotel['price'])
                                    <div>
                                        <span class="text-xs text-stone-400 uppercase tracking-wider">{{ __('From') }}</span>
                                        <div class="text-xl font-bold text-stone-900">{{ $hotel['currency'] }} {{ number_format((float)$hotel['price'], 0) }}</div>
                                    </div>
                                @endif
                                <span class="text-amber-600 text-sm font-medium group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                    {{ __('View rooms') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

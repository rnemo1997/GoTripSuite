@extends('layouts.app')

@section('title', __('Booking Confirmation') . ' - GoTripSuite')

@section('content')
<div class="pt-24 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        @if($prebook->status === 'booked')
            {{-- Success --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-50 rounded-full mb-5">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Booking Confirmed!') }}</h1>
                <p class="text-stone-500 mt-2">{{ __('Your adventure is booked. We\'ve sent the details to your email.') }}</p>
            </div>
        @else
            {{-- Failed --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-50 rounded-full mb-5">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-stone-900" style="font-family: 'Playfair Display', serif;">{{ __('Booking Failed') }}</h1>
                <p class="text-stone-500 mt-2">{{ __('Something went wrong. Please try again or contact support.') }}</p>

                @if(isset($bookResult['error']))
                    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm inline-block">
                        {{ $bookResult['error']['message'] ?? __('Unknown error occurred') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Hotel Card --}}
        @if(!empty($hotel))
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden mb-6">
                @if($hotel['main_photo'] ?? null)
                    <div class="h-44 overflow-hidden">
                        <img src="{{ $hotel['main_photo'] }}" alt="{{ $hotel['name'] ?? '' }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="font-semibold text-stone-900 text-lg">{{ $hotel['name'] ?? 'Hotel' }}</h2>
                            @if($hotel['address'] ?? null)
                                <p class="text-sm text-stone-400 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $hotel['address'] }}
                                </p>
                            @endif
                        </div>
                        @if($hotel['starRating'] ?? null)
                            <div class="flex items-center gap-0.5">
                                @for($i = 0; $i < (int)$hotel['starRating']; $i++)
                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                        @endif
                    </div>
                    @if($hotel['phone'] ?? null)
                        <p class="text-xs text-stone-400 mt-2">{{ __('Phone:') }} {{ $hotel['phone'] }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Booking Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <h2 class="font-semibold text-stone-900 text-lg mb-5">{{ __('Reservation Details') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                @if($prebook->booking_id)
                    <div class="sm:col-span-2 bg-stone-50 rounded-xl p-4">
                        <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Booking ID') }}</span>
                        <div class="font-mono font-semibold text-stone-900 text-lg mt-1">{{ $prebook->booking_id }}</div>
                    </div>
                @endif

                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Status') }}</span>
                    <div class="mt-1">
                        @if($prebook->status === 'booked')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                {{ __('CONFIRMED') }}
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold border border-red-200 uppercase">{{ $prebook->status }}</span>
                        @endif
                    </div>
                </div>

                @if($bookResult['hotelConfirmationCode'] ?? null)
                    <div>
                        <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Hotel Confirmation') }}</span>
                        <div class="font-mono font-semibold text-stone-900 mt-1">{{ $bookResult['hotelConfirmationCode'] }}</div>
                    </div>
                @endif

                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Check-in') }}</span>
                    <div class="font-semibold text-stone-900 mt-1">{{ $prebook->checkin->format('l, M d, Y') }}</div>
                </div>

                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Check-out') }}</span>
                    <div class="font-semibold text-stone-900 mt-1">{{ $prebook->checkout->format('l, M d, Y') }}</div>
                </div>

                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Guest') }}</span>
                    <div class="font-semibold text-stone-900 mt-1">{{ $prebook->holder_first_name }} {{ $prebook->holder_last_name }}</div>
                </div>

                <div>
                    <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Email') }}</span>
                    <div class="font-semibold text-stone-900 mt-1">{{ $prebook->holder_email }}</div>
                </div>

                @if($prebook->pricing)
                    @php
                        $total = $prebook->pricing['totalAmount'] ?? $prebook->pricing['price'] ?? null;
                        $currency = $prebook->pricing['currency'] ?? 'EUR';
                    @endphp
                    @if($total)
                        <div class="sm:col-span-2 pt-4 border-t border-stone-100">
                            <span class="text-stone-400 text-xs uppercase tracking-wider">{{ __('Total Paid') }}</span>
                            <div class="font-bold text-stone-900 text-2xl mt-1">{{ $currency }} {{ number_format((float)$total, 2) }}</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Cancellation Policy --}}
        @if(!empty($bookResult['cancellationPolicies'] ?? $bookResult['cancellation'] ?? null))
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
                <h2 class="font-semibold text-stone-900 mb-3">{{ __('Cancellation Policy') }}</h2>
                @php
                    $policies = $bookResult['cancellationPolicies'] ?? $bookResult['cancellation'] ?? [];
                    if (!is_array($policies)) $policies = [];
                @endphp
                @foreach($policies as $policy)
                    <div class="text-sm text-stone-600 mb-2">
                        @if(is_array($policy))
                            <p>{{ $policy['description'] ?? json_encode($policy) }}</p>
                        @else
                            <p>{{ $policy }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center pt-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-stone-900 text-white py-3.5 px-10 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                {{ __('Plan Another Trip') }}
            </a>
        </div>
    </div>
</div>
@endsection

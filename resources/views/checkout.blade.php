@extends('layouts.app')

@section('title', __('Checkout') . ' - GoTripSuite')

@section('content')
<div class="pt-24 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        {{-- Progress indicator --}}
        <div class="flex items-center justify-center gap-2 mb-10 text-sm">
            <span class="flex items-center gap-1.5 text-stone-400">
                <span class="w-6 h-6 rounded-full bg-stone-200 flex items-center justify-center text-xs text-stone-500">1</span>
                {{ __('Search') }}
            </span>
            <span class="w-8 h-px bg-stone-300"></span>
            <span class="flex items-center gap-1.5 text-stone-400">
                <span class="w-6 h-6 rounded-full bg-stone-200 flex items-center justify-center text-xs text-stone-500">2</span>
                {{ __('Select') }}
            </span>
            <span class="w-8 h-px bg-stone-300"></span>
            <span class="flex items-center gap-1.5 text-stone-900 font-semibold">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white flex items-center justify-center text-xs">3</span>
                {{ __('Checkout') }}
            </span>
        </div>

        <h1 class="text-3xl font-bold text-stone-900 mb-8 text-center" style="font-family: 'Playfair Display', serif;">{{ __('Complete Your Booking') }}</h1>

        {{-- Booking Summary --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <div class="flex items-start gap-4">
                @if($hotel['main_photo'] ?? $hotel['thumbnail'] ?? null)
                    <img src="{{ $hotel['thumbnail'] ?? $hotel['main_photo'] }}" alt="{{ $hotel['name'] ?? '' }}"
                         class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                @endif
                <div class="flex-1">
                    <h2 class="font-semibold text-stone-900">{{ $hotel['name'] ?? 'Hotel' }}</h2>
                    <div class="mt-2 grid grid-cols-2 gap-x-6 gap-y-1 text-sm text-stone-600">
                        <div>
                            <span class="text-stone-400">{{ __('Check-in:') }}</span>
                            {{ $prebook->checkin->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="text-stone-400">{{ __('Check-out:') }}</span>
                            {{ $prebook->checkout->format('M d, Y') }}
                        </div>
                        <div>
                            <span class="text-stone-400">{{ __('Guests:') }}</span>
                            {{ $prebook->guests_adults }} {{ $prebook->guests_adults === 1 ? __('adult') : __('adults') }}
                        </div>
                    </div>
                    @if($prebook->pricing)
                        @php
                            $total = $prebook->pricing['totalAmount'] ?? $prebook->pricing['price'] ?? null;
                            $currency = $prebook->pricing['currency'] ?? 'EUR';
                        @endphp
                        @if($total)
                            <div class="mt-3 pt-3 border-t border-stone-100 flex items-baseline gap-2">
                                <span class="text-sm text-stone-400">{{ __('Total:') }}</span>
                                <span class="text-xl font-bold text-stone-900">{{ $currency }} {{ number_format((float)$total, 2) }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Guest Details Form --}}
        <div id="guestFormSection" class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <h2 class="font-semibold text-stone-900 mb-1">{{ __('Guest Details') }}</h2>
            <p class="text-sm text-stone-400 mb-5">{{ __('Who is staying at the hotel?') }}</p>
            <form id="guestForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" id="firstName" required
                            value="{{ $prebook->holder_first_name }}"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" id="lastName" required
                            value="{{ $prebook->holder_last_name }}"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" required
                        value="{{ $prebook->holder_email }}"
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                </div>
                <button type="submit" id="saveGuestBtn"
                    class="w-full bg-stone-900 text-white py-3.5 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all disabled:opacity-50">
                    {{ __('Continue to Payment') }}
                </button>
            </form>
        </div>

        {{-- Payment Section --}}
        <div id="paymentSection" class="hidden">
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
                <h2 class="font-semibold text-stone-900 mb-1">{{ __('Payment') }}</h2>
                <p class="text-sm text-stone-400 mb-5">{{ __('Secure payment processing') }}</p>

                @if($liteapiEnv === 'sandbox')
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl mb-5 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div>
                            <strong>{{ __('Sandbox Mode') }}</strong><br>
                            Card: <code class="bg-amber-100 px-1.5 py-0.5 rounded text-xs">4242 4242 4242 4242</code> &middot; CVV: any 3 digits &middot; Expiry: future date
                        </div>
                    </div>
                @endif

                <div id="paymentContainer" class="min-h-[200px] flex items-center justify-center">
                    <div class="text-center">
                        <svg class="animate-spin h-8 w-8 text-stone-400 mx-auto mb-3" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-sm text-stone-400">{{ __('Loading payment form...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head')
<script src="https://payment-wrapper.liteapi.travel/dist/liteAPIPayment.js?v=a1"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const localePrefix = @json(app()->getLocale() === 'en' ? '' : '/' . app()->getLocale());
    const guestForm = document.getElementById('guestForm');
    const guestFormSection = document.getElementById('guestFormSection');
    const paymentSection = document.getElementById('paymentSection');
    const prebookId = @json($prebook->prebook_id);
    const secretKey = @json($prebook->secret_key);
    const liteapiEnv = @json($liteapiEnv);
    const appUrl = @json($appUrl);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const confirmUrl = @json(route('booking.confirm'));

    guestForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('saveGuestBtn');
        btn.disabled = true;
        btn.textContent = '{{ __('Saving...') }}';

        try {
            const response = await fetch(`${localePrefix}/checkout/${prebookId}/guest`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    first_name: document.getElementById('firstName').value,
                    last_name: document.getElementById('lastName').value,
                    email: document.getElementById('email').value,
                }),
            });

            const data = await response.json();

            if (data.success) {
                guestFormSection.classList.add('hidden');
                paymentSection.classList.remove('hidden');
                initPayment();
            } else {
                alert('{{ __('Failed to save guest details. Please try again.') }}');
                btn.disabled = false;
                btn.textContent = '{{ __('Continue to Payment') }}';
            }
        } catch (err) {
            alert('{{ __('An error occurred. Please try again.') }}');
            btn.disabled = false;
            btn.textContent = '{{ __('Continue to Payment') }}';
        }
    });

    function initPayment() {
        const publicKey = liteapiEnv === 'sandbox' ? 'sandbox' : liteapiEnv;
        const returnUrl = `${confirmUrl}?prebookId=${prebookId}&transactionId=${prebookId}`;

        if (typeof LiteAPIPayment !== 'undefined') {
            LiteAPIPayment.init({
                container: '#paymentContainer',
                publicKey: publicKey,
                secretKey: secretKey,
                returnUrl: returnUrl,
            });
        } else {
            document.getElementById('paymentContainer').innerHTML =
                '<p class="text-red-600 text-center">{{ __('Payment SDK failed to load. Please refresh the page.') }}</p>';
        }
    }
});
</script>
@endpush

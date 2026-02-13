@extends('layouts.app')

@section('title', __('Checkout') . ' - GoTripSuite')

@section('content')
<div class="pt-24 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        {{-- Progress indicator --}}
        <div class="flex items-center justify-center gap-2 mb-10 text-sm">
            <span class="flex items-center gap-1.5 text-stone-400">
                <span class="w-6 h-6 rounded-full bg-stone-200 flex items-center justify-center text-xs text-stone-500">1</span>
                {{ __('Plan') }}
            </span>
            <span class="w-8 h-px bg-stone-300"></span>
            <span class="flex items-center gap-1.5 text-stone-400">
                <span class="w-6 h-6 rounded-full bg-stone-200 flex items-center justify-center text-xs text-stone-500">2</span>
                {{ __('Hotels') }}
            </span>
            <span class="w-8 h-px bg-stone-300"></span>
            <span class="flex items-center gap-1.5 text-stone-900 font-semibold">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white flex items-center justify-center text-xs">3</span>
                {{ __('Checkout') }}
            </span>
        </div>

        <h1 class="text-3xl font-bold text-stone-900 mb-2 text-center" style="font-family: 'Playfair Display', serif;">{{ __('Complete Your Trip Booking') }}</h1>
        <p class="text-stone-500 text-center mb-8">{{ $trip->name }} &middot; {{ $trip->stops->count() }} {{ __('stops') }}</p>

        {{-- Trip Summary --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <h2 class="font-semibold text-stone-900 mb-4">{{ __('Your Trip') }}</h2>
            <div class="space-y-3">
                @foreach($trip->stops as $i => $stop)
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $i + 1 }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-medium text-stone-900 text-sm truncate">{{ $stop->place_name }}</p>
                                    <p class="text-xs text-stone-400">{{ $stop->hotel_name }} &middot; {{ $stop->checkin->format('M d') }} — {{ $stop->checkout->format('M d') }}</p>
                                </div>
                                <span class="text-sm font-semibold text-stone-900 flex-shrink-0">{{ $stop->hotel_currency }} {{ number_format($stop->hotel_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-stone-100 flex items-baseline justify-between">
                <span class="text-sm text-stone-400">{{ __('Total') }}</span>
                <span class="text-xl font-bold text-stone-900">{{ $trip->currency }} {{ number_format($trip->total_price, 2) }}</span>
            </div>
        </div>

        {{-- Guest Details Form --}}
        <div id="guestFormSection" class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
            <h2 class="font-semibold text-stone-900 mb-1">{{ __('Guest Details') }}</h2>
            <p class="text-sm text-stone-400 mb-5">{{ __('One set of details for all :count hotel bookings.', ['count' => $trip->stops->count()]) }}</p>
            <form id="guestForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" id="firstName" required
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" id="lastName" required
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-stone-800 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1.5 uppercase tracking-wider">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" required
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
            @foreach($trip->stops as $i => $stop)
                @if($stop->prebook_id)
                    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6" id="paymentStop{{ $stop->id }}">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $i + 1 }}</div>
                            <div>
                                <h2 class="font-semibold text-stone-900">{{ __('Payment for :place', ['place' => $stop->place_name]) }}</h2>
                                <p class="text-xs text-stone-400">{{ $stop->hotel_name }}</p>
                            </div>
                        </div>

                        @if($liteapiEnv === 'sandbox')
                            <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl mb-4 text-sm flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <div>
                                    <strong>{{ __('Sandbox Mode') }}</strong> &middot;
                                    Card: <code class="bg-amber-100 px-1.5 py-0.5 rounded text-xs">4242 4242 4242 4242</code>
                                </div>
                            </div>
                        @endif

                        <div class="payment-container min-h-[200px] flex items-center justify-center"
                             id="paymentContainer{{ $stop->id }}"
                             data-prebook-id="{{ $stop->prebook_id }}"
                             data-secret-key="{{ $stop->secret_key }}">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 text-stone-400 mx-auto mb-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <p class="text-sm text-stone-400">{{ __('Loading payment form...') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Sandbox: skip payment and confirm directly --}}
            @if($liteapiEnv === 'sandbox')
                <div class="text-center mt-4">
                    <p class="text-sm text-stone-400 mb-3">{{ __('In sandbox mode, you can skip payment:') }}</p>
                    <a href="{{ route('trip-builder.confirm', $trip) }}"
                       class="inline-flex items-center gap-2 bg-stone-900 text-white py-3.5 px-10 rounded-xl font-semibold hover:bg-stone-800 active:scale-[0.98] transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Confirm All Bookings') }}
                    </a>
                </div>
            @endif
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
    const tripId = @json($trip->id);
    const liteapiEnv = @json($liteapiEnv);
    const appUrl = @json($appUrl);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const guestForm = document.getElementById('guestForm');
    const guestFormSection = document.getElementById('guestFormSection');
    const paymentSection = document.getElementById('paymentSection');

    guestForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('saveGuestBtn');
        btn.disabled = true;
        btn.textContent = '{{ __('Saving...') }}';

        try {
            const response = await fetch(`${localePrefix}/trip-builder/${tripId}/guest`, {
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
                initPayments();
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

    function initPayments() {
        const containers = document.querySelectorAll('.payment-container');
        const confirmUrl = @json(route('trip-builder.confirm', $trip));
        containers.forEach(container => {
            const prebookId = container.dataset.prebookId;
            const secretKey = container.dataset.secretKey;

            if (!prebookId || !secretKey) return;

            const publicKey = liteapiEnv === 'sandbox' ? 'sandbox' : liteapiEnv;
            const returnUrl = `${confirmUrl}?prebookId=${prebookId}&transactionId=${prebookId}`;

            if (typeof LiteAPIPayment !== 'undefined') {
                LiteAPIPayment.init({
                    container: `#${container.id}`,
                    publicKey: publicKey,
                    secretKey: secretKey,
                    returnUrl: returnUrl,
                });
            }
        });
    }
});
</script>
@endpush

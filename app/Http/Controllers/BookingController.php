<?php

namespace App\Http\Controllers;

use App\Models\Prebook;
use App\Services\LiteApiClient;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected LiteApiClient $api) {}

    public function prebook(Request $request)
    {
        $request->validate([
            'offer_id' => 'required|string',
            'hotel_id' => 'required|string',
            'checkin' => 'required|date',
            'checkout' => 'required|date',
            'adults' => 'required|integer|min:1',
        ]);

        $result = $this->api->prebook($request->offer_id);

        if (!isset($result['data']['prebookId'])) {
            return back()->with('error', $result['error']['message'] ?? __('Prebook failed. Please choose another rate.'));
        }

        $prebook = Prebook::create([
            'prebook_id' => $result['data']['prebookId'],
            'transaction_id' => $result['data']['transactionId'] ?? null,
            'secret_key' => $result['data']['secretKey'] ?? null,
            'hotel_id' => $request->hotel_id,
            'offer_id' => $request->offer_id,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'guests_adults' => $request->adults,
            'pricing' => $result['data'] ?? null,
            'status' => 'initiated',
        ]);

        return redirect()->route('checkout', $prebook->prebook_id);
    }

    public function checkout(string $prebookId)
    {
        $prebook = Prebook::where('prebook_id', $prebookId)->firstOrFail();

        $hotel = $this->api->getHotel($prebook->hotel_id);

        return view('checkout', [
            'prebook' => $prebook,
            'hotel' => $hotel['data'] ?? [],
            'liteapiEnv' => config('services.liteapi.env'),
            'appUrl' => config('app.url'),
        ]);
    }

    public function saveGuest(Request $request, string $prebookId)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
        ]);

        $prebook = Prebook::where('prebook_id', $prebookId)->firstOrFail();

        $prebook->update([
            'holder_first_name' => $request->first_name,
            'holder_last_name' => $request->last_name,
            'holder_email' => $request->email,
        ]);

        return response()->json(['success' => true]);
    }

    public function confirm(Request $request)
    {
        $prebookId = $request->query('prebookId');
        $transactionId = $request->query('transactionId');

        if (!$prebookId) {
            return redirect()->route('home')->with('error', __('Invalid booking request.'));
        }

        $prebook = Prebook::where('prebook_id', $prebookId)->firstOrFail();

        // Update transaction ID if provided by payment redirect
        if ($transactionId) {
            $prebook->update(['transaction_id' => $transactionId]);
        }

        $prebook->update(['status' => 'paid']);

        // Perform the actual booking
        $holder = [
            'firstName' => $prebook->holder_first_name,
            'lastName' => $prebook->holder_last_name,
            'email' => $prebook->holder_email,
        ];

        $guests = [[
            'occupancyNumber' => 1,
            'firstName' => $prebook->holder_first_name,
            'lastName' => $prebook->holder_last_name,
            'email' => $prebook->holder_email,
        ]];

        $bookResult = $this->api->book(
            $prebook->prebook_id,
            $prebook->transaction_id,
            $holder,
            $guests
        );

        if (isset($bookResult['data']['bookingId'])) {
            $prebook->update([
                'status' => 'booked',
                'booking_id' => $bookResult['data']['bookingId'],
            ]);
        } else {
            $prebook->update(['status' => 'failed']);
        }

        // Get hotel details for confirmation page
        $hotel = $this->api->getHotel($prebook->hotel_id);

        return view('confirmation', [
            'prebook' => $prebook->fresh(),
            'bookResult' => $bookResult['data'] ?? $bookResult,
            'hotel' => $hotel['data'] ?? [],
        ]);
    }
}

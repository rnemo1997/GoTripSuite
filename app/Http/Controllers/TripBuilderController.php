<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripStop;
use App\Services\LiteApiClient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TripBuilderController extends Controller
{
    public function __construct(protected LiteApiClient $api) {}

    public function index()
    {
        // Resume existing planning trip if any
        $trip = Trip::where('session_id', session()->getId())
            ->where('status', 'planning')
            ->latest()
            ->first();

        return view('home', compact('trip'));
    }

    public function createFromTemplate(Request $request, string $slug)
    {
        $templates = [
            'italian-classics' => [
                'name' => 'Italian Classics',
                'stops' => [
                    ['place_name' => 'Rome', 'place_id' => 'ChIJ16eBGMSJJRMRGkFpq9bMkyk', 'latitude' => 41.9028, 'longitude' => 12.4964, 'nights' => 3],
                    ['place_name' => 'Florence', 'place_id' => 'ChIJrdbSgKZWKhMRAyrH7xEB3Ek', 'latitude' => 43.7696, 'longitude' => 11.2558, 'nights' => 2],
                    ['place_name' => 'Venice', 'place_id' => 'ChIJiT3W8dqxfkcRoGQuXEBFQSE', 'latitude' => 45.4408, 'longitude' => 12.3155, 'nights' => 2],
                    ['place_name' => 'Milan', 'place_id' => 'ChIJ53USP0nBhkcRjQ50xhPN_zw', 'latitude' => 45.4642, 'longitude' => 9.1900, 'nights' => 3],
                ],
            ],
            'thai-explorer' => [
                'name' => 'Thai Explorer',
                'stops' => [
                    ['place_name' => 'Chiang Mai', 'place_id' => 'ChIJAUPCGkintR4RslIeASHLBFI', 'latitude' => 18.7883, 'longitude' => 98.9853, 'nights' => 3],
                    ['place_name' => 'Bangkok', 'place_id' => 'ChIJ82ENKDJgHTERIEjiXbIAAQE', 'latitude' => 13.7563, 'longitude' => 100.5018, 'nights' => 3],
                    ['place_name' => 'Phuket', 'place_id' => 'ChIJdZkz4E1RUDARq0NCsVMOR9I', 'latitude' => 7.8804, 'longitude' => 98.3923, 'nights' => 3],
                ],
            ],
            'spanish-coast' => [
                'name' => 'Spanish Coast',
                'stops' => [
                    ['place_name' => 'Barcelona', 'place_id' => 'ChIJ5TCOcRaYpBIRCmZHTz37sEQ', 'latitude' => 41.3874, 'longitude' => 2.1686, 'nights' => 3],
                    ['place_name' => 'Valencia', 'place_id' => 'ChIJb7Dv8ExPYA0ROR1_HwFRo7Q', 'latitude' => 39.4699, 'longitude' => -0.3763, 'nights' => 2],
                    ['place_name' => 'Seville', 'place_id' => 'ChIJkWK-FBFsEg0R_UACwyT0kPo', 'latitude' => 37.3891, 'longitude' => -5.9845, 'nights' => 3],
                ],
            ],
        ];

        if (!isset($templates[$slug])) {
            return redirect()->route('home');
        }

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $template = $templates[$slug];
        $startDate = Carbon::parse($request->start_date);

        $trip = Trip::create([
            'session_id' => session()->getId(),
            'name' => $template['name'],
            'adults' => 2,
            'status' => 'planning',
            'currency' => config('services.liteapi.currency', 'USD'),
        ]);

        session(['trip_start_date' => $startDate->format('Y-m-d')]);

        $currentDate = $startDate->copy();
        foreach ($template['stops'] as $index => $stopData) {
            TripStop::create([
                'trip_id' => $trip->id,
                'sort_order' => $index,
                'place_id' => $stopData['place_id'],
                'place_name' => $stopData['place_name'],
                'latitude' => $stopData['latitude'],
                'longitude' => $stopData['longitude'],
                'checkin' => $currentDate->copy(),
                'checkout' => $currentDate->copy()->addDays($stopData['nights']),
                'nights' => $stopData['nights'],
            ]);
            $currentDate->addDays($stopData['nights']);
        }

        return redirect()->route('trip-builder.show', ['trip' => $trip]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'adults' => 'required|integer|min:1|max:9',
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $trip = Trip::create([
            'session_id' => session()->getId(),
            'name' => $request->name,
            'adults' => $request->adults,
            'status' => 'planning',
            'currency' => config('services.liteapi.currency', 'USD'),
        ]);

        session(['trip_start_date' => $request->start_date]);

        return redirect()->route('trip-builder.show', ['trip' => $trip]);
    }

    public function show(Trip $trip)
    {
        $trip->load('stops');
        $liteapiEnv = config('services.liteapi.env');
        $startDate = session('trip_start_date', $trip->stops->first()?->checkin?->format('Y-m-d') ?? now()->addDay()->format('Y-m-d'));

        return view('trip-builder.show', compact('trip', 'liteapiEnv', 'startDate'));
    }

    public function updateStartDate(Request $request, Trip $trip)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        session(['trip_start_date' => $request->start_date]);

        // Recalculate all stop dates from the new start date
        $currentDate = Carbon::parse($request->start_date);
        foreach ($trip->stops()->orderBy('sort_order')->get() as $stop) {
            $stop->update([
                'checkin' => $currentDate->copy(),
                'checkout' => $currentDate->copy()->addDays($stop->nights),
            ]);
            $currentDate->addDays($stop->nights);
        }

        $trip->load('stops');

        return response()->json([
            'success' => true,
            'stops' => $trip->stops,
            'start_date' => $request->start_date,
        ]);
    }

    public function addStop(Request $request, Trip $trip)
    {
        $request->validate([
            'place_id' => 'required|string',
            'place_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'nights' => 'nullable|integer|min:1|max:30',
        ]);

        $lastStop = $trip->stops()->orderByDesc('sort_order')->first();
        $sortOrder = $lastStop ? $lastStop->sort_order + 1 : 0;

        // Calculate checkin based on previous stop's checkout or trip start date
        if ($lastStop && $lastStop->checkout) {
            $checkin = $lastStop->checkout;
        } else {
            $checkin = Carbon::parse(session('trip_start_date', now()->addDay()->format('Y-m-d')));
        }

        $nights = $request->nights ?? 1;

        $stop = TripStop::create([
            'trip_id' => $trip->id,
            'sort_order' => $sortOrder,
            'place_id' => $request->place_id,
            'place_name' => $request->place_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'checkin' => $checkin,
            'checkout' => $checkin->copy()->addDays($nights),
            'nights' => $nights,
        ]);

        $trip->load('stops');

        return response()->json([
            'success' => true,
            'stop' => $stop,
            'stops' => $trip->stops,
        ]);
    }

    public function updateStop(Request $request, Trip $trip, TripStop $stop)
    {
        $request->validate([
            'nights' => 'required|integer|min:1|max:30',
        ]);

        $stop->update(['nights' => $request->nights]);
        $trip->recalculateDates();
        $trip->recalculateTotal();
        $trip->load('stops');

        return response()->json([
            'success' => true,
            'stops' => $trip->stops,
        ]);
    }

    public function removeStop(Request $request, Trip $trip, TripStop $stop)
    {
        $stop->delete();

        // Re-number sort_order
        $trip->stops()->orderBy('sort_order')->get()->each(function ($s, $i) {
            $s->update(['sort_order' => $i]);
        });

        $trip->recalculateDates();
        $trip->recalculateTotal();
        $trip->load('stops');

        return response()->json([
            'success' => true,
            'stops' => $trip->stops,
        ]);
    }

    public function reorderStops(Request $request, Trip $trip)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:trip_stops,id',
        ]);

        foreach ($request->order as $index => $stopId) {
            TripStop::where('id', $stopId)->where('trip_id', $trip->id)->update(['sort_order' => $index]);
        }

        $trip->recalculateDates();
        $trip->recalculateTotal();
        $trip->load('stops');

        return response()->json([
            'success' => true,
            'stops' => $trip->stops,
        ]);
    }

    public function searchHotels(Request $request, Trip $trip, TripStop $stop)
    {
        if (!$stop->checkin || !$stop->checkout) {
            return response()->json(['hotels' => [], 'error' => 'Stop dates not set']);
        }

        if ($stop->latitude && $stop->longitude) {
            $result = $this->api->searchRatesByLocation(
                $stop->latitude,
                $stop->longitude,
                $stop->checkin->format('Y-m-d'),
                $stop->checkout->format('Y-m-d'),
                $trip->adults
            );
        } else {
            $result = $this->api->searchRatesByPlace(
                $stop->place_id,
                $stop->checkin->format('Y-m-d'),
                $stop->checkout->format('Y-m-d'),
                $trip->adults
            );
        }

        // Normalize like SearchController does
        $hotelInfoMap = [];
        foreach (($result['hotels'] ?? []) as $h) {
            $hotelInfoMap[$h['id'] ?? ''] = $h;
        }

        $hotels = [];
        foreach (($result['data'] ?? []) as $item) {
            $hId = $item['hotelId'] ?? '';
            $info = $hotelInfoMap[$hId] ?? [];

            $lowestPrice = null;
            $lowestOfferId = null;
            $currency = 'EUR';

            foreach (($item['roomTypes'] ?? []) as $roomType) {
                foreach (($roomType['offers'] ?? []) as $offer) {
                    $price = $offer['retailRate']['total'][0]['amount'] ?? $roomType['offerRetailRate']['amount'] ?? null;
                    $cur = $offer['retailRate']['total'][0]['currency'] ?? $roomType['offerRetailRate']['currency'] ?? 'EUR';
                    $offerId = $offer['offerId'] ?? $roomType['offerId'] ?? null;

                    if ($price !== null && ($lowestPrice === null || $price < $lowestPrice)) {
                        $lowestPrice = $price;
                        $currency = $cur;
                        $lowestOfferId = $offerId;
                    }
                }

                // Fallback to offerRetailRate at roomType level
                if ($lowestPrice === null) {
                    $price = $roomType['offerRetailRate']['amount'] ?? null;
                    if ($price !== null) {
                        $lowestPrice = $price;
                        $currency = $roomType['offerRetailRate']['currency'] ?? 'EUR';
                        $lowestOfferId = $roomType['offerId'] ?? null;
                    }
                }
            }

            $hotels[] = [
                'hotelId' => $hId,
                'name' => $info['name'] ?? $item['name'] ?? 'Hotel',
                'photo' => $info['main_photo'] ?? $info['thumbnail'] ?? $item['main_photo'] ?? null,
                'address' => $info['address'] ?? $item['address'] ?? '',
                'rating' => $info['rating'] ?? $item['rating'] ?? null,
                'starRating' => $info['starRating'] ?? $item['starRating'] ?? null,
                'price' => $lowestPrice,
                'currency' => $currency,
                'offerId' => $lowestOfferId,
            ];
        }

        return response()->json(['hotels' => $hotels]);
    }

    public function hotelDetail(Request $request, Trip $trip, TripStop $stop, string $hotelId)
    {
        $hotel = $this->api->getHotel($hotelId);
        $ratesResult = $this->api->searchHotelRates(
            $hotelId,
            $stop->checkin->format('Y-m-d'),
            $stop->checkout->format('Y-m-d'),
            $trip->adults
        );

        $hotelData = $hotel['data'] ?? [];

        // Build room lookup
        $hotelRooms = [];
        foreach (($hotelData['rooms'] ?? []) as $room) {
            $hotelRooms[$room['id']] = $room;
        }

        // Group offers by room (same logic as HotelController)
        $groupedRooms = [];
        $roomTypes = $ratesResult['data'][0]['roomTypes'] ?? [];

        foreach ($roomTypes as $roomType) {
            $rate = $roomType['rates'][0] ?? null;
            if (!$rate) continue;

            $mappedRoomId = $rate['mappedRoomId'] ?? 'unknown';
            $roomInfo = $hotelRooms[$mappedRoomId] ?? null;

            if (!isset($groupedRooms[$mappedRoomId])) {
                $groupedRooms[$mappedRoomId] = [
                    'name' => $roomInfo['roomName'] ?? $rate['name'] ?? 'Room',
                    'image' => $roomInfo['photos'][0]['url'] ?? $hotelData['main_photo'] ?? null,
                    'description' => $roomInfo['description'] ?? null,
                    'size' => isset($roomInfo['roomSizeSquare']) ? $roomInfo['roomSizeSquare'] . ' ' . ($roomInfo['roomSizeUnit'] ?? 'sqm') : null,
                    'maxOccupancy' => $roomInfo['maxOccupancy'] ?? $rate['maxOccupancy'] ?? null,
                    'bedTypes' => $roomInfo['bedTypes'] ?? [],
                    'amenities' => array_slice(array_column($roomInfo['roomAmenities'] ?? [], 'name'), 0, 8),
                    'offers' => [],
                ];
            }

            $taxesAndFees = $rate['retailRate']['taxesAndFees'] ?? [];
            $allTaxesIncluded = true;
            foreach ($taxesAndFees as $tax) {
                if (!($tax['included'] ?? false)) {
                    $allTaxesIncluded = false;
                    break;
                }
            }

            $groupedRooms[$mappedRoomId]['offers'][] = [
                'offerId' => $roomType['offerId'],
                'rateName' => $rate['name'] ?? 'Room',
                'boardType' => $rate['boardType'] ?? 'RO',
                'boardName' => $rate['boardName'] ?? 'Room Only',
                'refundable' => $rate['cancellationPolicies']['refundableTag'] ?? 'NRFN',
                'price' => $rate['retailRate']['total'][0]['amount'] ?? null,
                'currency' => $rate['retailRate']['total'][0]['currency'] ?? 'EUR',
                'taxesIncluded' => $allTaxesIncluded,
            ];
        }

        return response()->json([
            'hotel' => [
                'id' => $hotelId,
                'name' => $hotelData['name'] ?? 'Hotel',
                'photo' => $hotelData['main_photo'] ?? null,
                'images' => array_slice(array_column($hotelData['hotelImages'] ?? [], 'url'), 0, 5),
                'address' => $hotelData['address'] ?? '',
                'city' => $hotelData['city'] ?? '',
                'starRating' => $hotelData['starRating'] ?? null,
                'rating' => $hotelData['rating'] ?? null,
                'reviewCount' => $hotelData['reviewCount'] ?? 0,
                'description' => $hotelData['hotelDescription'] ?? null,
                'facilities' => array_slice($hotelData['hotelFacilities'] ?? [], 0, 15),
                'phone' => $hotelData['phone'] ?? null,
            ],
            'rooms' => array_values($groupedRooms),
        ]);
    }

    public function selectHotel(Request $request, Trip $trip, TripStop $stop)
    {
        $request->validate([
            'hotel_id' => 'required|string',
            'hotel_name' => 'required|string',
            'hotel_photo' => 'nullable|string',
            'offer_id' => 'required|string',
            'hotel_price' => 'required|numeric',
            'hotel_currency' => 'required|string|max:3',
        ]);

        $stop->update($request->only([
            'hotel_id', 'hotel_name', 'hotel_photo', 'offer_id', 'hotel_price', 'hotel_currency',
        ]));

        $trip->recalculateTotal();
        $trip->load('stops');

        return response()->json([
            'success' => true,
            'stop' => $stop->fresh(),
            'trip' => $trip,
        ]);
    }

    public function checkout(Request $request, Trip $trip)
    {
        $trip->load('stops');

        // Verify all stops have hotels
        $stopsWithoutHotel = $trip->stops->filter(fn($s) => !$s->offer_id);
        if ($stopsWithoutHotel->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'error' => __('Please select a hotel for every stop before checkout.'),
            ], 422);
        }

        // Prebook each stop
        $allSuccess = true;
        foreach ($trip->stops as $stop) {
            $result = $this->api->prebook($stop->offer_id);

            if (isset($result['data']['prebookId'])) {
                $stop->update([
                    'prebook_id' => $result['data']['prebookId'],
                    'transaction_id' => $result['data']['transactionId'] ?? null,
                    'secret_key' => $result['data']['secretKey'] ?? null,
                    'booking_status' => 'initiated',
                    'pricing' => $result['data'] ?? null,
                ]);
            } else {
                $allSuccess = false;
                $stop->update(['booking_status' => 'prebook_failed']);
            }
        }

        if (!$allSuccess) {
            return response()->json([
                'success' => false,
                'error' => __('Some hotels could not be prebooked. Please try different hotels for failed stops.'),
            ], 422);
        }

        $trip->update(['status' => 'prebooked']);

        return response()->json([
            'success' => true,
            'redirect' => route('trip-builder.checkout-page', $trip),
        ]);
    }

    public function checkoutPage(Trip $trip)
    {
        $trip->load('stops');
        $liteapiEnv = config('services.liteapi.env');
        $appUrl = config('app.url');

        return view('trip-builder.checkout', compact('trip', 'liteapiEnv', 'appUrl'));
    }

    public function saveGuest(Request $request, Trip $trip)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
        ]);

        // Store guest info in session for the confirm step
        session([
            'trip_guest' => [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ],
        ]);

        return response()->json(['success' => true]);
    }

    public function confirm(Request $request, Trip $trip)
    {
        $trip->load('stops');
        $guest = session('trip_guest', []);

        if (empty($guest)) {
            return redirect()->route('trip-builder.checkout-page', $trip)
                ->with('error', __('Please fill in guest details first.'));
        }

        $holder = [
            'firstName' => $guest['first_name'],
            'lastName' => $guest['last_name'],
            'email' => $guest['email'],
        ];

        $guests = [[
            'occupancyNumber' => 1,
            'firstName' => $guest['first_name'],
            'lastName' => $guest['last_name'],
            'email' => $guest['email'],
        ]];

        $allBooked = true;
        foreach ($trip->stops as $stop) {
            if (!$stop->prebook_id) continue;

            // In sandbox, use prebookId as transactionId
            $transactionId = $stop->transaction_id ?: $stop->prebook_id;

            $bookResult = $this->api->book(
                $stop->prebook_id,
                $transactionId,
                $holder,
                $guests
            );

            if (isset($bookResult['data']['bookingId'])) {
                $stop->update([
                    'booking_id' => $bookResult['data']['bookingId'],
                    'booking_status' => 'booked',
                ]);
            } else {
                $stop->update(['booking_status' => 'failed']);
                $allBooked = false;
            }
        }

        $trip->update(['status' => $allBooked ? 'booked' : 'partial']);

        return view('trip-builder.confirmation', [
            'trip' => $trip->fresh()->load('stops'),
            'guest' => $guest,
            'allBooked' => $allBooked,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\LiteApiClient;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected LiteApiClient $api) {}

    public function home()
    {
        return view('home');
    }

    public function places(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $result = $this->api->searchPlaces($request->q);

        return response()->json($result);
    }

    public function search(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:destination,vibe',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'adults' => 'required|integer|min:1|max:9',
        ]);

        $checkin = $request->checkin;
        $checkout = $request->checkout;
        $adults = (int) $request->adults;

        if ($request->mode === 'destination') {
            $request->validate(['place_id' => 'required|string']);
            $result = $this->api->searchRatesByPlace(
                $request->place_id,
                $checkin,
                $checkout,
                $adults
            );
        } else {
            $request->validate(['vibe' => 'required|string|min:3']);
            $result = $this->api->searchRatesByAi(
                $request->vibe,
                $checkin,
                $checkout,
                $adults
            );
        }

        session([
            'search' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'adults' => $adults,
                'mode' => $request->mode,
            ],
        ]);

        // Normalize hotels into a uniform format
        // hotels[] uses "id", data[] uses "hotelId"
        $hotelInfoMap = [];
        foreach (($result['hotels'] ?? []) as $h) {
            $hotelInfoMap[$h['id'] ?? ''] = $h;
        }

        $hotels = [];
        foreach (($result['data'] ?? []) as $item) {
            $hId = $item['hotelId'] ?? '';
            $info = $hotelInfoMap[$hId] ?? [];

            // Get lowest price from roomTypes
            $lowestPrice = null;
            $currency = 'EUR';
            foreach (($item['roomTypes'] ?? []) as $roomType) {
                $price = $roomType['offerRetailRate']['amount'] ?? null;
                $cur = $roomType['offerRetailRate']['currency'] ?? 'EUR';
                if ($price !== null && ($lowestPrice === null || $price < $lowestPrice)) {
                    $lowestPrice = $price;
                    $currency = $cur;
                }
            }

            $hotels[] = [
                'hotelId' => $hId,
                'name' => $info['name'] ?? $item['name'] ?? 'Hotel',
                'photo' => $info['main_photo'] ?? $item['main_photo'] ?? null,
                'address' => $info['address'] ?? $item['address'] ?? '',
                'rating' => $info['rating'] ?? $item['rating'] ?? null,
                'starRating' => $info['starRating'] ?? $item['starRating'] ?? null,
                'price' => $lowestPrice,
                'currency' => $currency,
            ];
        }

        return view('results', [
            'hotels' => $hotels,
            'mode' => $request->mode,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'adults' => $adults,
            'query' => $request->mode === 'vibe' ? $request->vibe : ($request->place_name ?? 'your destination'),
        ]);
    }
}

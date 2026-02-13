<?php

namespace App\Http\Controllers;

use App\Services\LiteApiClient;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct(protected LiteApiClient $api) {}

    public function show(string $hotelId, Request $request)
    {
        $search = session('search');
        if (!$search) {
            return redirect()->route('home')->with('error', __('Please start a new search.'));
        }

        $checkin = $request->query('checkin', $search['checkin']);
        $checkout = $request->query('checkout', $search['checkout']);
        $adults = (int) $request->query('adults', $search['adults']);

        $hotel = $this->api->getHotel($hotelId);
        $ratesResult = $this->api->searchHotelRates($hotelId, $checkin, $checkout, $adults);

        // Build room lookup from hotel details: rooms[].id => room
        $hotelRooms = [];
        foreach (($hotel['data']['rooms'] ?? []) as $room) {
            $hotelRooms[$room['id']] = $room;
        }

        // Rates structure: data[].roomTypes[].rates[]
        // Group by mappedRoomId (from rates[].mappedRoomId)
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
                    'image' => $roomInfo['photos'][0]['url'] ?? $hotel['data']['main_photo'] ?? null,
                    'description' => $roomInfo['description'] ?? null,
                    'size' => isset($roomInfo['roomSizeSquare']) ? $roomInfo['roomSizeSquare'] . ' ' . ($roomInfo['roomSizeUnit'] ?? 'sqm') : null,
                    'maxOccupancy' => $roomInfo['maxOccupancy'] ?? $rate['maxOccupancy'] ?? null,
                    'bedTypes' => $roomInfo['bedTypes'] ?? [],
                    'amenities' => array_slice(array_column($roomInfo['roomAmenities'] ?? [], 'name'), 0, 6),
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
                'taxesAndFees' => $taxesAndFees,
            ];
        }

        return view('hotel', [
            'hotel' => $hotel['data'] ?? [],
            'groupedRooms' => $groupedRooms,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'adults' => $adults,
            'hotelId' => $hotelId,
        ]);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class LiteApiClient
{
    protected string $apiKey;
    protected string $currency;
    protected string $guestNationality;
    protected string $dataBaseUrl = 'https://api.liteapi.travel/v3.0';
    protected string $bookBaseUrl = 'https://book.liteapi.travel/v3.0';

    public function __construct()
    {
        $this->apiKey = config('services.liteapi.key');
        $this->currency = config('services.liteapi.currency');
        $this->guestNationality = config('services.liteapi.guest_nationality');
    }

    protected function dataClient(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'accept' => 'application/json',
        ])->timeout(30);
    }

    protected function bookClient(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(30);
    }

    public function searchPlaces(string $query): array
    {
        $response = $this->dataClient()
            ->get("{$this->dataBaseUrl}/data/places", [
                'textQuery' => $query,
            ]);

        return $response->json() ?? [];
    }

    public function searchRatesByPlace(
        string $placeId,
        string $checkin,
        string $checkout,
        int $adults = 2
    ): array {
        $response = $this->dataClient()
            ->post("{$this->dataBaseUrl}/hotels/rates", [
                'occupancies' => [['adults' => $adults]],
                'currency' => $this->currency,
                'guestNationality' => $this->guestNationality,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'placeId' => $placeId,
                'roomMapping' => true,
                'maxRatesPerHotel' => 1,
                'includeHotelData' => true,
            ]);

        return $response->json() ?? [];
    }

    public function searchRatesByAi(
        string $aiSearch,
        string $checkin,
        string $checkout,
        int $adults = 2
    ): array {
        $response = $this->dataClient()
            ->post("{$this->dataBaseUrl}/hotels/rates", [
                'occupancies' => [['adults' => $adults]],
                'currency' => $this->currency,
                'guestNationality' => $this->guestNationality,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'maxRatesPerHotel' => 1,
                'roomMapping' => true,
                'aiSearch' => $aiSearch,
                'includeHotelData' => true,
            ]);

        return $response->json() ?? [];
    }

    public function searchHotelRates(
        string $hotelId,
        string $checkin,
        string $checkout,
        int $adults = 2
    ): array {
        $response = $this->dataClient()
            ->post("{$this->dataBaseUrl}/hotels/rates", [
                'hotelIds' => [$hotelId],
                'occupancies' => [['adults' => $adults]],
                'currency' => $this->currency,
                'guestNationality' => $this->guestNationality,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'roomMapping' => true,
                'includeHotelData' => true,
            ]);

        return $response->json() ?? [];
    }

    public function getHotel(string $hotelId): array
    {
        $response = $this->dataClient()
            ->get("{$this->dataBaseUrl}/data/hotel", [
                'hotelId' => $hotelId,
                'timeout' => 4,
            ]);

        return $response->json() ?? [];
    }

    public function prebook(string $offerId): array
    {
        $response = $this->bookClient()
            ->post("{$this->bookBaseUrl}/rates/prebook", [
                'usePaymentSdk' => true,
                'offerId' => $offerId,
            ]);

        return $response->json() ?? [];
    }

    public function book(
        string $prebookId,
        string $transactionId,
        array $holder,
        array $guests
    ): array {
        $response = $this->bookClient()
            ->post("{$this->bookBaseUrl}/rates/book", [
                'prebookId' => $prebookId,
                'holder' => $holder,
                'payment' => [
                    'method' => 'TRANSACTION_ID',
                    'transactionId' => $transactionId,
                ],
                'guests' => $guests,
            ]);

        return $response->json() ?? [];
    }
}

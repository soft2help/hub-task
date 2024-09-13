<?php

namespace App\Connector;

use App\Contract\iHotelLegsAPI;

class HotelLegsConnector implements iHotelLegsAPI
{
    public function search(array $hubRequest): array
    {
        // Normalize HubRequest to HotelLegsRequest
        $providerRequest = $this->normalizeHubRequest($hubRequest);

        //Here we should perform http request to the APi
        $responseLegsApi = $this->mockProviderResponse();

        return $this->normalizeProviderResponse(
            $responseLegsApi
        );
    }

    // This method normalizes the HubRequest to the format expected by HotelLegs
    private function normalizeHubRequest(array $hubRequest): array
    {
        // Calculate the number of nights
        $checkInDate = new \DateTime($hubRequest['checkIn']);
        $checkOutDate = new \DateTime($hubRequest['checkOut']);
        $numberOfNights = $checkInDate->diff($checkOutDate)->days;

        // Map HubRequest to HotelLegsRequest format
        return [
            'hotel' => $hubRequest['hotelId'],
            'checkInDate' => $hubRequest['checkIn'],
            'numberOfNights' => $numberOfNights,
            'guests' => $hubRequest['numberOfGuests'],
            'rooms' => $hubRequest['numberOfRooms'],
            'currency' => $hubRequest['currency'],
        ];
    }


    // This method normalizes the provider's response to the format expected by the Hub
    private function normalizeProviderResponse(array $providerResponse): array
    {
        $rooms = [];

        foreach ($providerResponse['results'] as $result) {
            $roomId = $result['room'];
            $rate = [
                'mealPlanId' => $result['meal'],
                'isCancellable' => $result['canCancel'],
                'price' => $result['price'],
            ];

            // Group rates by roomId
            if (!isset($rooms[$roomId])) {
                $rooms[$roomId] = [
                    'roomId' => $roomId,
                    'rates' => [],
                ];
            }

            $rooms[$roomId]['rates'][] = $rate;
        }

        // Reformat the rooms array to match the HubResponse format
        return array_values($rooms);
    }

    // This method mocks the provider response
    private function mockProviderResponse(): array
    {
        return [
            'results' => [
                ['room' => 1, 'meal' => 1, 'canCancel' => true, 'price' => 123.48],
                ['room' => 1, 'meal' => 1, 'canCancel' => false, 'price' => 150.00],
                ['room' => 2, 'meal' => 1, 'canCancel' => true, 'price' => 148.25],
                ['room' => 2, 'meal' => 2, 'canCancel' => true, 'price' => 165.38],
            ],
        ];
    }
}

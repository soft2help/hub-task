<?php

namespace App\Tests\Connector;

use App\Connector\HotelLegsConnector;
use PHPUnit\Framework\TestCase;

class HotelLegsConnectorTest extends TestCase
{
    private $hotelLegsConnector;

    protected function setUp(): void
    {
        // Initialize the HotelLegsConnector
        $this->hotelLegsConnector = new HotelLegsConnector();
    }

    public function testNormalizeHubRequest(): void
    {
        // Define the Hub request (input)
        $hubRequest = [
            'hotelId' => 1,
            'checkIn' => '2024-01-01',
            'checkOut' => '2024-01-05',
            'numberOfGuests' => 2,
            'numberOfRooms' => 1,
            'currency' => 'EUR',
        ];

        // Use reflection to access the private normalizeHubRequest method
        $reflection = new \ReflectionClass(HotelLegsConnector::class);
        $method = $reflection->getMethod('normalizeHubRequest');
        $method->setAccessible(true);

        // Call the private method with the input and check if the output is correct
        $normalizedRequest = $method->invoke($this->hotelLegsConnector, $hubRequest);

        // Expected HotelLegs request format
        $expectedRequest = [
            'hotel' => 1,
            'checkInDate' => '2024-01-01',
            'numberOfNights' => 4,  // (2024-01-05 - 2024-01-01)
            'guests' => 2,
            'rooms' => 1,
            'currency' => 'EUR',
        ];

        $this->assertSame($expectedRequest, $normalizedRequest);
    }

    public function testNormalizeProviderResponse(): void
    {
        // Define the mock provider response from HotelLegs (input)
        $providerResponse = [
            'results' => [
                ['room' => 1, 'meal' => 1, 'canCancel' => false, 'price' => 123.48],
                ['room' => 1, 'meal' => 1, 'canCancel' => true, 'price' => 150.00],
                ['room' => 2, 'meal' => 1, 'canCancel' => false, 'price' => 148.25],
                ['room' => 2, 'meal' => 2, 'canCancel' => false, 'price' => 165.38],
            ],
        ];

        // Use reflection to access the private normalizeProviderResponse method
        $reflection = new \ReflectionClass(HotelLegsConnector::class);
        $method = $reflection->getMethod('normalizeProviderResponse');
        $method->setAccessible(true);

        // Call the private method with the input and check if the output is correct
        $normalizedResponse = $method->invoke($this->hotelLegsConnector, $providerResponse);

        // Expected normalized Hub response format
        $expectedResponse = [
            [
                'roomId' => 1,
                'rates' => [
                    ['mealPlanId' => 1, 'isCancellable' => false, 'price' => 123.48],
                    ['mealPlanId' => 1, 'isCancellable' => true, 'price' => 150.00],
                ],
            ],
            [
                'roomId' => 2,
                'rates' => [
                    ['mealPlanId' => 1, 'isCancellable' => false, 'price' => 148.25],
                    ['mealPlanId' => 2, 'isCancellable' => false, 'price' => 165.38],
                ],
            ]
        ];

        $this->assertSame($expectedResponse, $normalizedResponse);
    }

    public function testSearch(): void
    {
        // Simulate a Hub request
        $hubRequest = [
            'hotelId' => 1,
            'checkIn' => '2024-01-01',
            'checkOut' => '2024-01-05',
            'numberOfGuests' => 2,
            'numberOfRooms' => 1,
            'currency' => 'EUR',
        ];

        // Call the public search method of HotelLegsConnector
        $result = $this->hotelLegsConnector->search($hubRequest);

        // Expected response from the mocked provider response normalized into the Hub response format
        $expectedResponse = [
            [
                'roomId' => 1,
                'rates' => [
                    ['mealPlanId' => 1, 'isCancellable' => true, 'price' => 123.48],
                    ['mealPlanId' => 1, 'isCancellable' => false, 'price' => 150.00],
                ],
            ],
            [
                'roomId' => 2,
                'rates' => [
                    ['mealPlanId' => 1, 'isCancellable' => true, 'price' => 148.25],
                    ['mealPlanId' => 2, 'isCancellable' => true, 'price' => 165.38],
                ],
            ]
        ];

        $this->assertSame($expectedResponse, $result);
    }
}

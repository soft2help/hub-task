<?php

namespace App\Tests\Service;

use App\Service\HubService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Psr\Cache\CacheItemInterface;
use App\Service\BusinessRulesService;
use App\Repository\SettingRepository;

class HubServiceTest extends TestCase
{
    private $cacheMock;
    private $connectorMock;
    private $businessRulesServiceMock;

    protected function setUp(): void
    {
        // Mock the CacheInterface
        $this->cacheMock = $this->createMock(CacheInterface::class);

        // Mock a connector (assuming connectors implement an interface, e.g., iHotelLegsAPI)
        $this->connectorMock = $this->createMock(\App\Contract\iHotelLegsAPI::class);
    }

    public function testSearchWithCacheHit(): void
    {

        // Configure cache mock to return a hit (cached response)
        $this->cacheMock->expects($this->once())
            ->method('get')
            ->willReturn([
                [
                    'roomId' => 'CACHED_ID',
                    'rates' => [
                        [
                            'mealPlanId' => 'CACHED_PLAN',
                            'isCancellable' => true,
                            'price' => 10000.5
                        ]
                    ]
                ]
            ]);

        $businessRulesService = new BusinessRulesService($this->createMock(SettingRepository::class));
        // Create HubService with a single connector
        $hubService = new HubService([$this->connectorMock], $this->cacheMock, $businessRulesService);

        // Simulate a search request
        $result = $hubService->search(['hotelId' => 1]);

        // Assert that the cached data is returned
        $this->assertSame(['rooms' => [
            [
                'roomId' => 'CACHED_ID',
                'rates' => [
                    [
                        'mealPlanId' => 'CACHED_PLAN',
                        'isCancellable' => true,
                        'price' => 10100.51
                    ]
                ]
            ]
        ]], $result);
    }
}

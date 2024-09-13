<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\CacheItem;

class HubService
{
    private iterable $connectors;
    private CacheInterface $cache;
    private BusinessRulesService $businessRulesService;
    public function __construct(iterable $connectors, CacheInterface $cache, BusinessRulesService $businessRulesService)
    {
        $this->connectors = $connectors;
        $this->cache = $cache;
        $this->businessRulesService = $businessRulesService;
    }

    public function search(array $hubRequest): array
    {

        $responses = [];
        foreach ($this->connectors as $connector) {
            // Generate a unique cache key for the connector and request
            $cacheKey = $this->generateCacheKey($hubRequest, get_class($connector));


            // Retrieve cached response or execute the search if not cached
            $response = $this->cache->get($cacheKey, function (CacheItem $item) use ($connector, $hubRequest) {
                // Set the cache expiration time, e.g., 1 minute
                // We should also think in another cache invalidation strategy
                $item->expiresAfter(60);

                // Perform the search if the cache doesn't exist
                return $connector->search($hubRequest);
            });

            $responses[] = $this->businessRulesService->apply($response);
        }
        return $this->aggregateResponses($responses);
    }


    private function generateCacheKey(array $hubRequest, string $connectorIdentifier): string
    {
        // Create a hash of the request parameters and connector identifier
        return md5(json_encode($hubRequest) . $connectorIdentifier);
    }

    private function aggregateResponses(array $responses): array
    {
        // Logic to aggregate the responses from multiple providers

        // maybe here we should put another logic because maybe they return the same roomId from different connector
        return ['rooms' => array_merge(...$responses)];
    }
}

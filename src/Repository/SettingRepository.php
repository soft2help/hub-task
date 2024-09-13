<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    private CacheInterface $cache;
    private const CACHE_TTL = 600; // Cache for 10 minutes
    const PREFIX_SETTINGS = 'settings_prefix_';
    
    public function __construct(ManagerRegistry $registry, CacheInterface $cache)
    {
        parent::__construct($registry, Setting::class);
        $this->cache = $cache;
    }

    /**
     * Get the value of a setting by category and key.
     */
    public function get(string $category, string $key): ?string
    {
        try {
            $settings = $this->cache->get(self::PREFIX_SETTINGS . $category, function (ItemInterface $item) use ($category) {
                $item->expiresAfter(self::CACHE_TTL);
                return $this->fetchSettingsByCategory($category);
            });

            return $settings[$key] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set or update a setting.
     */
    public function set(string $category, string $key, string $value): void
    {
        $entityManager = $this->getEntityManager();

        $setting = $this->findOneBy(['category' => $category, 'key' => $key]) ?? new Setting();
        $setting->setCategory($category);
        $setting->setKey($key);
        $setting->setValue($value);

        $entityManager->persist($setting);
        $entityManager->flush();

        // Invalidate cache after saving
        $this->cache->delete(self::PREFIX_SETTINGS . $category);
    }

    /**
     * Fetch all settings by category and cache the result.
     */
    private function fetchSettingsByCategory(string $category): array
    {
        $settings = $this->findBy(['category' => $category]);

        // Convert to key-value array
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting->getKey()] = $setting->getValue();
        }

        return $settingsArray;
    }
}

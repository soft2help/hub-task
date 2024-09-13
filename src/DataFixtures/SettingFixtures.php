<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Define your settings data
        $settingsData = [
            ['category' => 'general', 'key' => 'site_name', 'value' => 'My Website'],
            ['category' => 'general', 'key' => 'admin_email', 'value' => 'admin@example.com'],
            ['category' => 'hub_business_rules', 'key' => 'remove_non_cancellable', 'value' => 'true'],
            ['category' => 'hub_business_rules', 'key' => 'profit_default_fee_percentage', 'value' => '5.0']           
        ];

        // Loop through settings and persist them
        foreach ($settingsData as $data) {
            $setting = new Setting();
            $setting->setCategory($data['category']);
            $setting->setKey($data['key']);
            $setting->setValue($data['value']);
            $manager->persist($setting);
        }

        // Flush to save the settings in the database
        $manager->flush();
    }
}
